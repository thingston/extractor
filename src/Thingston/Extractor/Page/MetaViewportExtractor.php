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
 * Page meta viewport extractor.
 *
 * @viewport Pedro Ferreira <pedro@thingston.com>
 */
class MetaViewportExtractor extends AbstractExtractor
{

    /**
     * Extract page meta viewport.
     *
     * @return array|null
     */
    public function extract()
    {
        $viewport = [];

        /* @var $meta \DOMElement */
        foreach ($this->dom->filter('meta') as $meta) {
            if (false === $meta->hasAttribute('name') || 'viewport' !== strtolower($meta->getAttribute('name'))) {
                continue;
            }

            if (true === $meta->hasAttribute('content')) {
                foreach (explode(',', $meta->getAttribute('content')) as $attr) {
                    $parts = explode('=', $attr, 2);
                    $name = trim($parts[0]);
                    $value = isset($parts[1]) ? trim($parts[1]) : '';
                    $viewport[$name] = $value;
                }
            }

            return $viewport;
        }

        return null;
    }
}
