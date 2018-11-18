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
 * Page link favicon extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class LinkFaviconExtractor extends AbstractExtractor
{

    /**
     * Extract page link favicon.
     *
     * @return string
     */
    public function extract()
    {
        /* @var $link \DOMElement */
        foreach ($this->dom->filter('link') as $link) {
            if (false === $link->hasAttribute('rel') || false === in_array(strtolower($link->getAttribute('rel')), ['icon', 'shortcut icon'])) {
                continue;
            }

            if (true === $link->hasAttribute('href')) {
                return $this->resolve(trim($link->getAttribute('href')));
            }
        }

        return $this->resolve('/favicon.ico');
    }
}
