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
 * Summary extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class SummaryExtractor extends AbstractExtractor
{

    /**
     * Extract article summary.
     *
     * @return string
     */
    public function extract()
    {
        if (null !== $summary = $this->fromJsonLd()) {
            return $summary;
        }

        if (null !== $summary = $this->fromOpenGraph()) {
            return $summary;
        }

        if (null !== $summary = $this->fromTwitter()) {
            return $summary;
        }

        return $this->fromMeta();
    }

    /**
     * Extract summary from JSON-LD description.
     *
     * @return string|null
     */
    public function fromJsonLd(): ?string
    {
        foreach ($this->getPageExtractor()->extractJsonLd() as $jsonLd) {
            if (true === isset($jsonLd['description'])) {
                return $jsonLd['description'];
            }
        }

        return null;
    }

    /**
     * Extract summary from OG description.
     *
     * @return string|null
     */
    public function fromOpenGraph(): ?string
    {
        $og = $this->getPageExtractor()->extractOpenGraph();

        return $og['og:description'] ?? null;
    }

    /**
     * Extract summary from Twitter description.
     *
     * @return string|null
     */
    public function fromTwitter(): ?string
    {
        $twitter = $this->getPageExtractor()->extractTwitter();

        return $twitter['twitter:description'] ?? null;
    }

    /**
     * Extract summary from META tag description.
     *
     * @return string|null
     */
    public function fromMeta(): ?string
    {
        return $this->getPageExtractor()->extractDescription();
    }
}
