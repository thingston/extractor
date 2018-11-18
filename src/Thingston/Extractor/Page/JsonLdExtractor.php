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
 * Page JSON-LD extractor.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class JsonLdExtractor extends AbstractExtractor
{

    /**
     * Extract page JSON-LD
     *
     * @return array
     */
    public function extract()
    {
        $data = [];

        foreach ($this->dom->filter('script[type="application/ld+json"]') as $script) {
            $json = trim(trim($script->textContent, PHP_EOL));

            if (null === $record = json_decode($json, true)) {
                // @hack
                $json = str_replace(": '", ': "', $json);
                $json = str_replace("',\n", '",' . PHP_EOL, $json);

                if (null === $record = json_decode($json, true)) {
                    continue;
                }
            }

            $data[] = $record;
        }

        return $data;
    }
}
