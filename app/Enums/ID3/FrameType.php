<?php

declare(strict_types=1);

namespace App\Enums\ID3;

enum FrameType
{
    // Textual frames
    case ALBUM;
    case BPM;
    case COMPOSER;
    case GENRE;
    case COPYRIGHT;
    case ENCODING_TIME;
    case PLAYLIST_DELAY;
    case ORIGINAL_RELEASE_TIME;
    case RECORDING_TIME;
    case RELEASE_TIME;
    case TAGGING_TIME;
    case ENCODER;
    case WRITER;
    case FILE_TYPE;
    case INVOLVED_PEOPLE;
    case CONTENT_GROUP;
    case TITLE;
    case SUBTITLE;
    case INITIAL_KEY;
    case LANGUAGE;
    case LENGTH;
    case CREDITS;
    case MEDIA_TYPE;
    case MOOD;
    case ORIGINAL_ALBUM;
    case ORIGINAL_FILENAME;
    case ORIGINAL_WRITER;
    case ORIGINAL_ARTIST;
    case OWNER;
    case ARTIST;
    case BAND;
    case CONDUCTOR;
    case REMIXER;
    case SET_PART;
    case PRODUCED_NOTICE;
    case PUBLISHER;
    case TRACK;
    case RADIO_NAME;
    case RADIO_OWNER;
    case ALBUM_SORT;
    case PERFORMER_SORT;
    case TITLE_SORT;
    case ISRC;
    case ENCODER_SETTINGS;
    case SET_SUBTITLE;
    case YEAR;

    // URL frames
    case URL_COMMERCIAL;
    case URL_LEGAL;
    case URL_FILE;
    case URL_ARTIST;
    case URL_SOURCE;
    case URL_RADIO;
    case URL_PAYMENT;
    case URL_PUBLISHER;
    case URL_COPYRIGHT;

    // Misc
    case COMMENTS;
    case IMAGE;
    case PRIVATE;

    public static function from(string $frameType): ?self
    {
        return match ($frameType) {
            'TALB', 'TAL' => self::ALBUM,
            'TBPM', 'TBP' => self::BPM,
            'TCOM', 'TCM' => self::COMPOSER,
            'TCON', 'TCO' => self::GENRE,
            'TCOP', 'TCR' => self::COPYRIGHT,
            'TDEN' => self::ENCODING_TIME,
            'TDLY', 'TDY' => self::PLAYLIST_DELAY,
            'TDOR' => self::ORIGINAL_RELEASE_TIME,
            'TDRC' => self::RECORDING_TIME,
            'TDRL' => self::RELEASE_TIME,
            'TDTG' => self::TAGGING_TIME,
            'TENC', 'TEN' => self::ENCODER,
            'TEXT', 'TXT' => self::WRITER,
            'TFLT', 'TFT' => self::FILE_TYPE,
            'TIPL' => self::INVOLVED_PEOPLE,
            'TIT1', 'TT1' => self::CONTENT_GROUP,
            'TIT2', 'TT2' => self::TITLE,
            'TIT3', 'TT3' => self::SUBTITLE,
            'TKEY', 'TKE' => self::INITIAL_KEY,
            'TLAN', 'TLA' => self::LANGUAGE,
            'TLEN', 'TLE' => self::LENGTH,
            'TMCL' => self::CREDITS,
            'TMED', 'TMT' => self::MEDIA_TYPE,
            'TMOO' => self::MOOD,
            'TOAL', 'TOT' => self::ORIGINAL_ALBUM,
            'TOFN', 'TOF' => self::ORIGINAL_FILENAME,
            'TOLY', 'TOL' => self::ORIGINAL_WRITER,
            'TOPE', 'TOA' => self::ORIGINAL_ARTIST,
            'TOWN' => self::OWNER,
            'TPE1', 'TP1' => self::ARTIST,
            'TPE2', 'TP2' => self::BAND,
            'TPE3', 'TP3' => self::CONDUCTOR,
            'TPE4', 'TP4' => self::REMIXER,
            'TPOS', 'TPA' => self::SET_PART,
            'TPRO' => self::PRODUCED_NOTICE,
            'TPUB', 'TPB' => self::PUBLISHER,
            'TRCK', 'TRK' => self::TRACK,
            'TRSN' => self::RADIO_NAME,
            'TRSO' => self::RADIO_OWNER,
            'TSOA' => self::ALBUM_SORT,
            'TSOP' => self::PERFORMER_SORT,
            'TSOT' => self::TITLE_SORT,
            'TSRC', 'TRC' => self::ISRC,
            'TSSE', 'TSS' => self::ENCODER_SETTINGS,
            'TSST' => self::SET_SUBTITLE,
            'TYER', 'TYE' => self::YEAR,
            'WCOM', 'WCM' => self::URL_COMMERCIAL,
            'WCOP' => self::URL_LEGAL,
            'WOAF', 'WAF' => self::URL_FILE,
            'WOAR', 'WAR' => self::URL_ARTIST,
            'WOAS', 'WAS' => self::URL_SOURCE,
            'WORS' => self::URL_RADIO,
            'WPAY' => self::URL_PAYMENT,
            'WPUB', 'WPB' => self::URL_PUBLISHER,
            'WCP' => self::URL_COPYRIGHT,
            'COMM' => self::COMMENTS,
            'APIC', 'PIC' => self::IMAGE,
            'PRIV' => self::PRIVATE,
            default => null
        };
    }
}
