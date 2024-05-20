<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ApiTokenManager from '@/Pages/API/Partials/ApiTokenManager.vue';
import PageHeader from '@/Components/PageHeader.vue';

defineProps({
    tokens: Array,
    availablePermissions: Array,
    defaultPermissions: Array
});
</script>

<template>
    <Head title="API Tokens" />

    <PageHeader>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">API Tokens</h2>
    </PageHeader>

    <div>
        <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
            <ApiTokenManager
                :tokens="tokens"
                :available-permissions="availablePermissions"
                :default-permissions="defaultPermissions"
            />
        </div>
    </div>
</template>
