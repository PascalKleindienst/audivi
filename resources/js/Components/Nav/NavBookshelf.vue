<script setup lang="ts">
import { SidebarGroup, SidebarGroupAction, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/Components/ui/sidebar';
import type { NavItem } from '@/types';
import type { SharedProps } from '@/types/inertia';
import { Link, usePage } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedProps>();
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel>{{ $t('navigation.bookshelf') }}</SidebarGroupLabel>
        <SidebarGroupAction title="Add Project">
            <Plus />
            <span class="sr-only">Add Project</span>
        </SidebarGroupAction>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                    <Link :href="item.href" prefetch>
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
