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
 * Page meta open graph extractor.
 *
 * @open graph Pedro Ferreira <pedro@thingston.com>
 */
class MetaOpenGraphExtractor extends AbstractExtractor
{

    /**
     * Extract page meta open graph.
     *
     * @return array|null
     */
    public function extract()
    {
        $openGraph = [];
        $namespaces = ['og', 'fb', 'facebook', 'article', 'book', 'profile', 'website', 'music', 'video'];

        /* @var $meta \DOMElement */
        foreach ($this->dom->filter('meta') as $meta) {
            if (false === $meta->hasAttribute('property')) {
                continue;
            }

            $property = null;

            foreach ($namespaces as $namespace) {
                if ($namespace . ':' === substr($meta->getAttribute('property'), 0, strlen($namespace) + 1)) {
                    $property = $meta->getAttribute('property');
                    break;
                }
            }

            if (null === $property) {
                continue;
            }

            if (true === $meta->hasAttribute('content')) {
                $content = $this->decode($meta->getAttribute('content'));

                if (false === isset($openGraph[$property])) {
                    $openGraph[$property] = $content;
                } elseif (false === is_array($openGraph[$property])) {
                    $openGraph[$property] = [$openGraph[$property], $content];
                } else {
                    $openGraph[$property][] = $content;
                }
            }
        }

        return $openGraph;
    }
}
