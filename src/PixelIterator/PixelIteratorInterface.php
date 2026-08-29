<?php

namespace League\ColorExtractor\PixelIterator;

/**
 * Yield each of an image’s pixels’ color in the form of a 64-bits integer with
 * four 16-bits samples: red, green, blue and alpha (opacity).
 *
 * @phpstan-extends \IteratorAggregate<int>
 */
interface PixelIteratorInterface extends \IteratorAggregate
{
}
