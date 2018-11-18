<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Page;

use GuzzleHttp\Psr7\Uri;
use Thingston\Extractor\AbstractExtractor;

/**
 * Page link canonical extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class LinkCanonicalExtractor extends AbstractExtractor
{

    /**
     * Extract page link canonical.
     *
     * @return string|null
     */
    public function extract()
    {
        /* @var $link \DOMElement */
        foreach ($this->dom->filter('link') as $link) {
            if (false === $link->hasAttribute('rel') || 'canonical' !== strtolower($link->getAttribute('rel'))) {
                continue;
            }

            if (true === $link->hasAttribute('href')) {
                try {
                    return (string) new Uri($this->decode($link->getAttribute('href')));
                } catch (Exception $ex) {
                    // ignore
                }
            }
        }

        return null;
    }
}
