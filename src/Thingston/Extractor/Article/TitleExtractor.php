<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Article;

use Thingston\Extractor\AbstractExtractor;

/**
 * Title extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class TitleExtractor extends AbstractExtractor
{

    const SEPARATORS = '|-:·•';
    const HARD_SEPARATORS = '|·•';

    /**
     * Extract article title.
     *
     * @return string
     */
    public function extract()
    {
        if (null !== $title = $this->fromJsonLd()) {
            return $this->cleanupTitle($this->compareWithH1($title));
        }

        if (null !== $title = $this->fromOpenGraph()) {
            return $this->cleanupTitle($this->compareWithH1($title));
        }

        if (null !== $title = $this->fromTwitter()) {
            return $this->cleanupTitle($this->compareWithH1($title));
        }

        return $this->cleanupTitle($this->getPageExtractor()->extractTitle());
    }

    /**
     * Extract title from JSON-LD.
     *
     * @return string|null
     */
    public function fromJsonLd(): ?string
    {
        foreach ($this->getPageExtractor()->extractJsonLd() as $jsonLd) {
            if (true === isset($jsonLd['headline'])) {
                return $jsonLd['headline'];
            }
        }

        return null;
    }

    /**
     * Extract the content of first H! element.
     *
     * @return string|null
     */
    public function fromH1(): ?string
    {
        foreach ($this->dom->filter('h1') as $h1) {
            return $this->decode($h1->textContent);
        }

        return null;
    }

    /**
     * Extract title from OG.
     *
     * @return string|null
     */
    public function fromOpenGraph(): ?string
    {
        $og = $this->getPageExtractor()->extractOpenGraph();

        return $og['og:title'] ?? null;
    }

    /**
     * Extract title from Twitter.
     *
     * @return string|null
     */
    public function fromTwitter(): ?string
    {
        $twitter = $this->getPageExtractor()->extractTwitter();

        return $twitter['twitter:title'] ?? null;
    }

    /**
     * Compare found title with the content of first H1 and return best.
     *
     * @param string $title
     * @return string
     */
    public function compareWithH1(string $title): string
    {
        if (null === $h1 = $this->fromH1()) {
            return $title;
        }

        $percent = 0;
        similar_text($title, $h1, $percent);

        if (0.75 < $percent) {
            return $h1;
        }

        return $title;
    }

    /**
     * Cleanup title from publisher signature.
     *
     * @param string $title
     * @return string
     */
    public function cleanupTitle(string $title): string
    {
        $title = $this->removePublisher($title);
        $title = $this->removeHardSeparators($title);

        return $title;
    }

    /**
     * Remove publisher name.
     *
     * @param string $title
     * @return string
     */
    public function removePublisher(string $title): string
    {
        if (null === $publisher = $this->getArticleExtractor()->extractPublisher()) {
            return $title;
        }

        if (false === $pos = stripos($title, $publisher)) {
            return $title;
        }

        $length = strlen($publisher);
        $balance = round(100 * $pos / $length);

        if (15 > $balance) {
            $clean = substr($title, $length);
        } elseif (75 < $balance) {
            $clean = substr($title, 0, -1 * $length);
        } else {
            return $title;
        }

        $clean = trim(trim(trim($clean), self::SEPARATORS));

        if ('' === $clean) {
            return $title;
        }

        return $clean;
    }

    /**
     * Remove text after/before a balanced hard separator.
     *
     * @param string $title
     * @return string
     */
    public function removeHardSeparators(string $title): string
    {
        $length = strlen($title);

        for ($i = 0; $i < strlen(self::HARD_SEPARATORS); $i++) {
            $separator = substr(self::HARD_SEPARATORS, $i, 1);

            if (false !== $pos = strpos($title, $separator)) {
                $balance = round(100 * $pos / $length);

                if (15 > $balance) {
                    $title = trim(substr($title, $pos));
                } elseif (75 < $balance) {
                    $title = trim(substr($title, 0, $pos));
                }

                break;
            }
        }

        return $title;
    }
}
