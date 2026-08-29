<?php

namespace League\ColorExtractor\PixelIterator\Png;

enum ColorType: int
{
    case GREYSCALE = 0;
    case TRUECOLOR = 2;
    case INDEXED_COLOR = 3;
    case GREYSCALE_WITH_ALPHA = 4;
    case TRUECOLOR_WITH_ALPHA = 6;
}
