<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavBookshelf from '@/Components/Nav/NavBookshelf.vue';
import NavFooter from '@/Components/Nav/NavFooter.vue';
import NavMain from '@/Components/Nav/NavMain.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/Components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, BuildingIcon, CogIcon, LayoutGrid, ListIcon, PencilIcon, TagIcon, UsersIcon } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid
    },
    {
        title: 'navigation.books',
        href: route('audio-books'),
        icon: BookOpen
    },
    {
        title: 'navigation.categories',
        href: route('dashboard'),
        icon: TagIcon
    },
    {
        title: 'navigation.series',
        href: route('dashboard'),
        icon: ListIcon
    },
    {
        title: 'navigation.authors',
        href: route('authors.index'),
        icon: PencilIcon
    },
    {
        title: 'navigation.publishers',
        href: route('dashboard'),
        icon: BuildingIcon
    }
];

// TODO
const bookShelfs: NavItem[] = [
    {
        title: 'Harry Potter',
        href: route('dashboard')
    }
];

const footerNavItems: NavItem[] = [
    {
        title: 'Settings',
        href: route('dashboard'),
        icon: CogIcon
    },
    {
        title: 'Users',
        href: route('dashboard'),
        icon: UsersIcon
    }
];
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <div class="flex size-25">
                                <ApplicationLogo />
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavBookshelf :items="bookShelfs" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
