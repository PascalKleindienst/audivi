<script setup lang="ts">
import { route } from 'ziggy-js';
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { SharedProps } from '@/types/inertia';
import { useColorMode } from '@vueuse/core';
import { Button } from '@/Components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger
} from '@/Components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Icon } from '@iconify/vue';
import { trans } from 'laravel-vue-i18n';

defineProps<{ showNavigation: boolean }>();
defineEmits<{ (e: 'openNavigation'): void }>();

const mode = useColorMode();
const pageProps = computed(() => usePage<SharedProps>().props);
const initials = computed(
    () =>
        pageProps.value.auth.user?.name
            ?.split(' ')
            .map((n) => n[0])
            .join('') ?? pageProps.value.auth.user?.name
);
</script>

<template>
    <header
        class="sticky inset-x-0 top-0 z-[48] flex w-full flex-wrap border-b bg-white/80 py-2.5 text-sm backdrop-blur-lg dark:border-gray-700 dark:bg-gray-800/80 sm:flex-nowrap sm:justify-start sm:py-4 lg:ps-64"
    >
        <nav class="mx-auto flex w-full basis-full items-center px-4 sm:px-6 md:px-8" aria-label="Global">
            <div class="me-5 lg:me-0 lg:hidden">
                <!-- Hamburger -->
                <div class="-me-2 flex items-center">
                    <Button variant="ghost" @click="$emit('openNavigation')">
                        <Icon icon="heroicons:bars-3" class="h-6 w-6" />
                    </Button>
                </div>
            </div>

            <div class="ms-auto flex w-full items-center justify-end sm:order-3 sm:justify-between sm:gap-x-3">
                <!-- Search -->
                <div class="sm:hidden">
                    <Button variant="ghost">
                        <Icon icon="heroicons:magnifying-glass" class="size-4 flex-shrink-0" />
                    </Button>
                </div>

                <div class="hidden w-full sm:block">
                    <label for="icon" class="sr-only">{{ trans('general.search') }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 start-0 z-20 flex items-center ps-4">
                            <Icon icon="heroicons:magnifying-glass" class="size-4 flex-shrink-0 text-gray-400" />
                        </div>
                        <input
                            id="icon"
                            type="text"
                            name="icon"
                            class="block w-full rounded-lg border-gray-200 bg-transparent px-4 py-2 ps-11 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:pointer-events-none disabled:opacity-50 dark:border-gray-700 dark:bg-slate-900 dark:text-gray-400 dark:focus:ring-gray-600"
                            :placeholder="trans('general.search')"
                        />
                    </div>
                </div>

                <div class="flex flex-row items-center justify-end gap-2">
                    <!-- Theme Toggle -->
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost">
                                <Icon
                                    icon="heroicons:moon"
                                    class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0"
                                />
                                <Icon
                                    icon="heroicons:sun"
                                    class="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100"
                                />
                                <span class="sr-only">{{ trans('general.toggle_theme') }}</span>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem class="cursor-pointer" @click="mode = 'light'">
                                {{ trans('general.light_theme') }}
                            </DropdownMenuItem>
                            <DropdownMenuItem class="cursor-pointer" @click="mode = 'dark'">
                                {{ trans('general.dark_theme') }}
                            </DropdownMenuItem>
                            <DropdownMenuItem class="cursor-pointer" @click="mode = 'auto'">
                                {{ trans('general.system_theme') }}
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <!-- Settings Dropdown -->
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Avatar>
                                <AvatarImage
                                    :src="
                                        pageProps.jetstream.managesProfilePhotos
                                            ? pageProps.auth.user.profile_photo_url
                                            : ''
                                    "
                                    :alt="pageProps.auth.user.name"
                                />
                                <AvatarFallback>{{ initials }}</AvatarFallback>
                            </Avatar>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent class="w-56">
                            <DropdownMenuLabel>{{ trans('general.manage_account') }}</DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuGroup>
                                <DropdownMenuItem>
                                    <Link :href="route('profile.show')" class="flex flex-1 items-center">
                                        <Icon icon="heroicons:user" class="mr-2" />
                                        {{ trans('general.profile') }}
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem v-if="pageProps.jetstream.hasApiFeatures">
                                    <Link :href="route('api-tokens.index')" class="flex flex-1 items-center">
                                        <Icon icon="heroicons:command-line" class="mr-2" />
                                        {{ trans('general.api_tokens') }}
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuGroup>
                            <DropdownMenuSeparator />
                            <DropdownMenuGroup>
                                <DropdownMenuItem>
                                    <Link
                                        :href="route('logout')"
                                        as="button"
                                        method="post"
                                        class="flex flex-1 items-center text-left"
                                    >
                                        {{ trans('auth.logout') }}
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuGroup>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </nav>
    </header>
</template>
