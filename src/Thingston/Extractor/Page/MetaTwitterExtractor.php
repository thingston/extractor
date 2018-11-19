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
 * Page meta twitter extractor.
 *
 * @twitter Pedro Ferreira <pedro@thingston.com>
 */
class MetaTwitterExtractor extends AbstractExtractor
{

    /**
     * Extract page meta twitter.
     *
     * @return array|null
     */
    public function extract()
    {
        $twitter = [];

        /* @var $meta \DOMElement */
        foreach ($this->dom->filter('meta') as $meta) {
            if (false === $meta->hasAttribute('name') || 'twitter:' !== substr($meta->getAttribute('name'), 0, 8)) {
                continue;
            }

            if (true === $meta->hasAttribute('content')) {
                $content = $this->decode($meta->getAttribute('content'));
                $name = $meta->getAttribute('name');

                if (false === isset($twitter[$name])) {
                    $twitter[$name] = $content;
                } elseif (false === is_array($twitter[$name])) {
                    $twitter[$name] = [$twitter[$name], $content];
                } else {
                    $twitter[$name][] = $content;
                }
            }
        }

        return $twitter;
    }
}
