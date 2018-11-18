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
 * Page meta tags extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class MetaTagsExtractor extends AbstractExtractor
{

    /**
     * Extract page META tags.
     *
     * @return array
     */
    public function extract()
    {
        $metas = [];

        /* @var $meta \DOMElement */
        foreach ($this->dom->filter('meta') as $meta) {
            if (false === $meta->hasAttributes()) {
                continue;
            }

            $metas[] = $this->getAttributes($meta);
        }

        return $metas;
    }
}
