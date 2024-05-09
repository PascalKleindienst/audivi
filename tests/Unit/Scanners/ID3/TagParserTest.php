<?php

use App\Data\ID3\Genre;
use App\Data\ID3\ImageType;
use App\Data\ID3\ImageValueData;
use App\Data\ID3\TagData;
use App\Scanners\ID3\TagParser;

it('checks if the buffer contains a valid ID3v2 tag', function () {
    $parser = new TagParser();
    $validBuffer = "ID3\x03\x00".str_repeat("\x00", 11);
    $invalidBuffer = str_repeat(' ', 14);

    expect($parser->check($validBuffer))->toBeTrue()
        ->and($parser->check($invalidBuffer))->toBeFalse();
})->group('scanners', 'id3');

it('does not support unsynchronisation', function () {
    $parser = new TagParser();
    $buffer = "ID3\x03\x00\xFF";

    expect($parser->parse($buffer))->toBeNull();
})->group('scanners', 'id3');

it('parses a tag', function () {
    $parser = new TagParser();
    $buffer = "ID3\x03\x00\x00\x00\x00\x0Ev"
        ."APIC\x00\x00\x00\x22\x00\x00\x00image/jpg\x00\x03sample description\x00\x00\xFF\xFF"
        ."TPE1\x00\x00\x00\x07\x00\x00\x00ARTIST"
        ."TALB\x00\x00\x00\x06\x00\x00\x00ALBUM"
        ."COMM\x00\x00\x00\x11\x00\x00\x00\x00\x00\x00\x00SOME\x20COMMENT"
        ."TCON\x00\x00\x00\x04\x00\x00\x00(1)"
        ."TYER\x00\x00\x00\x05\x00\x00\x001970"
        ."TRCK\x00\x00\x00\x02\x00\x00\x002"
        ."COMM\x00\x00\x00\x1E\x00\x00\x00XXX"
        ."ID3v1 Comment\x00SOME COMMENT"
        ."TIT2\x00\x00\x00\x06\x00\x00\x00TITLE"
        ."TIT3\x00\x00\x00\x09\x00\x00\x00SUBTITLE";
    $tag = $parser->parse($buffer);

    expect($tag)->toBeInstanceOf(TagData::class)
        ->and($tag?->kind)->toBe('v2')
        ->and($tag?->title)->toBe('TITLE')
        ->and($tag?->album)->toBe('ALBUM')
        ->and($tag?->artist)->toBe('ARTIST')
        ->and($tag?->year)->toBe(1970)
        ->and($tag?->version)->toBe([3, 0])
        ->and($tag?->comment)->toBe(null)
        ->and($tag?->track)->toBe('2')
        ->and($tag?->genre)->toBe(Genre::CLASSIC_ROCK)
        ->and($tag?->images)->toHaveCount(1)
        ->and($tag?->images[0])->toBeInstanceOf(ImageValueData::class)
        ->and($tag?->images[0]->type)->toBe(ImageType::COVER_FRONT)
        ->and($tag?->images[0]->mime)->toBe('image/jpg')
        ->and($tag?->images[0]->data)->toBe("\xFF\Xff");
})->group('scanners', 'id3');

it('it skips invalid frames and non frame blocks', function () {
    $parser = new TagParser();
    $buffer = "ID3\x03\x00\x00\x00\x00\x0Ev"
        ."\xFFTPE1\x00\x00\x00\x07\x00\x00\x00ARTIST";
    $tag = $parser->parse($buffer);

    expect($tag)->toBeInstanceOf(TagData::class)
        ->and($tag?->artist)->toBeNull();
})->group('scanners', 'id3');

it('parses a tag with an extended header', function () {
    $parser = new TagParser();
    $buffer = "ID3\x03\x00\x40\x00\x00\x0Ev\x00\x00\x00\x06\x00\x00"
        ."APIC\x00\x00\x00\x22\x00\x00\x00image/jpg\x00\x03sample description\x00\x00\xFF\xFF"
        ."TPE1\x00\x00\x00\x07\x00\x00\x00ARTIST"
        ."TALB\x00\x00\x00\x06\x00\x00\x00ALBUM"
        ."COMM\x00\x00\x00\x11\x00\x00\x00\x00\x00\x00\x00SOME\x20COMMENT"
        ."TCON\x00\x00\x00\x04\x00\x00\x00(1)"
        ."TYER\x00\x00\x00\x05\x00\x00\x001970"
        ."TRCK\x00\x00\x00\x02\x00\x00\x002"
        ."COMM\x00\x00\x00\x1E\x00\x00\x00XXX"
        ."ID3v1 Comment\x00SOME COMMENT"
        ."TIT2\x00\x00\x00\x06\x00\x00\x00TITLE"
        ."TIT3\x00\x00\x00\x09\x00\x00\x00SUBTITLE";
    $tag = $parser->parse($buffer);

    expect($tag)->toBeInstanceOf(TagData::class)
        ->and($tag?->kind)->toBe('v2')
        ->and($tag?->title)->toBe('TITLE')
        ->and($tag?->album)->toBe('ALBUM')
        ->and($tag?->artist)->toBe('ARTIST')
        ->and($tag?->year)->toBe(1970)
        ->and($tag?->version)->toBe([3, 0])
        ->and($tag?->comment)->toBe(null)
        ->and($tag?->track)->toBe('2')
        ->and($tag?->genre)->toBe(Genre::CLASSIC_ROCK)
        ->and($tag?->images)->toHaveCount(1)
        ->and($tag?->images[0])->toBeInstanceOf(ImageValueData::class)
        ->and($tag?->images[0]->type)->toBe(ImageType::COVER_FRONT)
        ->and($tag?->images[0]->mime)->toBe('image/jpg')
        ->and($tag?->images[0]->data)->toBe("\xFF\Xff");
})->group('scanners', 'id3');
