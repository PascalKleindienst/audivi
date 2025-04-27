<script setup lang="ts">
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
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
import { Input } from '@/Components/ui/input';
import { SidebarTrigger } from '@/Components/ui/sidebar';
import type { SharedProps } from '@/types/inertia';
import { Icon } from '@iconify/vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useColorMode } from '@vueuse/core';
import { Search } from 'lucide-vue-next';
import { computed } from 'vue';
import BreadcrumbItemData = App.Data.BreadcrumbItemData;

defineProps<{
    breadcrumbs?: BreadcrumbItemData[];
}>();

const mode = useColorMode();
const pageProps = computed(() => usePage<SharedProps>().props);
const initials = computed(
    () =>
        pageProps.value.auth.user?.name
            ?.split(' ')
            .map((n: string) => n[0])
            .join('') ?? pageProps.value.auth.user?.name
);
</script>

<template>
    <header
        class="border-sidebar-border bg-sidebar flex h-16 shrink-0 items-center gap-8 border-b px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div class="ml-auto flex flex-row items-center justify-end gap-2">
            <div class="hidden sm:block">
                <label for="icon" class="sr-only">{{ $t('general.search') }}</label>
                <div class="relative w-full max-w-sm items-center">
                    <kbd
                        class="text-muted-foreground border-border absolute inset-y-0 end-0 m-2 flex hidden items-center justify-center border-1 px-2 font-mono text-xs not-[.os-macos_&]:block"
                        >Strg+K</kbd
                    >
                    <kbd
                        class="text-muted-foreground border-border absolute inset-y-0 end-0 m-2 flex hidden items-center justify-center border-1 px-2 font-mono text-xs [.os-macos_&]:block"
                        >⌘+K</kbd
                    >
                    <Input id="search" type="text" :placeholder="$t('general.search')" class="pr-12 pl-10" />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                        <Search class="text-muted-foreground size-6" />
                    </span>
                </div>
            </div>
            <!-- Theme Toggle -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon" class="focus-within:ring-primary focus-within:ring-2">
                        <Icon icon="heroicons:moon" class="h-[1.2rem] w-[1.2rem] scale-100 rotate-0 transition-all dark:scale-0 dark:-rotate-90" />
                        <Icon
                            icon="heroicons:sun"
                            class="absolute h-[1.2rem] w-[1.2rem] scale-0 rotate-90 transition-all dark:scale-100 dark:rotate-0"
                        />
                        <span class="sr-only">{{ $t('general.toggle_theme') }}</span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuItem class="cursor-pointer" @click="mode = 'light'">
                        {{ $t('general.light_theme') }}
                    </DropdownMenuItem>
                    <DropdownMenuItem class="cursor-pointer" @click="mode = 'dark'">
                        {{ $t('general.dark_theme') }}
                    </DropdownMenuItem>
                    <DropdownMenuItem class="cursor-pointer" @click="mode = 'auto'">
                        {{ $t('general.system_theme') }}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <!-- Settings Dropdown -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon" class="focus-within:ring-primary relative w-auto rounded-full focus-within:ring-2">
                        <Avatar>
                            <AvatarImage
                                :src="pageProps.jetstream.managesProfilePhotos ? pageProps.auth.user.profile_photo_url : ''"
                                :alt="pageProps.auth.user.name"
                            />
                            <AvatarFallback>{{ initials }}</AvatarFallback>
                        </Avatar>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-56" align="end">
                    <DropdownMenuLabel>{{ $t('general.manage_account') }}</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuGroup>
                        <DropdownMenuItem>
                            <Link :href="route('profile.show')" class="flex flex-1 items-center">
                                <Icon icon="heroicons:user" class="mr-2" />
                                {{ $t('general.profile') }}
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem v-if="pageProps.jetstream.hasApiFeatures">
                            <Link :href="route('api-tokens.index')" class="flex flex-1 items-center">
                                <Icon icon="heroicons:command-line" class="mr-2" />
                                {{ $t('general.api_tokens') }}
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuGroup>
                    <DropdownMenuSeparator />
                    <DropdownMenuGroup>
                        <DropdownMenuItem>
                            <Link :href="route('logout')" as="button" method="post" class="flex flex-1 items-center text-left">
                                {{ $t('auth.logout') }}
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuGroup>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
