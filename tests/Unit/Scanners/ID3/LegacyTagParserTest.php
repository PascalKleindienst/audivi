<?php

declare(strict_types=1);

use App\Data\ID3\TagData;
use App\Enums\ID3\Genre;
use App\Scanners\ID3\LegacyTagParser;
use App\ValueObjects\Buffer;
use App\ValueObjects\Version;

it('checks if the buffer contains a valid ID3v1 tag', function () {
    $parser = new LegacyTagParser();
    $validBuffer = 'TAG'.str_repeat(' ', 125);
    $invalidBuffer = str_repeat(' ', 128);

    expect($parser->check(Version::from(1, 1), Buffer::from($validBuffer)))->toBeTrue()
        ->and($parser->check(Version::from(1, 1), Buffer::from($invalidBuffer)))->toBeFalse();
})->group('scanners', 'id3');

it('parses a ID3v1 tag', function () {
    $parser = new LegacyTagParser();
    $buffer = "TAGTITLE\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."ARTIST\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."ALBUM\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."1999COMMENT\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."\x01";

    $tagData = $parser->parse(Buffer::from($buffer));

    expect($tagData)->toBeInstanceOf(TagData::class)
        ->and($tagData->kind)->toBe('v1')
        ->and($tagData->title)->toBe('TITLE')
        ->and($tagData->artist)->toBe('ARTIST')
        ->and($tagData->album)->toBe('ALBUM')
        ->and($tagData->year)->toBe(1999)
        ->and($tagData->comments)->toBe('COMMENT')
        ->and($tagData->genre)->toBe(Genre::CLASSIC_ROCK);

})->group('scanners', 'id3');

it('parses additional ID3v1 data', function () {
    $parser = new LegacyTagParser();
    $buffer = "TAGTITLE\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."ARTIST\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."ALBUM\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."1999COMMENT\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x02"
        ."\x01";

    $tagData = $parser->parse(Buffer::from($buffer));

    expect($tagData)->toBeInstanceOf(TagData::class)
        ->and($tagData->kind)->toBe('v1')
        ->and($tagData->title)->toBe('TITLE')
        ->and($tagData->artist)->toBe('ARTIST')
        ->and($tagData->album)->toBe('ALBUM')
        ->and($tagData->year)->toBe(1999)
        ->and($tagData->comments)->toBe('COMMENT')
        ->and($tagData->genre)->toBe(Genre::CLASSIC_ROCK)
        ->and($tagData->version)->toEqual(Version::from(1, 1))
        ->and($tagData->track)->toBe('2');
})->group('scanners', 'id3');
