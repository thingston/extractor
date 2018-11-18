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
 * Page figure tags extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class FigureTagsExtractor extends AbstractExtractor
{

    /**
     * Extract page FIGURE tags.
     *
     * @return array
     */
    public function extract()
    {
        $figures = [];

        /* @var $figure \DOMElement */
        foreach ($this->dom->filter('figure') as $figure) {
            $img = $figure->getElementsByTagName('img');

            if (false === isset($img[0])) {
                continue;
            }

            $tag = [
                'attributes' => $this->getAttributes($figure),
                'image' => $this->getAttributes($img[0]),
            ];

            $caption = $figure->getElementsByTagName('figcaption');

            if (true === isset($caption[0])) {
                $tag['caption'] = $this->getAttributes($caption[0]);
            }

            $figures[] = $tag;
        }

        return $figures;
    }
}
