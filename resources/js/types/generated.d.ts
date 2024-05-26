declare namespace App.Data {
    export type AudioBookData = {
        id: number | null;
        title: string;
        path: string;
        subtitle: string | null;
        volume: number | null;
        description?: string | null;
        rating: number | null;
        cover: string | null;
        language: string | null;
        authors?: Array<App.Data.AuthorData>;
        tracks?: Array<App.Data.TrackData>;
        series?: App.Data.SeriesData | null;
        publisher?: App.Data.PublisherData | null;
        published_at: string | null;
        created_at: string | null;
        updated_at: string | null;
    };
    export type AuthorData = {
        id: number | null;
        name: string;
        image?: string | null;
        description?: string | null;
        link?: string | null;
        created_at?: string | null;
        updated_at?: string | null;
    };
    export type PublisherData = {
        id: number | null;
        name: string;
        created_at?: string | null;
        updated_at?: string | null;
    };
    export type SeriesData = {
        id: number | null;
        name: string;
        created_at?: string | null;
        updated_at?: string | null;
    };
    export type TrackData = {
        id: number | null;
        title: string;
        position: number;
        path: string | null;
        start: number | null;
        end: number | null;
    };
}
declare namespace App.Data.ID3 {
    export type FrameData = {
        id: string;
        type: any;
        value: any | App.Data.ID3.Genre | string | null;
    };
    export type ImageValueData = {
        type: App.Data.ID3.ImageType;
        mime: string;
        description: string | null;
        data: string;
    };
    export type TagData = {
        kind: string | null;
        title: string | null;
        album: string | null;
        artist: string | null;
        year: number | null;
        version: number | Array<number> | null;
        comments: string | null;
        track: string | null;
        genre: App.Data.ID3.Genre | null;
        publisher: string | null;
        language: string | null;
        frames: Array<App.Data.ID3.FrameData>;
        images: Array<App.Data.ID3.ImageValueData>;
    };
    export type TrackData = {
        title: string;
        position: number;
        path: string | null;
    };
    export type Genre =
        | 0
        | 1
        | 2
        | 3
        | 4
        | 5
        | 6
        | 7
        | 8
        | 9
        | 10
        | 11
        | 12
        | 13
        | 14
        | 15
        | 16
        | 17
        | 18
        | 19
        | 20
        | 21
        | 22
        | 23
        | 24
        | 25
        | 26
        | 27
        | 28
        | 29
        | 30
        | 31
        | 32
        | 33
        | 34
        | 35
        | 36
        | 37
        | 38
        | 39
        | 40
        | 41
        | 42
        | 43
        | 44
        | 45
        | 46
        | 47
        | 48
        | 49
        | 50
        | 51
        | 52
        | 53
        | 54
        | 55
        | 56
        | 57
        | 58
        | 59
        | 60
        | 61
        | 62
        | 63
        | 64
        | 65
        | 66
        | 67
        | 68
        | 69
        | 70
        | 71
        | 72
        | 73
        | 74
        | 75
        | 76
        | 77
        | 78
        | 79
        | 80
        | 81
        | 82
        | 83
        | 84
        | 85
        | 86
        | 87
        | 88
        | 89
        | 90
        | 91
        | 92
        | 93
        | 94
        | 95
        | 96
        | 97
        | 98
        | 99
        | 100
        | 101
        | 102
        | 103
        | 104
        | 105
        | 106
        | 107
        | 108
        | 109
        | 110
        | 111
        | 112
        | 113
        | 114
        | 115
        | 116
        | 117
        | 118
        | 119
        | 120
        | 121
        | 122
        | 123
        | 124
        | 125
        | 126
        | 127
        | 128
        | 129
        | 130
        | 131
        | 132
        | 133
        | 134
        | 135
        | 136
        | 137
        | 138
        | 139
        | 140
        | 141
        | 142
        | 143
        | 144
        | 145
        | 146
        | 147
        | 148
        | 149
        | 150
        | 151
        | 152
        | 153
        | 154
        | 155
        | 156
        | 157
        | 158
        | 159
        | 160
        | 161
        | 162
        | 163
        | 164
        | 165
        | 166
        | 167
        | 168
        | 169
        | 170
        | 171
        | 172
        | 173
        | 174
        | 175
        | 176
        | 177
        | 178
        | 179
        | 180
        | 181
        | 182
        | 183
        | 184
        | 185
        | 186
        | 187
        | 188
        | 189
        | 190
        | 191;
    export type ImageType =
        | 0
        | 1
        | 2
        | 3
        | 4
        | 5
        | 6
        | 7
        | 8
        | 9
        | 10
        | 11
        | 12
        | 13
        | 14
        | 15
        | 16
        | 17
        | 18
        | 19
        | 20;
}
declare namespace App.Data.Library {
    export type FileData = {
        name: string;
        size: number;
        extension: string;
    };
    export type ItemData = {
        mediaFiles: Array<App.Data.Library.FileData>;
        size: number | null;
        folder: string;
        files: Array<App.Data.Library.FileData>;
        meta: App.Data.Library.MetaData;
    };
    export type MetaData = {
        title: string | null;
        subtitle: string | null;
        series: string | null;
        volume: number | null;
        description: string | null;
        publisher: string | null;
        published_at: string | null;
        cover: string | null;
        language: string | null;
        path: string;
        authors: Array<string>;
        tracks: Array<App.Data.ID3.TrackData>;
    };
}
