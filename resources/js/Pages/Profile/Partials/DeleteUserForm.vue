<script setup lang="ts">
import ActionSection from '@/Components/ActionSection.vue';
import PasswordConfirmedDeleteAction from '@/Components/PasswordConfirmedDeleteAction.vue';
</script>

<template>
    <ActionSection>
        <template #title>{{ $t('profile.delete_account') }}</template>

        <template #description>
            {{ $t('profile.delete_account.subtitle') }}
        </template>

        <template #content>
            <PasswordConfirmedDeleteAction :route="route('current-user.destroy')" :title="$t('profile.delete_account')">
                {{ $t('profile.delete_account.confirm') }}
            </PasswordConfirmedDeleteAction>
        </template>
    </ActionSection>
</template>
