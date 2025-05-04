<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavBookshelf from '@/Components/Nav/NavBookshelf.vue';
import NavFooter from '@/Components/Nav/NavFooter.vue';
import NavMain from '@/Components/Nav/NavMain.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/Components/ui/sidebar';
import { type NavItem } from '@/types';
import { SharedProps } from '@/types/inertia';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, BuildingIcon, LayoutGrid, ListIcon, PencilIcon, TagIcon, UsersIcon } from 'lucide-vue-next';

const page = usePage<SharedProps>();

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        isActive: route().current('dashboard'),
        icon: LayoutGrid
    },
    {
        title: 'navigation.books',
        href: route('audio-books'),
        isActive: route().current('audio-books.*'),
        icon: BookOpen
    },
    {
        title: 'navigation.categories',
        href: route('dashboard'),
        icon: TagIcon
    },
    {
        title: 'navigation.series',
        href: route('series.index'),
        isActive: route().current('series.*'),
        icon: ListIcon
    },
    {
        title: 'navigation.authors',
        href: route('authors.index'),
        isActive: route().current('authors.*'),
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
        title: 'navigation.users',
        href: route('admin.users.index'),
        isActive: route().current('admin.users.*'),
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
            <NavFooter v-if="page.props.auth.user.is_admin" :items="footerNavItems" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
