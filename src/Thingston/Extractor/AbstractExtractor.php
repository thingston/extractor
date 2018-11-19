<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor;

use DOMElement;
use Exception;
use ForceUTF8\Encoding;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Thingston\Extractor\Article\ArticleExtractor;
use Thingston\Extractor\Page\PageExtractor;

/**
 * Abstract extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
abstract class AbstractExtractor
{

    const TEXT_FOLDER = __DIR__ . '/../../../text';

    /**
     * @var DomCrawler
     */
    protected $dom;

    /**
     * @var PageExtractor
     */
    private $pageExtractor;

    /**
     * @var ArticleExtractor
     */
    private $articleExtractor;

    /**
     * @var array
     */
    private $stopWords;

    /**
     * @var array
     */
    private $blockTags = ['address', 'article', 'aside', 'blockquote', 'canvas', 'dd', 'div', 'dl', 'dt', 'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hr', 'li', 'main', 'nav', 'noscript', 'ol', 'output', 'p', 'pre', 'section', 'table', 'tfoot', 'ul', 'video'];

    /**
     * @var array
     */
    private $inlineTags = ['a', 'abbr', 'acronym', 'b', 'bdo', 'big', 'br', 'button', 'cite', 'code', 'dfn', 'em', 'i', 'img', 'input', 'kbd', 'label', 'map', 'object', 'q', 'samp', 'script', 'select', 'small', 'span', 'strong', 'sub', 'sup', 'textarea', 'time', 'tt', 'var'];

    /**
     * Create new instance.
     *
     * @param DomCrawler $dom
     */
    public function __construct(DomCrawler $dom)
    {
        $this->dom = $dom;
    }

    /**
     * Create new instance from HTML.
     *
     * @param string $html
     * @param string|null $uri
     * @return AbstractExtractor
     */
    public static function create(string $html, string $uri = null): AbstractExtractor
    {
        return new static(new DomCrawler(Encoding::toUTF8($html), $uri));
    }

    /**
     * Extract the required subject.
     *
     * @return array|string|null
     */
    abstract public function extract();

    /**
     * Decode HTML special characters into plain text.
     *
     * @param string $value
     * @return string
     */
    protected function decode(string $value): string
    {
        return trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5));
    }

    /**
     * Try to resolve a relative URI against base.
     *
     * @param string $href
     * @return string
     */
    protected function resolve(string $href): string
    {
        if (null !== $base = $this->dom->getUri()) {
            try {
                $hrefUri = new Uri($href);
                $baseUri = new Uri($base);
            } catch (Exception $e) {
                return $href;
            }

            $href = (string) UriResolver::resolve($baseUri, $hrefUri);
        }

        return $href;
    }

    protected function getAttributes(\DOMNode $node): array
    {
        $attributes = [];

        foreach ($node->attributes as $attribute) {
            $name = $attribute->nodeName;
            $value = $attribute->nodeValue;

            if (true === in_array($name, ['href', 'data-href', 'src', 'data-src'])) {
                $value = $this->resolve($value);
            }

            $attributes[$name] = $value;
        }

        if ('' !== $text = $this->decode($node->textContent)) {
            $attributes['_'] = $text;
        }

        return $attributes;
    }

    /**
     * Get page extractor.
     *
     * @return PageExtractor
     */
    public function getPageExtractor(): PageExtractor
    {
        if (null === $this->pageExtractor) {
            $this->pageExtractor = new PageExtractor($this->dom);
        }

        return $this->pageExtractor;
    }

    /**
     * Get article extractor.
     *
     * @return ArticleExtractor
     */
    public function getArticleExtractor(): ArticleExtractor
    {
        if (null === $this->articleExtractor) {
            $this->articleExtractor = new ArticleExtractor($this->dom);
        }

        return $this->articleExtractor;
    }

    /**
     * Get inner HTML of any DOM element.
     *
     * @param DOMElement $element
     * @return string
     */
    public function innerHTML(DOMElement $element): string
    {
        $doc = $element->ownerDocument;
        $html = '';

        foreach ($element->childNodes as $child) {
            $html .= $doc->saveHTML($child);
        }

        return trim($html);
    }

    /**
     * Get stop-words list for the current language.
     *
     * @return array
     */
    public function getStopwords(): array
    {
        $language = $this->getPageExtractor()->extractLanguage() ?? 'en';
        $path = sprintf('%s/stopwords-%s.txt', self::TEXT_FOLDER, $language);

        return explode(PHP_EOL, file_get_contents($path));
    }

    /**
     * Get links density.
     *
     * @param DOMElement $node
     * @return float
     */
    public function getLinksDensity(DOMElement $node): float
    {
        $links = $this->getChildsByName($node, 'a');

        if (0 === count($links)) {
            return 0;
        }

        if (0 === $textLength = strlen(trim($node->textContent))) {
            return 1;
        }

        $linksLength = 0;

        foreach ($links as $link) {
            $linksLength += strlen(trim($link->textContent));
        }

        return $linksLength / $textLength;
    }

    /**
     * Get child elements of a given name.
     *
     * @param DOMElement $node
     * @param string $name
     * @param array $childs
     * @return array
     */
    public function getChildsByName(DOMElement $node, string $name, array $childs = []): array
    {
        foreach ($node->childNodes as $child) {
            if (false === $child instanceof DOMElement) {
                continue;
            }

            if ($name === $child->nodeName) {
                $childs[] = $child;
            }

            $childs = $this->getChildsByName($child, $name, $childs);
        }

        return $childs;
    }

    /**
     * Get block tags density.
     *
     * @param DOMElement $node
     * @return float
     */
    public function getBlockDensity(DOMElement $node): float
    {
        if (false === $node->hasChildNodes()) {
            return 0;
        }

        if (0 === $textLength = strlen(trim($node->textContent))) {
            return 0;
        }

        $blocksLength = 0;

        foreach ($node->childNodes as $child) {
            if (true === $child instanceof DOMElement && true === in_array($child->nodeName, $this->blockTags)) {
                $blocksLength += strlen(trim($child->textContent));
            }
        }

        return $blocksLength / $textLength;
    }
}
