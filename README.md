ColorExtractor
==============

[![Total Downloads](https://poser.pugx.org/league/colorextractor/downloads.png)](https://packagist.org/packages/league/colorextractor)
[![Latest Stable Version](https://poser.pugx.org/league/colorextractor/v/stable.png)](https://packagist.org/packages/league/colorextractor)

Extract colors from an image like a human would do.

## Install

Via Composer

    {
        "require": {
            "league/color-extractor": ">=0.1"
        }
    }

## Usage

```php
include 'vendor/autoload.php';

use League\ColorExtractor;

$palette = ColorExtractor::extract(array(
    
    // Image resource identifier, as returned by imagecreatefromjpeg()
    'imageResource' => 'foo',

    // Maximum size of the colors array returned by ColorExtractor::extract
    // Default: 1
    'maxPaletteSize' => 3,

    // Minimum ratio below colors are ignored (0 - 1)
    // Default 0
    'minColorRatio' => 0.5,
));

// Returns an array with hexadecimal codes of dominant colors.
var_dump($palatte);
```

## TODO

- ~~Full Unit Test Coverage~~
- ~~Exception Handlers~~
- Extensive Documentation
- Silex/Laravel Service Providers


## Contributing

Please see [CONTRIBUTING](https://github.com/php-loep/color-extractor/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Mathieu Lechat](https://github.com/MatTheCat)
- [All Contributors](https://github.com/php-loep/color-extractor/contributors)


## License

The WTFPL License (WTFPL). Please see [License File](https://github.com/php-loep/color-extractor/blob/master/LICENSE) for more information.