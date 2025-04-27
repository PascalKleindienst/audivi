import $events, { type EventPayload } from '@/Utils/events';
import { useStorage } from '@vueuse/core';
import axios from 'axios';
import { readonly } from 'vue';
import PlaylistData = App.Data.PlaylistData;
import PlaylistTrackData = App.Data.PlaylistTrackData;

export interface PlaylistState {
    playlist: PlaylistTrackData[];
    bookId?: number | null;
    cover?: string | null;
    title?: string | null;
    track: PlaylistTrackData | null;
    chapter: number;
    time: number;
}

export interface PlayEventPayload extends EventPayload {
    detail: PlaylistState;
}

// TODO: Default from server
const playlistState = useStorage<PlaylistState>('playlist', {
    playlist: [],
    track: null,
    chapter: 0,
    time: 0
});

export function usePlaylist() {
    /**
     * Change the current playlist the user is listening to
     */
    const changePlaylist = async (trackId: number = 0) => {
        return axios.post<PlaylistData>(route('playlist.update'), { trackId }).then((response) => {
            playlistState.value.playlist = response.data.tracks;
            playlistState.value.bookId = response.data.bookId;
            playlistState.value.title = response.data.title;
            playlistState.value.cover = response.data.cover;
            play(playlistState.value.playlist.findIndex((track: PlaylistTrackData) => track.id === trackId));

            return playlistState;
        });
    };

    /**
     * Change to a new track number
     */
    const changeTrack = (index: number) => {
        if (index < 0 || index >= playlistState.value.playlist.length) {
            return;
        }

        playlistState.value.chapter = index;
        playlistState.value.track = playlistState.value.playlist[index];
        setTime(playlistState.value.track?.start);
    };

    /**
     * Set the current time
     *
     * If it exceeds the end of the track, the next track will be played
     */
    const setTime = (time: number) => {
        if (playlistState.value.track == null) {
            return;
        }

        playlistState.value.time = time; // overall time

        if (playlistState.value.time > playlistState.value.track.end) {
            next();
        }

        playlistState.value.track.currentTime = Math.max(0, time - (playlistState.value.track.start ?? 0));
    };

    /**
     * Set the current chapter
     */
    const setChapter = (chapter: number) => {
        playlistState.value.chapter = chapter;
    };

    /**
     * Play the track at the given index.
     * @param index The index of the track to play
     * @emits player:play<PlayEventPayload>
     */
    const play = (index: number) => {
        if (index < 0 || index >= playlistState.value.playlist.length) {
            return;
        }

        changeTrack(index);
        $events.emit('player:play', { detail: playlistState.value });
    };

    /**
     * Play the next track in the track list. If the last track is currently playing,
     * this function will play the first track.
     */
    const next = () => {
        play((playlistState.value.chapter + 1) % playlistState.value.playlist.length);
    };

    /**
     * Play the previous track in the track list. If the first track is currently playing,
     * this function will play the last track.
     */
    const previous = () => {
        play((playlistState.value.chapter - 1) % playlistState.value.playlist.length);
    };

    return {
        play,
        next,
        previous,
        changePlaylist,
        changeTrack,
        setChapter,
        setTime,

        // Use readonly to prevent direct state mutations from components
        state: readonly(playlistState)
    };
}
