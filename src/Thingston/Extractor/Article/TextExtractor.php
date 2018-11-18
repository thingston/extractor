<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Article;

use DOMElement;
use DOMNode;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Thingston\Extractor\AbstractExtractor;

/**
 * Text extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class TextExtractor extends AbstractExtractor
{

    /**
     * @var array
     */
    protected $nodeOrder;

    /**
     * @var array
     */
    protected $stopWords;

    /**
     * Extract article text.
     *
     * @return string
     */
    public function extract()
    {
        $text = [];

        foreach ($this->findClusteredText() as $el) {
            /* @var $node \DOMElement */
            $node = $el['node'];
            $text[] = trim(str_replace(PHP_EOL, ' ', $node->textContent));
        }

        return implode(PHP_EOL, $text);
    }

    /**
     * Get order array of remaining nodes.
     *
     * @return array
     */
    protected function getNodeOrder(): array
    {
        if (null === $this->nodeOrder) {
            $this->nodeOrder = [];

            $dom = $this->cleanupDom();
            $nodes = $dom->filter(' body');

            if (0 < $nodes->count()) {
                $this->nodeOrder = $this->traverseNodes($nodes->getNode(0));
            }
        }

        return $this->nodeOrder;
    }

    /**
     * Print nodes to screen.
     *
     * @return void
     */
    protected function printTree()
    {
        foreach ($this->getNodeOrder() as $node) {
            $level = $node['level'];
            $node = $node['node'];

            echo str_repeat(' ', $level * 2) . $node->nodeName;
            if (true === $node instanceof DOMElement) {
                if ($node->hasAttribute('class')) {
                    echo ' (' . $node->getAttribute('class') . ')';
                }
            }
            echo PHP_EOL;
        }
    }

    /**
     * Traverse nodes by level and return an ordered array of them.
     *
     * @param DOMNode $node
     * @param int $level
     * @param array $order
     * @return array
     */
    protected function traverseNodes(DOMNode $node, int $level = 0, array $order = []): array
    {
        if (false === $node instanceof DOMElement) {
            return $order;
        }

        $order[] = [
            'node' => $node,
            'level' => $level,
        ];

        if (null === $children = $node->childNodes) {
            return $order;
        }

        for ($i = 0; $i < $children->length; $i++) {
            $order = $this->traverseNodes($node->childNodes->item($i), $level + 1, $order);
        }

        return $order;
    }

    /**
     * Find clusters of closed text.
     *
     * @return array
     */
    protected function findClusters(): array
    {
        $this->getNodeOrder();

        $clusters = [];
        $cluster = [];

        $h1Position = $this->getH1Position();
        $prevPosition = null;
        $prevText = null;

        foreach ($this->nodeOrder as $i => $el) {
            /* @var $node \DOMNode */
            $node = $el['node'];

            if (false === $node instanceof DOMElement) {
                continue;
            }

            if ('p' !== $node->nodeName) {
                continue;
            }

            if ('' === trim($node->textContent)) {
                continue;
            }

            if (null !== $h1Position && $i <= $h1Position) {
                continue;
            }

            $stopwords = $this->findStopWords($node);

            if (2 > count($stopwords)) {
                continue;
            }

            if (0.66 < $this->getLinksDensity($node)) {
                continue;
            }

            if (0.66 < $this->getBlockDensity($node)) {
                continue;
            }

            if (null !== $prevText) {
                $similar = 0;
                similar_text($prevText, trim($node->textContent), $similar);

                if (95 < $similar) {
                    continue;
                }
            }

            $distance = null === $prevPosition ? 0 : $i - $prevPosition;

            $prevPosition = $i;
            $prevText = trim($node->textContent);

            $el['position'] = $i;
            $el['distance'] = $distance;
            $el['stopwords'] = count($stopwords);
            //$el['words'] = count($this->findNodeWords($node));

            if (3 < $distance) {
                $clusters[] = $cluster;
                $cluster = [];
            }

            $cluster[] = $el;
        }

        $clusters[] = $cluster;

        return $clusters;
    }

    /**
     * Find clustered text.
     *
     * @return array
     */
    protected function findClusteredText(): array
    {
        $clusters = $this->findClusters();
        $totalClusters = count($clusters);

        if (0 === $totalClusters) {
            return [];
        }

        if (1 === $totalClusters) {
            return $clusters[0];
        }

        $totalNodes = count($this->nodeOrder);
        $acceptableDistance = round($totalNodes * .10);

        if (1 === count($clusters[0]) && $clusters[1][0]['distance'] > $acceptableDistance) {
            $clusters[1][0]['distance'] = $acceptableDistance;
        }

        $clustered = [];

        foreach ($clusters as $i => $cluster) {
            foreach ($cluster as $el) {
                $clustered[] = $el;
            }

            $j = $i + 1;

            if ($j === $totalClusters) {
                break;
            }

            if ($clusters[$j][0]['distance'] > $acceptableDistance) {
                break;
            }
        }

        return $clustered;
    }

    /**
     * Get node text.
     *
     * @param DOMNode $node
     * @return string
     */
    protected function getNodeText(DOMNode $node): string
    {
        if ($node instanceof DOMElement) {
            return trim($node->textContent);
        } elseif ($node instanceof \DOMText) {
            return trim($node->nodeValue);
        }

        return '';
    }

    /**
     * Find all words in node text.
     *
     * @param DOMNode $node
     * @return array
     */
    protected function findNodeWords(DOMNode $node): array
    {
        $text = $this->getNodeText($node);
        $words = [];

        foreach ($this->splitText($this->removePunctuation($text)) as $word) {
            $word = trim($word);

            if ('' === $word) {
                continue;
            }

            $words[] = $word;
        }

        return $words;
    }

    /**
     * Find stop words contained on a node text.
     *
     * @param DOMNode $node
     * @return array
     */
    protected function findStopWords(DOMNode $node): array
    {
        $stopWords = $this->getStopwords();
        $words = $this->findNodeWords($node);

        $found = [];

        foreach ($words as $word) {
            if (true === in_array(strtolower($word), $stopWords)) {
                $found[] = $word;
            }
        }

        return $found;
    }

    /**
     * Remove punction from a string of text.
     *
     * @param string $text
     * @return string
     */
    protected function removePunctuation(string $text): string
    {
        return preg_replace("/[[:punct:]]+/", '', $text);
    }

    /**
     * Get page language.
     *
     * @return string
     */
    protected function getLanguage(): string
    {
        return $this->getPageExtractor()->extractLanguage();
    }

    /**
     * Split text into an array of words.
     *
     * @param string $text
     * @return array
     */
    protected function splitText(string $text): array
    {
        if ('ja' === $this->getLanguage()) {
            $regex = '/(' . implode('|', array_map('preg_quote', $this->getStopwords())) . ')/';
            $text = preg_replace($regex, ' $1 ', $text);
        }

        return explode(' ', $text);
    }

    /**
     * Clone original DOM object and remove unwanted nodes.
     *
     * @return DomCrawler
     */
    protected function cleanupDom(): DomCrawler
    {
        $dom = clone $this->dom;

        /* @var $el \DOMElement */

        $selectors = ['head', 'meta', 'link', 'style', 'script', 'noscript', 'nav', 'aside', 'footer', 'svg', 'g', 'path', 'figure', 'img', 'li p'];

        foreach ($selectors as $selector) {
            $nodes = $dom->filter($selector)->getIterator();

            for ($i = $nodes->count() - 1; $i >= 0; $i--) {
                $node = $nodes[$i];
                $node->parentNode->removeChild($node);
            }
        }

        return $dom;
    }

    /**
     * Get first H1 position.
     *
     * @return int|null
     */
    protected function getH1Position(): ?int
    {
        $this->getNodeOrder();

        for ($i = 0; $i < count($this->nodeOrder); $i++) {
            if ('h1' === $this->nodeOrder[$i]['node']->nodeName) {
                return $i;
            }
        }

        return null;
    }

    /**
     * Get max text density over nodes.
     *
     * @return int
     */
    protected function getMaxWordDensity(): int
    {
        $maxWordDensity = 0;

        foreach ($this->getNodeOrder() as $node) {
            if ($node['wordDensity'] > $maxWordDensity) {
                $maxWordDensity = $node['wordDensity'];
            }
        }

        return $maxWordDensity;
    }

    /**
     * Get stop words list.
     *
     * @return array
     */
    public function getStopwords(): array
    {
        if (null === $this->stopWords) {
            $this->stopWords = parent::getStopwords();
        }

        return $this->stopWords;
    }
}
