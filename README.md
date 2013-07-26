ColorExtractor
==============

Extract colors from an image like a human would do.

Usage
-----
Include `ColorExtractor` class as you want and call its `extract` method:

```php
<?php

include 'path/to/ColorExtractor.php'

$palette = ColorExtractor::extract(/* … */);
```

Parameters
----------

<table>
    <thead>
        <tr>
            <th>parameter</th>
            <th>description</th>
            <th>default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>imageResource</td>
            <td>image resource identifier, as returned by [imagecreatefromjpeg](http://www.php.net/manual/en/function.imagecreatefromjpeg.php)</td>
            <td>-</td>
        </tr>
        <tr>
            <td>maxPaletteSize</td>
            <td>maximum size of the colors array returned by ColorExtractor::extract</td>
            <td>1</td>
        </tr>
        <tr>
            <td>minColorRatio</td>
            <td>minimum ratio below colors are ignored (0 - 1)</td>
            <td>0</td>
        </tr>
        <tr>
            <td>minSaturation</td>
            <td>minimum saturation level below colors are ignored (0 - 1)</td>
            <td>0</td>
        </tr>
    </tbody>
</table>

Return
------
Returns an array with hexadecimal codes of dominant colors.