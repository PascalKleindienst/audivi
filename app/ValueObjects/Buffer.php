<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class Buffer implements \Stringable
{
    public int $length;

    private function __construct(public string $content)
    {
        $this->length = \strlen($this->content);
    }

    public static function from(string $content): self
    {
        return new self($content);
    }

    public function convert(string $to = 'UTF-8', string $from = 'ISO-8859-1'): string
    {
        return mb_convert_encoding($this->content, $to, $from);
    }

    public function position(string $needle, int $offset = 0): int
    {
        $pos = \strpos($this->content, $needle, $offset);

        return $pos === false ? 0 : $pos;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
