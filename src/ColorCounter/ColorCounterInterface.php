<?php

namespace League\ColorExtractor\ColorCounter;

use League\ColorExtractor\Color;

interface ColorCounterInterface
{
    /**
     * @return array<int, positive-int>
     */
    public function countAll(): array;
}
