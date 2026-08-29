<?php

declare(strict_types=1);

namespace League\ColorExtractor\PixelIterator;

use League\ColorExtractor\PixelIterator\Png\ColorType;

class StandalonePngIterator implements PixelIteratorInterface
{
    private const int SHIFT = \PHP_INT_SIZE * 8 - 1;

    /** @var positive-int */
    private int $width;

    private ColorType $colorType;

    /** @var 1|2|4|8|16 */
    private int $bitDepth;

    public function __construct(private readonly $stream)
    {
        if (!\is_resource($stream) || 'stream' !== get_resource_type($stream)) {
            throw new \InvalidArgumentException(sprintf('Expected "$stream" to be a stream but got a "%s".', \gettype($stream)));
        }

        if ("\x89\x50\x4E\x47\x0D\x0A\x1A\x0A" !== fread($this->stream, 8)) {
            throw new \DomainException('Stream is not a PNG one.');
        }

        if ("\x0\x0\x0\xDIHDR" !== fread($this->stream, 8)) {
            throw new \RuntimeException('Did not find valid "IHDR" chunk');
        }

        $this->width = unpack('N', fread($this->stream, 4))[1];

        fseek($this->stream, 4, \SEEK_CUR); // skip height

        $bitDepth = \ord(fread($this->stream, 1));

        $this->colorType = ColorType::tryFrom(\ord(fread($this->stream, 1))) ?? throw new \RuntimeException('Unknown color type.');

        if (!\in_array($bitDepth, match ($this->colorType) {
            ColorType::GREYSCALE => [1, 2, 4, 8, 16],
            ColorType::INDEXED_COLOR => [1, 2, 4, 8],
            default => [8, 16],
        }, true)) {
            throw new \RuntimeException(sprintf('Invalid bit depth "%d" for color type "%d".', $bitDepth, $this->colorType->value));
        }
        $this->bitDepth = $bitDepth;

        if ("\x0\x0" !== fread($this->stream, 2)) {
            throw new \RuntimeException('Unknown compression and/or filter method.');
        }

        fseek($this->stream, 5, \SEEK_CUR); // skip interlace method and checksum
    }

    public function getIterator(): \Traversable
    {
        $palette = [];
        $channelCount = match ($this->colorType) {
            ColorType::GREYSCALE_WITH_ALPHA => 2,
            ColorType::TRUECOLOR => 3,
            ColorType::TRUECOLOR_WITH_ALPHA => 4,
            default => 1,
        };
        $bitsPerPixel = $this->bitDepth * $channelCount;
        $bytesPerPixel = $bitsPerPixel >> 3;
        $scanlineLength = (int) ceil($this->width * $bitsPerPixel / 8);

        $filterPixelOffset = ($bitsPerPixel >> $channelCount) ?: 1;

        $scanlineIndex = null;
        $inflateContext = inflate_init(\ZLIB_ENCODING_DEFLATE);

        do {
            $chunk = unpack('Nlength/a4type', fread($this->stream, 8));

            switch (true) {
                case 'PLTE' === $chunk['type'] && ColorType::INDEXED_COLOR === $this->colorType:
                    if ($palette) {
                        throw new \RuntimeException('PLTE chunk must only appear once.');
                    }

                    foreach (str_split(fread($this->stream, $chunk['length']), 3) as $color) {
                        $palette[] = array_values(unpack('C3', $color));
                    }

                    break;

                case 'IDAT' === $chunk['type']:
                    while ($chunk['length']) {
                        do {
                            $bytes = inflate_add($inflateContext, fread($this->stream, $length = 16 > $chunk['length'] ? $chunk['length'] : 16));
                            $chunk['length'] -= $length;
                        } while ('' === $bytes && $chunk['length']);

                        if ('' === $bytes) {
                            break;
                        }

                        foreach (unpack('C*', $bytes) as $byte) {
                            if (null === $scanlineIndex) {
                                $filterType = $byte;
                                $scanlineIndex = 0;
                                $scanlineBytes = [];

                                continue;
                            }

                            $scanlineBytes[$scanlineIndex] = match ($filterType) {
                                0 => $byte,
                                1 => ($byte + ($scanlineBytes[$scanlineIndex - $filterPixelOffset] ?? 0)) % 256,
                                2 => ($byte + ($previousScanlineBytes[$scanlineIndex] ?? 0)) % 256,
                                3 => ($byte + ((($scanlineBytes[$scanlineIndex - $filterPixelOffset] ?? 0) + ($previousScanlineBytes[$scanlineIndex] ?? 0)) >> 1)) % 256,
                                4 => ($byte + self::paeth($scanlineBytes[$scanlineIndex - $filterPixelOffset] ?? 0, $previousScanlineBytes[$scanlineIndex] ?? 0, $previousScanlineBytes[$scanlineIndex - $filterPixelOffset] ?? 0)) % 256,
                                default => throw new \RuntimeException('Invalid filter "%d".', $byte),
                            };

                            ++$scanlineIndex;

                            if ($bitsPerPixel <= 8) {

                            } elseif (!($scanlineIndex % $bytesPerPixel)) {
                                yield ($scanlineBytes[$scanlineIndex - 1])
                                    + ($scanlineBytes[$scanlineIndex - 2] << 8)
                                    + ($scanlineBytes[$scanlineIndex - 3] << 16)
                                ;
                            }

                            if ($scanlineIndex === $scanlineLength) {
                                $scanlineIndex = null;
                                $previousScanlineBytes = $scanlineBytes;
                            }
                        }
                    }

                    break;
                default:
                    fseek($this->stream, $chunk['length'], \SEEK_CUR);
            }

            fseek($this->stream, 4, \SEEK_CUR);
        } while ('IEND' !== $chunk['type']);
    }

    private static function paeth(int $a, int $b, int $c): int
    {
        $pa = $b - $c;
        $mask = $pa >> self::SHIFT;
        $pa = ($mask ^ $pa) - $mask;

        $pb = $a - $c;
        $mask = $pb >> self::SHIFT;
        $pb = ($mask ^ $pb) - $mask;

        $pc = abs($a + $b - $c - $c);

        return match (true) {
            $pa <= $pb && $pa <= $pc => $a,
            $pb <= $pc => $b,
            default => $c,
        };
    }
}
