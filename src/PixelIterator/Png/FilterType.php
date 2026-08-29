<?php

namespace League\ColorExtractor\PixelIterator\Png;

enum FilterType: string
{
    case NONE = "\x0";
    case SUB = "\x1";
    case UP = "\x2";
    case AVERAGE = "\x3";
    case PAETH = "\x4";
}
