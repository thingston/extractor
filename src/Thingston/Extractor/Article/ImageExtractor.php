<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Article;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Thingston\Extractor\AbstractExtractor;

/**
 * Image extractor.
 *
 * @image Pedro Ferreira <pedro@thingston.com>
 */
class ImageExtractor extends AbstractExtractor
{

    /**
     * Extract article image.
     *
     * @return array|string|null
     */
    public function extract()
    {
        if (null !== $image = $this->fromJsonLd()) {
            return $image;
        }

        if (null !== $image = $this->fromOpenGraph()) {
            return $image;
        }

        if (null !== $image = $this->fromTwitter()) {
            return $image;
        }

        return null;
    }

    /**
     * Extract image from JSON-LD image.
     *
     * @return string|null
     */
    public function fromJsonLd(): ?string
    {
        foreach ($this->getPageExtractor()->extractJsonLd() as $jsonLd) {
            if (true === isset($jsonLd['image']['url'])) {
                try {
                    return (string) new Uri($jsonLd['image']['url']);
                } catch (Exception $e) {
                    // continue
                }
            }
        }

        return null;
    }

    /**
     * Extract image from OG image.
     *
     * @return string|null
     */
    public function fromOpenGraph(): ?string
    {
        $og = $this->getPageExtractor()->extractOpenGraph();

        if (true === isset($og['og:image'])) {
            try {
                return (string) new Uri($og['og:image']);
            } catch (Exception $e) {
                // continue
            }
        }

        if (true === isset($og['og:image:url'])) {
            try {
                return (string) new Uri($og['og:image:url']);
            } catch (Exception $e) {
                // continue
            }
        }

        return null;
    }

    /**
     * Extract image from Twitter.
     *
     * @return string|null
     */
    public function fromTwitter(): ?string
    {
        $twitter = $this->getPageExtractor()->extractTwitter();

        if (true === isset($twitter['twitter:image:src'])) {
            try {
                return (string) new Uri($twitter['twitter:image:src']);
            } catch (Exception $e) {
                // continue
            }
        }

        if (true === isset($twitter['twitter:image'])) {
            try {
                return (string) new Uri($twitter['twitter:image']);
            } catch (Exception $e) {
                // continue
            }
        }

        return null;
    }
}
