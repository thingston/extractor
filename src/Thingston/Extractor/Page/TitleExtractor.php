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
 * Page title extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class TitleExtractor extends AbstractExtractor
{

    /**
     * Extract page title.
     *
     * @return string|null
     */
    public function extract()
    {
        /* @var $title \DOMElement */
        foreach ($this->dom->filter('title') as $title) {
            return $this->decode($title->textContent);
        }

        return null;
    }
}
