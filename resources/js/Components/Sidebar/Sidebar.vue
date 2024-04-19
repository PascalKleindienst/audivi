<script setup lang="ts">
import { route } from 'ziggy-js';
import { Link, usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import SidebarNavItem from '@/Components/Sidebar/SidebarNavItem.vue';
import { trans } from 'laravel-vue-i18n';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps<{ showNavigation: boolean }>();
const emit = defineEmits<{ (e: 'closeNavigation'): void }>();

const page = usePage();

const closeNavigation = () => {
    emit('closeNavigation');
};
</script>

<template>
    <div
        :class="{
            'translate-x-0': showNavigation,
            '-translate-x-full': !showNavigation
        }"
        class="fixed bottom-0 start-0 top-0 z-[60] flex w-64 transform flex-col overflow-y-auto border-e border-gray-200 bg-slate-50 py-6 transition-all duration-300 dark:border-gray-700 dark:bg-gray-800 lg:bottom-0 lg:end-auto lg:translate-x-0 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-slate-500 [&::-webkit-scrollbar-track]:bg-gray-100 dark:[&::-webkit-scrollbar-track]:bg-slate-700 [&::-webkit-scrollbar]:w-2"
    >
        <div class="px-4">
            <Link :href="route('dashboard')" class="flex items-center gap-2">
                <ApplicationLogo class="block h-8 w-auto" />
            </Link>
        </div>

        <nav class="flex w-full flex-col flex-wrap p-4">
            <ul class="space-y-1.5">
                <SidebarNavItem
                    :href="route('dashboard')"
                    icon="heroicons:home"
                    :active="page.component === 'Dashboard'"
                >
                    {{ trans('navigation.dashboard') }}
                </SidebarNavItem>

                <SidebarNavItem :href="route('dashboard')" icon="heroicons:book-open" :active="false">
                    {{ trans('navigation.books') }}
                </SidebarNavItem>

                <SidebarNavItem :href="route('dashboard')" icon="heroicons:tag" :active="false">
                    {{ trans('navigation.categories') }}
                </SidebarNavItem>

                <SidebarNavItem :href="route('dashboard')" icon="heroicons:list-bullet" :active="false">
                    {{ trans('navigation.series') }}
                </SidebarNavItem>

                <SidebarNavItem :href="route('dashboard')" icon="heroicons:pencil-square" :active="false">
                    {{ trans('navigation.authors') }}
                </SidebarNavItem>

                <SidebarNavItem :href="route('dashboard')" icon="heroicons:building-storefront" :active="false">
                    {{ trans('navigation.publishers') }}
                </SidebarNavItem>
            </ul>

            <h4 class="mb-2 mt-8 flex cursor-pointer items-center justify-between font-medium">
                {{ trans('navigation.bookshelf') }}
                <span>
                    <span class="sr-only">{{ trans('general.add') }}</span>
                    <Icon icon="heroicons:plus-circle" class="size-5" />
                </span>
            </h4>
            <ul class="space-y-1.5">
                <SidebarNavItem :href="route('dashboard')" :active="false"> Harry Potter </SidebarNavItem>
            </ul>
        </nav>

        <footer class="mt-auto w-full px-4">
            <ul>
                <SidebarNavItem :href="route('dashboard')" icon="heroicons:cog-6-tooth" :active="false">
                    {{ trans('navigation.settings') }}
                </SidebarNavItem>
            </ul>
        </footer>
    </div>

    <div
        v-show="showNavigation"
        style="z-index: 59"
        class="duration fixed inset-0 cursor-pointer bg-gray-900 bg-opacity-50 transition dark:bg-opacity-80"
        @click="closeNavigation()"
    >
        <span class="relative left-64 top-2 p-4">
            <Icon icon="heroicons:x-mark" class="inline size-6" />
        </span>
    </div>
</template>
