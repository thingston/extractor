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
 * Page meta author extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class MetaAuthorExtractor extends AbstractExtractor
{

    /**
     * Extract page meta author.
     *
     * @return array|null
     */
    public function extract()
    {
        $author = [];

        /* @var $meta \DOMElement */
        foreach ($this->dom->filter('meta') as $meta) {
            if (false === $meta->hasAttribute('name') || 'author' !== strtolower($meta->getAttribute('name'))) {
                continue;
            }

            if (true === $meta->hasAttribute('content')) {
                $author[] = trim(html_entity_decode($meta->getAttribute('content'), ENT_QUOTES | ENT_HTML5));
            }
        }

        return false === empty($author) ? $author : null;
    }
}
