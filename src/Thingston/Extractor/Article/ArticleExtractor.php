<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Article;

use Thingston\Extractor\AbstractExtractorAggregator;
use Thingston\Extractor\Page\UriExtractor;

/**
 * Article extractor.
 *
 * @method string extractUri() Extract page URI
 * @method string|null extractPublisher() Extract article publisher
 * @method array|string|null extractAuthor() Extract article author(s)
 * @method DateTime|null extractDatePublished() Extract article date published
 * @method DateTime|null extractDateModified() Extract article date modified
 * @method string extractTitle() Extract article title
 * @method string extractSummary() Extract article summary
 * @method string extractImage() Extract article image
 * @method string|null extractText() Extract article text
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class ArticleExtractor extends AbstractExtractorAggregator
{

    /**
     * @var array
     */
    public static $extractors = [
        'uri' => UriExtractor::class,
        'publisher' => PublisherExtractor::class,
        'author' => AuthorExtractor::class,
        'published' => DatePublishedExtractor::class,
        'modified' => DateModifiedExtractor::class,
        'title' => TitleExtractor::class,
        'summary' => SummaryExtractor::class,
        'image' => ImageExtractor::class,
        'text' => TextExtractor::class,
    ];
}
