<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Article;

use Purl\Url;
use Thingston\Extractor\AbstractExtractor;

/**
 * Publisher extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class PublisherExtractor extends AbstractExtractor
{

    /**
     * Extract article publisher.
     *
     * @return string
     */
    public function extract()
    {
        if (null !== $publisher = $this->fromJsonLd()) {
            return $publisher;
        }

        if (null !== $publisher = $this->fromOpenGraph()) {
            return $publisher;
        }

        if (null !== $publisher = $this->fromMeta()) {
            return $publisher;
        }

        return $this->fromUri();
    }

    /**
     * Extract publisher from JSON-LD.
     *
     * @return string|null
     */
    public function fromJsonLd(): ?string
    {
        foreach ($this->getPageExtractor()->extractJsonLd() as $jsonLd) {
            if (true === isset($jsonLd['publisher']['name'])) {
                return $jsonLd['publisher']['name'];
            }
        }

        return null;
    }

    /**
     * Extract publisher from OG.
     *
     * @return string|null
     */
    public function fromOpenGraph(): ?string
    {
        $og = $this->getPageExtractor()->extractOpenGraph();

        return $og['og:site_name'] ?? null;
    }

    /**
     * Extract publisher from page META tags.
     *
     * @return string|null
     */
    public function fromMeta(): ?string
    {
        $publishers = $this->getPageExtractor()->extractPublisher();

        return false === empty($publishers) ? $publishers[0] : null;
    }

    /**
     * Return domain name as publisher.
     *
     * @return string|null
     */
    public function fromUri(): ?string
    {
        if (null === $uri = $this->dom->getUri()) {
            return null;
        }

        return (new Url($uri))->registerableDomain;
    }
}
