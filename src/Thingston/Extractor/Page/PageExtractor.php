<?php

/**
 * Thingston Extractor
 *
 * @link https://github.com/thingston/extractor Public Git repository
 * @copyright (c) 2018, Pedro Ferreira <https://thingston.com>
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Thingston\Extractor\Page;

use Thingston\Extractor\AbstractExtractorAggregator;

/**
 * Page extractor.
 *
 * @method string extractUri() Extract page URI
 * @method string extractTitle() Extract page title
 * @method string extractDescription() Extract page description
 * @method array extractKeywords() Extract page keywords
 * @method array extractAuthor() Extract page author(s)
 * @method array extractPublsiher() Extract page publisher(s)
 * @method array extractOrganization() Extract page organization(s)
 * @method string extractLanguage() Extract page language
 * @method string extractCharset() Extract page charset
 * @method string extractCanonical() Extract canonical URI
 * @method array extractViewport() Extract meta viewport values
 * @method array extractHttpEquiv() Extract meta http-equiv headers
 * @method array extractMetaTags() Extract all METE tags
 * @method array extractLinkTags() Extract all LINK tags
 * @method array extractAnchorTags() Extract all A tags
 * @method array extractImageTags() Extract all IMG tags
 * @method array extractJsonLd() Extract JSON-LD data
 * @method array extractOpenGraph() Extract Open Graph data
 * @method array extractTwitter() Extract Twitter data
 * @method string extractFavicon() Extract favicon URI
 * @method array extractIcons() Extract all icon URIs
 *
 * @author Pedro Ferreira <pedro@thingston.com>
 */
class PageExtractor extends AbstractExtractorAggregator
{

    /**
     * @var array
     */
    public static $extractors = [
        'uri' => UriExtractor::class,
        'canonical' => LinkCanonicalExtractor::class,
        'title' => TitleExtractor::class,
        'description' => MetaDescriptionExtractor::class,
        'keywords' => MetaKeywordsExtractor::class,
        'author' => MetaAuthorExtractor::class,
        'publisher' => MetaPublisherExtractor::class,
        'organization' => MetaOrganizationExtractor::class,
        'language' => LanguageExtractor::class,
        'charset' => MetaCharsetExtractor::class,
        'viewport' => MetaViewportExtractor::class,
        'httpEquiv' => MetaHttpEquivExtractor::class,
        'metas' => MetaTagsExtractor::class,
        'links' => LinkTagsExtractor::class,
        'figures' => FigureTagsExtractor::class,
        'images' => ImageTagsExtractor::class,
        'anchors' => AnchorTagsExtractor::class,
        'jsonLd' => JsonLdExtractor::class,
        'openGraph' => MetaOpenGraphExtractor::class,
        'twitter' => MetaTwitterExtractor::class,
        'favicon' => LinkFaviconExtractor::class,
        'icons' => LinkIconsExtractor::class,
    ];
}
