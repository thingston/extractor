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
 * Page meta charset extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class MetaCharsetExtractor extends AbstractExtractor
{

    /**
     * Extract page meta charset.
     *
     * @return string|null
     */
    public function extract()
    {
        /* @var $meta \DOMElement */
        foreach ($this->dom->filter('meta') as $meta) {
            if (false === $meta->hasAttribute('charset')) {
                continue;
            }

            return $this->decode($meta->getAttribute('charset'));
        }

        $node = $this->dom->getNode(0);
        $html = $node->ownerDocument->saveHTML($node);

        return strtolower(mb_detect_encoding($html));
    }
}
