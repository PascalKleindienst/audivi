<?php

declare(strict_types=1);

namespace App\Utils;

trait FileByteReader
{
    protected const UINT8 = 1;

    protected const UINT16 = 2;

    protected const UINT24 = 3;

    protected const UINT32 = 4;

    /**
     * Get the "synch" representation of a number
     *
     * @param  int  $number  The number to convert
     */
    protected function synch(int $number): int
    {
        $out = 0;
        $mask = 0x7F000000;

        while ($mask) {
            $out >>= 1;
            $out |= $number & $mask;
            $mask >>= 8;
        }

        return $out;
    }

    /**
     * Get the buffer content at a specific position cast as an unsigned integer.
     */
    protected function getUint(string $buffer, int $position, int $byteSize = self::UINT8): int
    {
        return (int) (hexdec(bin2hex(substr($buffer, $position, $byteSize))));
    }

    /**
     * Get a portion of the buffer as raw bytes without any casting/transforming.
     */
    protected function getRaw(string $buffer, ?int $length, int $offset = 0): string
    {
        return substr($buffer, $offset, $length);
    }

    /**
     * Get a portion of the buffer cast to a string.
     */
    protected function getString(string $buffer, ?int $length, int $offset = 0): string
    {
        $string = '';
        $buffer = $this->getRaw($buffer, $length, $offset);
        $limit = mb_strlen($buffer);

        for ($i = 0; $i < $limit; $i++) {
            $char = $buffer[$i];
            $ord = mb_ord($char) ?: mb_ord($char, 'Windows-1252'); // fallback to Windows-1252 if not UTF-8

            if (
                ($ord >= mb_ord(' ') && $ord <= mb_ord('~')) || // skip ASCII control characters
                ($ord >= mb_ord('À') && $ord <= mb_ord('ÿ'))   // include umlauts
            ) {
                $string .= $char;
            }
        }

        return $string;
    }
}
