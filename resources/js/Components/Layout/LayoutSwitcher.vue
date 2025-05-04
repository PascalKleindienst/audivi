<script setup lang="ts">
import { useLayout } from '@/Components/Layout';
import { Button } from '@/Components/ui/button';
import { LayoutGridIcon, ListIcon } from 'lucide-vue-next';

const { layout } = useLayout();
</script>

<template>
    <div class="p-4">
        <Button size="icon" :variant="layout === 'list' ? 'default' : 'secondary'" class="rounded-tr-none rounded-br-none" @click="layout = 'list'">
            <ListIcon />
        </Button>
        <Button size="icon" :variant="layout === 'grid' ? 'default' : 'secondary'" class="rounded-tl-none rounded-bl-none" @click="layout = 'grid'">
            <LayoutGridIcon />
        </Button>
    </div>
</template>
