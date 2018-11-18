<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Page;

use Thingston\Extractor\AbstractExtractor;

/**
 * Page link icons extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class LinkIconsExtractor extends AbstractExtractor
{

    /**
     * Extract page links icon.
     *
     * @return array
     */
    public function extract()
    {
        $icons = [];
        $rels = ['icon', 'shortcut icon', 'apple-touch-icon', 'apple-touch-icon-precomposed', 'apple-touch-startup-image'];

        /* @var $link \DOMElement */
        foreach ($this->dom->filter('link') as $link) {
            if (false === $link->hasAttribute('rel') || false === in_array(strtolower($link->getAttribute('rel')), $rels)) {
                continue;
            }

            if (true === $link->hasAttribute('href')) {
                $icons[$this->resolve(trim($link->getAttribute('href')))] = true;
            }
        }

        return array_keys($icons);
    }
}
