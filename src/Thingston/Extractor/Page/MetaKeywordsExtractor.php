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
 * Page meta keywords extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class MetaKeywordsExtractor extends AbstractExtractor
{

    /**
     * Extract page meta keywords.
     *
     * @return array|null
     */
    public function extract()
    {
        /* @var $meta \DOMElement */
        foreach ($this->dom->filter('meta') as $meta) {
            if (false === $meta->hasAttribute('name') || 'keywords' !== strtolower($meta->getAttribute('name'))) {
                continue;
            }

            if (true === $meta->hasAttribute('content')) {
                return explode(',', $this->decode($meta->getAttribute('content')));
            }

            return [];
        }

        return null;
    }
}
