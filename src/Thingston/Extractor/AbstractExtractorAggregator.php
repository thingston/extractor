<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor;

use RuntimeException;
use Thingston\Extractor\AbstractExtractor;

/**
 * Abstract extractor aggregator.
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
abstract class AbstractExtractorAggregator extends AbstractExtractor
{

    /**
     * @var array
     */
    public static $extractors = [];

    /**
     * @var array
     */
    public $data = [];

    /**
     * Extract page language.
     *
     * @return array
     */
    public function extract()
    {
        foreach (static::$extractors as $key => $extractor) {
            if (false === array_key_exists($key, $this->data)) {
                $this->data[$key] = (new $extractor($this->dom))->extract();
            }
        }

        return $this->data;
    }

    /**
     * Magic method caller.
     *
     * @param string $name
     * @param array $arguments
     * @return array|string|null
     * @throws RuntimeException
     */
    public function __call($name, $arguments)
    {
        if ('extract' === substr($name, 0, 7)) {
            $key = lcfirst(substr($name, 7));

            if (true === isset(static::$extractors[$key])) {
                if (true === array_key_exists($key, $this->data)) {
                    return $this->data[$key];
                }

                $extractor = static::$extractors[$key];
                $this->data[$key] = (new $extractor($this->dom))->extract();

                return $this->data[$key];
            }
        }

        throw new RuntimeException('Invalid method name: ' . $name);
    }
}
