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
     * Extract article text.
     *
     * @return string
     */
    public function extract()
    {
        $dom = $this->cleanupDom();

        $nodes = $this->getNodeCandidates($dom);

        //dump($dom->html());
        exit;
    }

    /**
     * Get DOM node candidates.
     *
     * @param DomCrawler $dom
     * @return array
     */
    protected function getNodeCandidates(DomCrawler $dom): array
    {
        $candidates = [];

        foreach ($dom->filter('p') as $el) {
            $text = preg_replace("/[[:punct:]]+/", '', $el->textContent);

            $candidateWords = $this->getCandidateWords($text);
            $stopWords = $this->getStopwords();
            $overlapWords = [];

            foreach ($candidateWords as $word) {
                $word = mb_strtolower($word);

                if (true === in_array($word, $stopWords)) {
                    $overlapWords[] = $word;
                }
            }

            if (count($overlapWords) > 2 && false === $this->isHighLinkDensity($el)) {
                $candidates[] = $el;
                dump($el->textContent);
            }
        }

        return $candidates;
    }

    /**
     * Cleanup DOM object.
     *
     * @return DomCrawler
     */
    protected function cleanupDom(): DomCrawler
    {
        $dom = clone $this->dom;

        /* @var $el \DOMElement */

        $selectors = ['head', 'body style', 'body script', 'nav', 'aside', 'footer'];

        foreach ($selectors as $selector) {
            foreach ($dom->filter($selector) as $el) {
                $el->parentNode->removeChild($el);
            }
        }

        return $dom;
    }

    /**
     * Get list of candidate words.
     *
     * @param string $text
     * @return array
     */
    protected function getCandidateWords(string $text): array
    {
        $language = $this->getPageExtractor()->extractLanguage();

        if ('ja' == $language) {
            $regexp = '/(' . implode('|', array_map('preg_quote', $this->getStopwords())) . ')/';
            $text = preg_replace($regexp, ' $1 ', $text);
        }

        $words = [];

        foreach (explode(' ', $text) as $word) {
            if (true === empty($word)) {
                continue;
            }

            $words[] = $word;
        }

        return $words;
    }

    protected function isHighLinkDensity(DOMElement $node, float $limit = 1.0): bool
    {
        //@todo
        //$links = $node->find('a, [onclick]');

        $links = $node->getElementsByTagName('a');

        if (0 === $links->length) {
            return false;
        }

        $words = preg_split('/[\s]+/iu', $node->textContent, -1, PREG_SPLIT_NO_EMPTY);

        if (false === is_array($words) || true === empty($words)) {
            return false;
        }

        $sb = [];

        foreach ($links as $link) {
            $sb[] = trim(preg_replace('/[\n\r\s\t]+/', ' ', $link->textContent));
        }

        $linkWords = explode(' ', implode('', $sb));

        $divisor = count($linkWords) / count($words);
        $score = $divisor * $links->length;

        return $score >= $limit;
    }
}
