ColorExtractor
==============

[![Build Status](https://travis-ci.org/php-loep/color-extractor.png?branch=master)](https://travis-ci.org/php-loep/color-extractor)
[![Total Downloads](https://poser.pugx.org/league/color-extractor/downloads.png)](https://packagist.org/packages/league/color-extractor)
[![Latest Stable Version](https://poser.pugx.org/league/color-extractor/v/stable.png)](https://packagist.org/packages/league/color-extractor)

Extract colors from an image like a human would do.

## Install

Via Composer

```json
{
    "require": {
        "league/color-extractor": "~0.1"
    }
}
```

## Usage

```php
require 'vendor/autoload.php';

use League\ColorExtractor\Client as ColorExtractor;

$client = new ColorExtractor;

$image = $client->loadPng('./some/image.png');

// Get the most used color hex code
$palette = $image->extract();

// Get three most used color hex code
$palette = $image->extract(3);

// Change the Minimum Color Ratio (0 - 1)
// Default: 0
$image->setMinColorRatio(1);
$palette = $image->extract();

```

## Service Providers

- Silex

First register `ColorExtractorServiceProvider` in your application:
```php
use League\ColorExtractor\Silex\ColorExtractorServiceProvider;

// ... create $app
$app->register(new ColorExtractorServiceProvider);
```

Then you can use like this:

```php
$image = $app['colorextractor']->loadPng('./some/image.png');
...
$palette = $image->extract();
```

- Laravel4: coming soon...


## Contributing

Please see [CONTRIBUTING](https://github.com/php-loep/color-extractor/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Mathieu Lechat](https://github.com/MatTheCat)
- [All Contributors](https://github.com/php-loep/color-extractor/contributors)


## License

The WTFPL License (WTFPL). Please see [License File](https://github.com/php-loep/color-extractor/blob/master/LICENSE) for more information.