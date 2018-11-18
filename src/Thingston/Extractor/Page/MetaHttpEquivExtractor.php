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
 * Page meta http-equiv extractor.
 *
 * @http-equiv Pedro Ferreira <pedro@thingston.com>
 */
class MetaHttpEquivExtractor extends AbstractExtractor
{

    /**
     * Extract page meta http-equiv.
     *
     * @return array|null
     */
    public function extract()
    {
        $httpEquiv = [];

        /* @var $meta \DOMElement */
        foreach ($this->dom->filter('meta') as $meta) {
            if (false === $meta->hasAttribute('http-equiv')) {
                continue;
            }

            if (true === $meta->hasAttribute('content')) {
                $name = $meta->getAttribute('http-equiv');
                $httpEquiv[$name][] = trim(html_entity_decode($meta->getAttribute('content'), ENT_QUOTES | ENT_HTML5));
            }

            return $httpEquiv;
        }

        return null;
    }
}
