<script setup lang="ts">
import { ref } from 'vue';
import Banner from '@/Components/Banner.vue';
import AppHeader from '@/Components/AppHeader.vue';
import Sidebar from '@/Components/Sidebar/Sidebar.vue';
import { Toaster } from '@/Components/ui/toast';

defineProps({
    title: String
});

const showNavigation = ref(false);
</script>

<template>
    <AppHeader :show-navigation="showNavigation" @open-navigation="showNavigation = true" />
    <Sidebar :show-navigation="showNavigation" @close-navigation="showNavigation = false" />

    <!-- Page Content -->
    <section class="w-full lg:ps-64">
        <Banner />
        <Toaster />

        <main class="px-4 py-10 sm:px-6 md:px-8">
            <slot />
        </main>
    </section>
</template>
