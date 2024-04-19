<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import AppHeader from '@/Components/AppHeader.vue';
import Sidebar from '@/Components/Sidebar/Sidebar.vue';

defineProps({
    title: String
});

const showNavigation = ref(false);
</script>

<template>
    <Head :title="title" />

    <AppHeader :show-navigation="showNavigation" @open-navigation="showNavigation = true" />
    <Sidebar :show-navigation="showNavigation" @close-navigation="showNavigation = false" />

    <!-- Page Content -->
    <section class="w-full lg:ps-64">
        <Banner />
        <main class="px-4 pt-10 sm:px-6 md:px-8">
            <slot />
        </main>
    </section>
</template>
