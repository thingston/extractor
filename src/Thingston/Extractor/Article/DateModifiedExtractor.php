<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Article;

use DateTime;
use Exception;
use Thingston\Extractor\AbstractExtractor;

/**
 * Date modified extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class DateModifiedExtractor extends AbstractExtractor
{

    /**
     * Extract article date modified.
     *
     * @return string
     */
    public function extract()
    {
        if (null !== $date = $this->fromJsonLd()) {
            return $date;
        }

        if (null !== $date = $this->fromMeta()) {
            return $date;
        }

        if (null !== $date = $this->fromOpenGraph()) {
            return $date;
        }

        return null;
    }

    /**
     * Extract modified date from JSON-LD.
     *
     * @return DateTime|null
     */
    public function fromJsonLd(): ?DateTime
    {
        foreach ($this->getPageExtractor()->extractJsonLd() as $jsonLd) {
            if (true === isset($jsonLd['dateModified'])) {
                try {
                    return new DateTime($jsonLd['dateModified']);
                } catch (Exception $e) {
                    // continue
                }
            }
        }

        return null;
    }

    /**
     * Extract modified date from meta tags.
     *
     * @return DateTime|null
     */
    public function fromMeta(): ?DateTime
    {
        $tags = [
            ['attribute' => 'property', 'value' => 'article:modified_time', 'content' => 'content'],
            ['attribute' => 'itemprop', 'value' => 'dateModified', 'content' => 'datetime'],
            ['attribute' => 'property', 'value' => 'og:modified_time', 'content' => 'content'],
        ];

        foreach ($tags as $selector) {
            $filter = sprintf('[%s="%s"]', $selector['attribute'], $selector['value']);

            foreach ($this->dom->filter($filter) as $tag) {
                if (true === $tag->hasAttribute($selector['content'])) {
                    try {
                        $content = $this->decode($tag->getAttribute($selector['content']));

                        return new DateTime($content);
                    } catch (Exception $e) {
                        // continue
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract modified date from OG.
     *
     * @return DateTime|null
     */
    public function fromOpenGraph(): ?DateTime
    {
        $og = $this->getPageExtractor()->extractOpenGraph();
        $properties = ['article:modified_time', 'og:modified_time'];

        foreach ($properties as $property) {
            if (true === isset($og[$property])) {
                try {
                    return new DateTime($og[$property]);
                } catch (Exception $e) {
                    continue;
                }
            }
        }

        return null;
    }
}
