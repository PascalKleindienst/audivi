import { type PlayEventPayload, usePlaylist } from '@/Composables/playlist';
import $events from '@/Utils/events';
import { MaybeRef, useMediaControls, useStorage } from '@vueuse/core';
import { computed, onMounted, toRef, watch, watchEffect } from 'vue';

const playerStorage = useStorage<{ volume: number; muted: boolean }>(
    'player',
    {
        volume: 0.5,
        muted: false
    },
    sessionStorage
);

export function usePlayer(player: MaybeRef<HTMLAudioElement | null>) {
    player = toRef(player);
    const { state, next, setTime } = usePlaylist();

    const initialTime = (state.value.track?.currentTime ?? 0) + (state.value.track?.start ?? 0);
    const endBuffer = computed(() => (buffered.value.length > 0 ? buffered.value[buffered.value.length - 1][1] : 0));
    const currentTrackSrc = computed(() => (state.value.track ? route('playlist.play', state.value.track?.id) : null));

    const controls = useMediaControls(player, {
        src: currentTrackSrc
    });
    const { buffered, currentTime, ended } = controls;

    // Tracklist change -> autostart
    $events.on<PlayEventPayload>('player:play', (payload) => {
        if (payload.detail.track === null) {
            return;
        }

        currentTime.value = payload.detail.track.start + payload.detail.track.currentTime;

        // autoplay new track (needed when tracks are on files)
        if (player.value) {
            player.value.onloadeddata = () => {
                if (payload.detail.track === null) {
                    return;
                }

                currentTime.value = payload.detail.track.start + payload.detail.track.currentTime;

                player.value?.play().catch((error) => {
                    console.error('Fehler beim Abspielen:', error);
                });
            };
        }
    });

    /**
     * Converts a duration in seconds to a string in the format "MM:SS".
     *
     * @param seconds - The duration in seconds.
     * @returns A string representing the duration in "MM:SS" format.
     */
    const formatDuration = (seconds: number) => {
        return new Date(1000 * seconds).toISOString().slice(14, 19);
    };

    // When the track ends, play the next track
    watch(ended, (newValue) => {
        if (newValue && state.value.chapter < state.value.playlist.length - 1) {
            next();
        }
    });

    // Watch Audio Settings
    watch([controls.volume, controls.muted], ([volume, muted]) => {
        playerStorage.value.volume = volume;
        playerStorage.value.muted = muted;
    });

    // Sync Current Time with track
    watchEffect(() => setTime(currentTime.value));

    // Set the current starting time for the track
    onMounted(() => {
        currentTime.value = initialTime ?? 0;
        controls.volume.value = playerStorage.value.volume;
        controls.muted.value = playerStorage.value.muted;
    });

    return {
        ...controls,
        endBuffer,
        formatDuration
    };
}
