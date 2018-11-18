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
 * Page anchor tags extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class AnchorTagsExtractor extends AbstractExtractor
{

    /**
     * Extract page A tags.
     *
     * @return array
     */
    public function extract()
    {
        $anchors = [];

        /* @var $a \DOMElement */
        foreach ($this->dom->filter('a') as $a) {
            if (false === $a->hasAttributes()) {
                continue;
            }

            $anchors[] = $this->getAttributes($a);
        }

        return $anchors;
    }
}
