<?php

declare(strict_types=1);

use App\Data\ID3\FrameData;
use App\Data\ID3\ImageValueData;
use App\Enums\ID3\FrameType;
use App\Enums\ID3\Genre;
use App\Enums\ID3\ImageType;
use App\Exceptions\ParserError;
use App\Scanners\ID3\FrameParser;
use App\ValueObjects\Buffer;

it('does not support compressed or unsynchronised frames', function () {
    $parser = new FrameParser();
    $buffer = Buffer::from("TALB\x00\x00\x00\x00\xFF\xFF");
    expect(static fn () => $parser->parse($buffer))->toThrow(ParserError::class);
})->group('scanners', 'id3');

it('does not parse an invalid frame type', function () {
    $parser = new FrameParser();
    $buffer = Buffer::from("UNKN\x00\x00\x00\x00\x00\x00");
    expect(static fn () => $parser->parse($buffer))->toThrow(ParserError::class, 'Unsupported frame type: UNKN');
})->group('scanners', 'id3');

it('parses a frame with text type', function () {
    $parser = new FrameParser();
    $buffer = Buffer::from("TALB\x00\x00\x00\x00\x00\x00\x03Hello, world!");
    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame->id)->toBe('TALB')
        ->and($frame->type)->toBe(FrameType::ALBUM)
        ->and($frame->value)->toBe('Hello, world!');
})->group('scanners', 'id3');

it('parses a frame with UTF-16LE text type', function () {
    $parser = new FrameParser();
    $buffer = Buffer::from("TALB\x00\x00\x00\x00\x00\x00\x00\x01".mb_convert_encoding('Hello, world!', 'UTF-16LE'));
    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame->id)->toBe('TALB')
        ->and($frame->type)->toBe(FrameType::ALBUM)
        ->and($frame->value)->toBe('Hello, world!');
})->group('scanners', 'id3');

it('parses a text frame a genre', function () {
    $parser = new FrameParser();
    $buffer = Buffer::from("TCON\x00\x00\x00\x00\x00\x00\x00(01)");
    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame->id)->toBe('TCON')
        ->and($frame->type)->toBe(FrameType::GENRE)
        ->and($frame->value)->toBe(Genre::CLASSIC_ROCK);
})->group('scanners', 'id3');

it('parses a url frame value', function () {
    $parser = new FrameParser();
    $buffer = Buffer::from("WPUB\x00\x00\x00\x00\x00\x00\x00123");
    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame->id)->toBe('WPUB')
        ->and($frame->type)->toBe(FrameType::URL_PUBLISHER)
        ->and($frame->value)->toBe('123');
})->group('scanners', 'id3');

it('parses a frame with an image value', function () {
    $parser = new FrameParser();
    $buffer = Buffer::from("APIC\x00\x00\x00\x00\x00\x00\x00image/jpg\x00\x03sample description\x00\x00\xFF\xFF");

    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame->id)->toBe('APIC')
        ->and($frame->type)->toBe(FrameType::IMAGE)
        ->and($frame->value)->toBeInstanceOf(ImageValueData::class)
        ->and($frame->value->type)->toBe(ImageType::COVER_FRONT)
        ->and($frame->value->mime)->toBe('image/jpg')
        ->and($frame->value->description)->toBe('sample description')
        ->and($frame->value->data)->toBe("\xFF\xFF");
})->group('scanners', 'id3');

it('parses a frame with an UTF-16LE image value', function () {
    $parser = new FrameParser();
    $buffer = Buffer::from("APIC\x00\x00\x00\x00\x00\x00\x01image/jpg\x00\x03\x00\x00".mb_convert_encoding('sample description', 'UTF-16LE')."\x00\x00\xFF\xFF");

    $frame = $parser->parse($buffer);

    expect($frame)->toBeInstanceOf(FrameData::class)
        ->and($frame->id)->toBe('APIC')
        ->and($frame->type)->toBe(FrameType::IMAGE)
        ->and($frame->value)->toBeInstanceOf(ImageValueData::class)
        ->and($frame->value->type)->toBe(ImageType::COVER_FRONT)
        ->and($frame->value->mime)->toBe('image/jpg')
        ->and($frame->value->description)->toBe('sample description')
        ->and($frame->value->data)->toBe("\xFF\xFF");
})->group('scanners', 'id3');
