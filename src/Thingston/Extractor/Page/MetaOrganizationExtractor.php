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
 * Page meta organization extractor.
 *
 * @organization Pedro Ferreira <pedro@thingston.com>
 */
class MetaOrganizationExtractor extends AbstractExtractor
{

    /**
     * Extract page meta organization.
     *
     * @return array|null
     */
    public function extract()
    {
        $organization = [];

        /* @var $meta \DOMElement */
        foreach ($this->dom->filter('meta') as $meta) {
            if (false === $meta->hasAttribute('name') || 'organization' !== strtolower($meta->getAttribute('name'))) {
                continue;
            }

            if (true === $meta->hasAttribute('content')) {
                $organization[] = $this->decode($meta->getAttribute('content'));
            }
        }

        return false === empty($organization) ? $organization : null;
    }
}
