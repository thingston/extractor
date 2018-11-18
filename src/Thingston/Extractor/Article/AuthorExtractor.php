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
 * Author extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class AuthorExtractor extends AbstractExtractor
{

    /**
     * Extract article author.
     *
     * @return array|string|null
     */
    public function extract()
    {
        if (null !== $author = $this->fromJsonLd()) {
            return $author;
        }

        if (null !== $author = $this->fromOpenGraph()) {
            return $author;
        }

        if (null !== $author = $this->fromMeta()) {
            return $author;
        }

        return $this->fromMeta();
    }

    /**
     * Extract author from JSON-LD description.
     *
     * @return string|null
     */
    public function fromJsonLd(): ?string
    {
        foreach ($this->getPageExtractor()->extractJsonLd() as $jsonLd) {
            if (true === isset($jsonLd['author']['name'])) {
                return $jsonLd['author']['name'];
            }
        }

        return null;
    }

    /**
     * Extract author from OG description.
     *
     * @return string|null
     */
    public function fromOpenGraph(): ?string
    {
        $og = $this->getPageExtractor()->extractOpenGraph();

        if (true === isset($og['article:author']) && false === strpos($og['article:author'], '://')) {
            return $og['article:author'];
        }

        return null;
    }

    /**
     * Extract author from Twitter description.
     *
     * @return string|null
     */
    public function fromMeta(): ?string
    {
        return $this->getPageExtractor()->extractAuthor();
    }
}
