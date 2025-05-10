<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import { Scrubber } from '@/Components/Player';
import { useSidebar } from '@/Components/ui/sidebar';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/Components/ui/dropdown-menu';
import { usePlayer } from '@/Composables/player';
import { usePlaylist } from '@/Composables/playlist';
import { CircleGaugeIcon, ListIcon, SkipBackIcon, SkipForwardIcon, Volume1Icon, Volume2, VolumeIcon, VolumeOffIcon } from 'lucide-vue-next';
import { Icon } from '@iconify/vue';
import { computed, shallowRef, useTemplateRef, watch } from 'vue';
import AudiobookCover from '@/Components/Audiobooks/AudiobookCover.vue';

const { state: sidebar } = useSidebar();
const sidebarOpen = computed(() => sidebar.value !== 'collapsed');

const audioPlayer = useTemplateRef<HTMLAudioElement>('audioPlayer');
const { play, next, previous, state } = usePlaylist();
const { playing, currentTime, muted, volume, rate, endBuffer, formatDuration } = usePlayer(audioPlayer);

const currentTimeInChapter = shallowRef(0);
const syncTime = () => {
    currentTime.value = currentTimeInChapter.value + state.value.track!.start;
};

// update current time in chapter as playback progress
watch(currentTime, () => (currentTimeInChapter.value = currentTime.value - state.value.track!.start));

// Reset current time in chapter when track changes
watch(
    () => state.value.track,
    () => (currentTimeInChapter.value = 0)
);
</script>

<template>
    <div
        :tabindex="0"
        autofocus
        class="text-card-foreground border-border bg-card /**h-16**/ fixed bottom-0 z-40 flex w-full items-center justify-between gap-16 border-t-1 px-4 py-2 outline-none"
        :class="{ 'control-bar-width--sidebar-open': sidebarOpen, 'control-bar-width--sidebar-closed': !sidebarOpen }"
        @keydown.prevent.space="playing = !playing"
        @keydown.right="currentTime += 10"
        @keydown.left="currentTime -= 10"
        @keydown.m="muted = !muted"
        @keydown.up="volume = Math.min(1, volume + 0.1)"
        @keydown.down="volume = Math.max(0, volume - 0.1)"
    >
        <audio ref="audioPlayer" crossorigin="anonymous" class="hidden" />

        <div class="flex items-center gap-2">
            <Button variant="ghost" size="icon" :title="$t('player.controls.previous')" @click="previous">
                <SkipBackIcon class="size-6" />
            </Button>
            <Button
                variant="ghost"
                size="icon"
                :title="playing ? $t('player.controls.pause') : $t('player.controls.play')"
                @click="playing = !playing"
            >
                <Icon v-if="playing" icon="heroicons:pause-circle" class="size-12" />
                <Icon v-if="!playing" icon="heroicons:play-circle" class="size-12" />
            </Button>
            <Button variant="ghost" size="icon" :title="$t('player.controls.next')" @click="next">
                <SkipForwardIcon class="size-6" />
            </Button>

            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon" class="hover:bg-transparent" :title="$t('player.controls.tracks')">
                        <ListIcon class="size-8" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                    <DropdownMenuItem
                        v-for="(item, index) in state.playlist"
                        :key="item.id"
                        class="cursor-pointer"
                        :class="{ 'font-semibold': index === state.chapter }"
                        @click="play(index)"
                    >
                        {{ item.title }} - {{ formatDuration(item.duration) }}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>

        <div v-if="state.track" class="flex w-full items-center gap-4">
            <AudiobookCover :cover="state.cover" :title="state.title" :pattern="state.bookId ?? 0" class="size-16 bg-cover" />
            <div class="flex-1 space-y-2">
                <div class="flex items-center justify-between gap-4">
                    <div class="line-clamp-2 text-sm font-semibold">{{ state.track.title }}</div>
                    <div class="text-muted-foreground line-clamp-2 text-xs">{{ state.title ?? '' }}</div>
                    <div class="text-muted-foreground text-xs text-nowrap">
                        {{ formatDuration(state.track.currentTime) }} /
                        {{ formatDuration(state.track.duration) }}
                    </div>
                </div>

                <Scrubber
                    v-model="currentTimeInChapter"
                    :max="state.track.duration"
                    :secondary="endBuffer"
                    class="mt-2"
                    @update:model-value="syncTime"
                >
                    <!--<Scrubber v-model="currentTime" :min="state.track.start_at" :max="state.track.duration" :secondary="endBuffer" class="mt-2">-->
                    <template #default="{ position, pendingValue }">
                        <div
                            class="absolute bottom-0 mb-4 -translate-x-1/2 transform rounded bg-black px-2 py-1 text-xs text-white"
                            :style="{ left: position }"
                        >
                            {{ formatDuration(pendingValue) }}
                        </div>
                    </template>
                </Scrubber>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div class="flex-column flex items-center gap-2">
                <Button variant="ghost" size="icon" class="hover:bg-transparent" :title="$t('player.controls.volume')" @click="muted = !muted">
                    <VolumeIcon v-show="!muted && volume === 0" class="size-6" />
                    <Volume1Icon v-show="!muted && volume < 0.5 && volume > 0" class="size-6" />
                    <Volume2 v-show="!muted && volume >= 0.5 && volume <= 1" class="size-6" />
                    <VolumeOffIcon v-show="muted" class="size-6" />
                </Button>
                <Scrubber v-model="volume" :max="1" class="min-w-16" />
            </div>

            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon" class="hover:bg-transparent" :title="$t('player.controls.rate')">
                        <CircleGaugeIcon class="size-6" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                    <DropdownMenuItem v-for="rval in [0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2]" :key="rval" as-child>
                        <button type="button" class="w-full cursor-pointer" :class="{ 'font-semibold': rate == rval }" @click="rate = rval">
                            {{ rval }}x
                        </button>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>

<style scoped>
.control-bar-width--sidebar-closed {
    max-width: calc(100% - var(--sidebar-width-icon, 0));
}

.control-bar-width--sidebar-open {
    max-width: calc(100% - var(--sidebar-width, 0));
}
</style>
