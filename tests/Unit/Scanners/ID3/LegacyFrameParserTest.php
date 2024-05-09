<?php

use App\Data\ID3\FrameData;
use App\Data\ID3\FrameType;
use App\Data\ID3\ImageType;
use App\Data\ID3\ImageValueData;
use App\Scanners\ID3\LegacyFrameParser;

it('checks for the correct version', function () {
    $parser = new LegacyFrameParser();
    expect($parser->check(3))->toBeFalse()
        ->and($parser->check(2))->toBeTrue();
})->group('scanners', 'id3');

it('parses a frame with text type', function () {
    $parser = new LegacyFrameParser();
    $buffer = "TAL\x00\x01\x54\x03Hello, world!";
    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame?->id)->toBe('TAL')
        ->and($frame?->type)->toBe(FrameType::ALBUM)
        ->and($frame?->value)->toBe('Hello, world!');
})->group('scanners', 'id3');

it('parses a text frame with a genre', function () {
    $parser = new LegacyFrameParser();
    $buffer = "TCO\x00\x01\x54\x001";
    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame?->id)->toBe('TCO')
        ->and($frame?->type)->toBe(FrameType::GENRE)
        ->and($frame?->value)->toBe(\App\Data\ID3\Genre::CLASSIC_ROCK);
})->group('scanners', 'id3');

it('does not parse an invalid frame', function () {
    $parser = new LegacyFrameParser();
    $buffer = 'invalid_frame';
    $frame = $parser->parse($buffer);

    expect($frame)->toBeNull();
})->group('scanners', 'id3');

it('parses a url frame value', function () {
    $parser = new LegacyFrameParser();
    $buffer = "WCP\x01\x54\x03\x00123";
    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame?->id)->toBe('WCP')
        ->and($frame?->type)->toBe(FrameType::URL_COPYRIGHT)
        ->and($frame?->value)->toBe('123');
})->group('scanners', 'id3');

it('parses a frame with an image value', function () {
    $parser = new LegacyFrameParser();
    $buffer = "PIC\x00\x00\x00\x00jpg\x00sample description\x00\xFF\xFF";

    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame?->id)->toBe('PIC')
        ->and($frame?->type)->toBe(FrameType::IMAGE)
        ->and($frame?->value)->toBeInstanceOf(ImageValueData::class)
        ->and($frame?->value->type)->toBe(ImageType::OTHER)
        ->and($frame?->value->mime)->toBe('image/jpg')
        ->and($frame?->value->description)->toBe('sample description')
        ->and($frame?->value->data)->toBe("\xFF\xFF");
})->group('scanners', 'id3');
