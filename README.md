ColorExtractor
==============

[![Build Status](https://travis-ci.org/thephpleague/color-extractor.png?branch=master)](https://travis-ci.org/thephpleague/color-extractor)
[![Total Downloads](https://poser.pugx.org/league/color-extractor/downloads.png)](https://packagist.org/packages/league/color-extractor)
[![Latest Stable Version](https://poser.pugx.org/league/color-extractor/v/stable.png)](https://packagist.org/packages/league/color-extractor)

Extract colors from an image like a human would do.

## Install

Via Composer

``` bash
$ composer require league/color-extractor:0.1.*
```

Requires an installation of php that includes GD support. See [PHP.net documentation](http://php.net/manual/en/image.installation.php) for installation procedures. 

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

Integration with most frameworks would require a bridge package, but for Silex and Laravel 4 a 
simple service provider will suffice. 

### Silex

First register `ColorExtractorServiceProvider` in your application:
```php
use League\ColorExtractor\Silex\ColorExtractorServiceProvider;

// ... create $app
$app->register(new ColorExtractorServiceProvider);
```

Then you can use like this:

```php
$image = $app['color-extractor']->loadPng('./some/image.png');
...
$palette = $image->extract();
```

### Laravel 4

Find the `providers` key in `app/config/app.php` and register the `ColorExtractorServiceProvider`:

```php
'providers' => array(
    // ...
    'League\ColorExtractor\Laravel\ColorExtractorServiceProvider',
)
```

Then you can use it exactly the same way as the Silex service provider.
If you prefer to use Facades, find the `aliases` key in `app/config/app.php` and register the `ColorExtractorFacade`:

```php
'aliases' => array(
    // ...
    'ColorExtractor' => 'League\ColorExtractor\Laravel\ColorExtractorFacade',
)
```

Example:

```php
$image = ColorExtractor::loadPng('./some/image.png');
...
$palette = $image->extract();
```


## Contributing

Please see [CONTRIBUTING](https://github.com/thephpleague/color-extractor/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Mathieu Lechat](https://github.com/MatTheCat)
- [All Contributors](https://github.com/thephpleague/color-extractor/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/thephpleague/color-extractor/blob/master/LICENSE) for more information.
