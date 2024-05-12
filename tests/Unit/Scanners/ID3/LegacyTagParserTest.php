<?php

use App\Data\ID3\Genre;
use App\Data\ID3\TagData;
use App\Scanners\ID3\LegacyTagParser;

it('checks if the buffer contains a valid ID3v1 tag', function () {
    $parser = new LegacyTagParser();
    $validBuffer = 'TAG'.str_repeat(' ', 125);
    $invalidBuffer = str_repeat(' ', 128);

    expect($parser->check($validBuffer))->toBeTrue()
        ->and($parser->check($invalidBuffer))->toBeFalse();
})->group('scanners', 'id3');

it('parses a ID3v1 tag', function () {
    $parser = new LegacyTagParser();
    $buffer = "TAGTITLE\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."ARTIST\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."ALBUM\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."1999COMMENT\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ."\x01";

    $tagData = $parser->parse($buffer);

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

    $tagData = $parser->parse($buffer);

    expect($tagData)->toBeInstanceOf(TagData::class)
        ->and($tagData->kind)->toBe('v1')
        ->and($tagData->title)->toBe('TITLE')
        ->and($tagData->artist)->toBe('ARTIST')
        ->and($tagData->album)->toBe('ALBUM')
        ->and($tagData->year)->toBe(1999)
        ->and($tagData->comments)->toBe('COMMENT')
        ->and($tagData->genre)->toBe(Genre::CLASSIC_ROCK)
        ->and($tagData->version)->toBe(1.1)
        ->and($tagData->track)->toBe('2');
})->group('scanners', 'id3');
