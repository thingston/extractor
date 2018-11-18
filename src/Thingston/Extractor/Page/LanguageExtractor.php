<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Page;

use LanguageDetection\Language;
use Thingston\Extractor\AbstractExtractor;

/**
 * Page language extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class LanguageExtractor extends AbstractExtractor
{

    /**
     * Extract page language.
     *
     * @return string|null
     */
    public function extract()
    {
        /* @var $html \DOMElement */
        foreach ($this->dom->filter('html') as $html) {
            if (false === $html->hasAttribute('lang')) {
                continue;
            }

            $language = strtolower(substr($this->decode($html->getAttribute('lang')), 0, 2));

            return $language;
        }

        /* @var $body \DOMElement */
        if (null === $body = $this->dom->filter('body')->getIterator()->current()) {
            return null;
        }

        $html = $body->ownerDocument->saveHTML($body);
        $detector = new Language();

        return (string) $detector->detect(strip_tags($html));
    }
}
