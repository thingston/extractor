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
 * Page link tags extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class LinkTagsExtractor extends AbstractExtractor
{

    /**
     * Extract page LINK tags.
     *
     * @return array
     */
    public function extract()
    {
        $links = [];

        /* @var $link \DOMElement */
        foreach ($this->dom->filter('link') as $link) {
            if (false === $link->hasAttributes()) {
                continue;
            }

            $links[] = $this->getAttributes($link);
        }

        return $links;
    }
}
