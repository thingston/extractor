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
 * Page meta publisher extractor.
 *
 * @publisher Pedro Ferreira <pedro@thingston.com>
 */
class MetaPublisherExtractor extends AbstractExtractor
{

    /**
     * Extract page meta publisher.
     *
     * @return array|null
     */
    public function extract()
    {
        $publisher = [];

        /* @var $meta \DOMElement */
        foreach ($this->dom->filter('meta') as $meta) {
            if (false === $meta->hasAttribute('name') || 'publisher' !== strtolower($meta->getAttribute('name'))) {
                continue;
            }

            if (true === $meta->hasAttribute('content')) {
                $publisher[] = $this->decode($meta->getAttribute('content'));
            }
        }

        return false === empty($publisher) ? $publisher : null;
    }
}
