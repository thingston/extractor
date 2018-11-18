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
 * Date published extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class DatePublishedExtractor extends AbstractExtractor
{

    /**
     * Extract article date published.
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

        if (null !== $date = $this->fromUri()) {
            return $date;
        }

        return null;
    }

    /**
     * Extract published date from JSON-LD.
     *
     * @return DateTime|null
     */
    public function fromJsonLd(): ?DateTime
    {
        foreach ($this->getPageExtractor()->extractJsonLd() as $jsonLd) {
            if (true === isset($jsonLd['datePublished'])) {
                try {
                    return new DateTime($jsonLd['datePublished']);
                } catch (Exception $e) {
                    // continue
                }
            }

            if (true === isset($jsonLd['dateCreated'])) {
                try {
                    return new DateTime($jsonLd['dateCreated']);
                } catch (Exception $e) {
                    // continue
                }
            }
        }

        return null;
    }

    /**
     * Extract published date from meta tags.
     *
     * @return DateTime|null
     */
    public function fromMeta(): ?DateTime
    {
        $tags = [
            ['attribute' => 'class', 'value' => 'date', 'content' => 'data-datetime'],
            ['attribute' => 'property', 'value' => 'rnews:datePublished', 'content' => 'content'],
            ['attribute' => 'property', 'value' => 'article:published_time', 'content' => 'content'],
            ['attribute' => 'name', 'value' => 'OriginalPublicationDate', 'content' => 'content'],
            ['attribute' => 'itemprop', 'value' => 'datePublished', 'content' => 'datetime'],
            ['attribute' => 'property', 'value' => 'og:published_time', 'content' => 'content'],
            ['attribute' => 'name', 'value' => 'article_date_original', 'content' => 'content'],
            ['attribute' => 'name', 'value' => 'publication_date', 'content' => 'content'],
            ['attribute' => 'name', 'value' => 'sailthru.date', 'content' => 'content'],
            ['attribute' => 'name', 'value' => 'PublishDate', 'content' => 'content'],
            ['attribute' => 'pubdate', 'value' => 'pubdate', 'content' => 'datetime'],
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
     * Extract published date from OG.
     *
     * @return DateTime|null
     */
    public function fromOpenGraph(): ?DateTime
    {
        $og = $this->getPageExtractor()->extractOpenGraph();
        $properties = ['article:published_time', 'og:published_time'];

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

    /**
     * Extract date published from URI.
     *
     * @return DateTime|null
     */
    public function fromUri(): ?DateTime
    {
        if (null === $uri = $this->getArticleExtractor()->extractUri()) {
            return null;
        }

        $matches = [];

        if (1 !== preg_match('#([12]\d{3}/(0[1-9]|1[0-2])/(0[1-9]|[12]\d|3[01]))#', $uri, $matches)) {
            return null;
        }

        try {
            return new DateTime($matches[0]);
        } catch (Exception $e) {
            return null;
        }

        return null;
    }
}
