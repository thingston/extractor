Thingston Extractor
===================

Collection of PHP classes to extract data from HTML pages.

Requirements
------------

Thingston Extractor requires:

-   [PHP 7.1](https://secure.php.net/releases/7_1_0.php) or above.

Instalation
-----------

Add Thingston Extractor to any PHP project using [Composer](https://getcomposer.org/):

```bash
composer require thingston/extractor
```

Getting Started
---------------

```php
use Thingston\Extractor\Page;

$uri = 'https://google.com/';
$html = file_get_contents($uri);

$page = Page\PageExtractor::create($html, $uri)->extract();
```

Contributors
------------

Open Source is made of contribuition. If you want to contribute to Thingston please
follow these steps:

1.  Fork latest version into your own repository.
2.  Write your changes or additions and commit them.
3.  Follow PSR-2 coding style standard.
4.  Make sure you have unit tests with full coverage to your changes.
5.  Go to Github Pull Requests at [https://github.com/thingston/extractor/pulls](https://github.com/thingston/extractor/pulls)
    and create a new request.

Thank you!

Changes and Versioning
----------------------

All relevant changes on this code are logged in a separated [log](CHANGELOG.md) file.

Version numbers follow recommendations from [Semantic Versioning](http://semver.org/).

License
-------

Thingston code is maintained under [The MIT License](https://opensource.org/licenses/MIT).