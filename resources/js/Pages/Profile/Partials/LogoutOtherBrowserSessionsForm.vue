<script setup lang="ts">
import { route } from 'ziggy-js';
import { Icon } from '@iconify/vue';
import { trans } from 'laravel-vue-i18n';
import ActionSection from '@/Components/ActionSection.vue';
import PasswordConfirmedDeleteAction from '@/Components/PasswordConfirmedDeleteAction.vue';

defineProps<{
    sessions: Array<{
        agent: {
            browser: string;
            is_desktop: boolean;
            platform: string;
        };
        ip_address: string;
        is_current_device: boolean;
        last_active: string;
    }>;
}>();
</script>

<template>
    <ActionSection>
        <template #title>{{ trans('profile.sessions.title') }}</template>

        <template #description>
            {{ trans('profile.sessions.subtitle') }}
        </template>

        <template #content>
            <!-- Other Browser Sessions -->
            <div v-if="sessions.length > 0" class="mt-5 space-y-6">
                <div v-for="(session, i) in sessions" :key="i" class="flex items-center">
                    <Icon
                        class="h-8 w-8 text-gray-500"
                        :icon="
                            session.agent.is_desktop ? 'heroicons:computer-desktop' : 'heroicons:device-phone-mobile'
                        "
                    />

                    <div class="ms-3">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ session.agent.platform ? session.agent.platform : trans('general.unknown') }} -
                            {{ session.agent.browser ? session.agent.browser : trans('general.unknown') }}
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">
                                {{ session.ip_address }},

                                <span v-if="session.is_current_device" class="font-semibold text-green-500">
                                    {{ trans('profile.sessions.current_device') }}
                                </span>
                                <span v-else>
                                    {{ trans('profile.session.last_active', { active: session.last_active }) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <PasswordConfirmedDeleteAction
                    :title="trans('profile.sessions.logout')"
                    :route="route('other-browser-sessions.destroy')"
                >
                    {{ trans('profile.sessions.confirm_password') }}
                </PasswordConfirmedDeleteAction>
            </div>
        </template>
    </ActionSection>
</template>
