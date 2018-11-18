<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Page;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Thingston\Extractor\AbstractExtractor;

/**
 * Page URI extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class UriExtractor extends AbstractExtractor
{

    /**
     * Extract page URI.
     *
     * @return array
     */
    public function extract()
    {
        if (null !== $uri = $this->fromCanonical()) {
            return $uri;
        }

        if (null !== $uri = $this->fromOpenGraph()) {
            return $uri;
        }

        if (null !== $uri = $this->dom->getUri()) {
            try {
                return (string) new Uri($uri);
            } catch (Exception $e) {
                // ignore exception and carry on to next strategy
            }
        }

        return null;
    }

    /**
     * Extract page URI from canonical meta tag.
     *
     * @return string|null
     */
    public function fromCanonical(): ?string
    {
        return $this->getPageExtractor()->extractCanonical();
    }

    /**
     * Extract page URI from OG.
     *
     * @return string|null
     */
    public function fromOpenGraph(): ?string
    {
        $og = $this->getPageExtractor()->extractOpenGraph();

        if (true === isset($og['og:url'])) {
            try {
                return (string) new Uri($og['og:url']);
            } catch (Exception $e) {
                return null;
            }
        }

        return null;
    }
}
