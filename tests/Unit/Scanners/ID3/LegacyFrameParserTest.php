<?php

declare(strict_types=1);

use App\Data\ID3\FrameData;
use App\Data\ID3\ImageValueData;
use App\Enums\ID3\FrameType;
use App\Enums\ID3\Genre;
use App\Enums\ID3\ImageType;
use App\Exceptions\ParserError;
use App\Scanners\ID3\LegacyFrameParser;
use App\ValueObjects\Buffer;
use App\ValueObjects\Version;

it('checks for the correct version', function () {
    $parser = new LegacyFrameParser();
    expect($parser->check(Version::from(3, 0), Buffer::from('')))->toBeFalse()
        ->and($parser->check(Version::from(2, 0), Buffer::from('')))->toBeTrue();
})->group('scanners', 'id3');

it('parses a frame with text type', function () {
    $parser = new LegacyFrameParser();
    $buffer = Buffer::from("TAL\x00\x01\x54\x03Hello, world!");
    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame->id)->toBe('TAL')
        ->and($frame->type)->toBe(FrameType::ALBUM)
        ->and($frame->value)->toBe('Hello, world!');
})->group('scanners', 'id3');

it('parses a text frame with a genre', function () {
    $parser = new LegacyFrameParser();
    $buffer = Buffer::from("TCO\x00\x01\x54\x001");
    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame->id)->toBe('TCO')
        ->and($frame->type)->toBe(FrameType::GENRE)
        ->and($frame->value)->toBe(Genre::CLASSIC_ROCK);
})->group('scanners', 'id3');

it('does not parse an invalid frame', function () {
    $parser = new LegacyFrameParser();
    $buffer = Buffer::from('invalid_frame');

    expect(static fn () => $parser->parse($buffer))->toThrow(ParserError::class, 'Unsupported frame type: inv');
})->group('scanners', 'id3');

it('parses a url frame value', function () {
    $parser = new LegacyFrameParser();
    $buffer = Buffer::from("WCP\x01\x54\x03\x00123");
    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame->id)->toBe('WCP')
        ->and($frame->type)->toBe(FrameType::URL_COPYRIGHT)
        ->and($frame->value)->toBe('123');
})->group('scanners', 'id3');

it('parses a frame with an image value', function () {
    $parser = new LegacyFrameParser();
    $buffer = Buffer::from("PIC\x00\x00\x00\x00jpg\x00sample description\x00\xFF\xFF");

    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame->id)->toBe('PIC')
        ->and($frame->type)->toBe(FrameType::IMAGE)
        ->and($frame->value)->toBeInstanceOf(ImageValueData::class)
        ->and($frame->value->type)->toBe(ImageType::OTHER)
        ->and($frame->value->mime)->toBe('image/jpg')
        ->and($frame->value->description)->toBe('sample description')
        ->and($frame->value->data)->toBe("\xFF\xFF");
})->group('scanners', 'id3');
