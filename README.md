ColorExtractor
==============

[![Build Status](https://travis-ci.org/thephpleague/color-extractor.png?branch=master)](https://travis-ci.org/thephpleague/color-extractor)
[![Total Downloads](https://poser.pugx.org/league/color-extractor/downloads.png)](https://packagist.org/packages/league/color-extractor)
[![Latest Stable Version](https://poser.pugx.org/league/color-extractor/v/stable.png)](https://packagist.org/packages/league/color-extractor)

Extract colors from an image like a human would do.

## Install

Via Composer

``` bash
$ composer require league/color-extractor:0.2.*
```

## Usage

```php
require 'vendor/autoload.php';

use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

$palette = Palette::fromFilename('./some/image.png');

// $palette is an iterator on colors sorted by pixel count
foreach($palette as $color => $count) {
    // colors are represented by integers
    echo Color::fromIntToHex($color), ': ', $count, "\n";
}

// it offers some helpers too
$topFive = $palette->getMostUsedColors(5);

$colorCount = count($palette);

$blackCount = $palette->getColorCount(Color::fromHexToInt('#000000'));


// an extractor is built from a palette
$extractor = new ColorExtractor($palette);

// it defines an extract method which return the most “representative” colors
$colors = $extractor->extract(5);

```

## Contributing

Please see [CONTRIBUTING](https://github.com/thephpleague/color-extractor/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Mathieu Lechat](https://github.com/MatTheCat)
- [All Contributors](https://github.com/thephpleague/color-extractor/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/thephpleague/color-extractor/blob/master/LICENSE) for more information.
