<script setup lang="ts">
import { route } from 'ziggy-js';
import { trans } from 'laravel-vue-i18n';
import ActionSection from '@/Components/ActionSection.vue';
import PasswordConfirmedDeleteAction from '@/Components/PasswordConfirmedDeleteAction.vue';
</script>

<template>
    <ActionSection>
        <template #title>{{ trans('profile.delete_account') }}</template>

        <template #description>
            {{ trans('profile.delete_account.subtitle') }}
        </template>

        <template #content>
            <PasswordConfirmedDeleteAction
                :route="route('current-user.destroy')"
                :title="trans('profile.delete_account')"
            >
                {{ trans('profile.delete_account.confirm') }}
            </PasswordConfirmedDeleteAction>
        </template>
    </ActionSection>
</template>
