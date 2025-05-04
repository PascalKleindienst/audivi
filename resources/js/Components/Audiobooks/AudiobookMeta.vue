<script setup lang="ts">
import { useHumanDate, useHumanFileSize, useHumanTime } from '@/Composables/human';
import { Link } from '@inertiajs/vue3';
import {
    Building2Icon,
    CalendarDaysIcon,
    GlobeIcon,
    HardDriveIcon,
    LibraryBigIcon,
    ListIcon,
    PencilIcon,
    StarHalfIcon,
    StarIcon,
    TimerIcon
} from 'lucide-vue-next';

const props = defineProps<{
    item: App.Data.AudioBookData;
}>();
</script>

<template>
    <ul class="text-muted-foreground mt-4 space-y-1 tracking-wide">
        <li v-if="props.item.rating" class="mb-2 flex items-center gap-x-1">
            <StarIcon class="size-4" :class="{ 'fill-yellow-400 text-yellow-400': props.item.rating > 0.5 }" />
            <StarIcon class="size-4" :class="{ 'fill-yellow-400 text-yellow-400': props.item.rating > 1.5 }" />
            <StarIcon class="size-4" :class="{ 'fill-yellow-400 text-yellow-400': props.item.rating > 2.5 }" />
            <StarIcon class="size-4" :class="{ 'fill-yellow-400 text-yellow-400': props.item.rating > 3.5 }" />

            <StarIcon v-if="props.item.rating < 4.5" class="size-4" :class="{ 'fill-yellow-400 text-yellow-400': props.item.rating > 4.5 }" />
            <StarHalfIcon v-else class="size-4 fill-yellow-400 text-yellow-400" />
            {{ props.item.rating }} / 5
        </li>
        <li v-if="props.item.authors" class="flex items-center gap-x-1 tracking-wide">
            <PencilIcon class="size-4" />
            <Link
                v-for="(author, index) in props.item.authors"
                :key="author.id"
                :href="route('authors.show', author.id)"
                class="text-primary hover:underline"
            >
                {{ author.name }} <span v-if="props.item.authors!.length > index + 1">, </span>
            </Link>
        </li>
        <!--<li v-if="props.item.narrator" class="flex items-center gap-x-1">-->
        <!--    <MicIcon class="size-4" />-->
        <!--    {{ props.item.narrator.name }}-->
        <!--</li>-->
        <li v-if="props.item.publisher" class="flex items-center gap-x-1">
            <Building2Icon class="size-4" />
            {{ props.item.publisher.name }}
        </li>
        <li v-if="props.item.published_at" class="flex items-center gap-x-1">
            <CalendarDaysIcon class="size-4" />
            {{ useHumanDate(new Date(props.item.published_at)) }}
        </li>
        <li v-if="props.item.fileSize" class="flex items-center gap-x-1">
            <HardDriveIcon class="size-4" />
            {{ useHumanFileSize(props.item.fileSize ?? 0) }}
        </li>
        <li v-if="props.item.duration" class="flex items-center gap-x-1">
            <TimerIcon class="size-4" />
            {{ useHumanTime(props.item.duration ?? 0, 'long') }}
        </li>
        <li v-if="props.item.language" class="flex items-center gap-x-1">
            <GlobeIcon class="size-4" />
            {{ props.item.language }}
        </li>
        <li v-if="props.item.series" class="flex items-center gap-x-1">
            <ListIcon class="size-4" />
            {{ props.item.series.name }}
        </li>
        <li v-if="props.item.volume" class="flex items-center gap-x-1">
            <LibraryBigIcon class="size-4" />
            {{ props.item.volume }}
        </li>
    </ul>
</template>
