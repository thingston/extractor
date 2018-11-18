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
 * Page image tags extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class ImageTagsExtractor extends AbstractExtractor
{

    /**
     * Extract page IMG tags.
     *
     * @return array
     */
    public function extract()
    {
        $images = [];

        /* @var $img \DOMElement */
        foreach ($this->dom->filter('img') as $img) {
            if (false === $img->hasAttributes()) {
                continue;
            }

            $images[] = $this->getAttributes($img);
        }

        return $images;
    }
}
