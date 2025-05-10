<script setup lang="ts">
import ActionSection from '@/Components/ActionSection.vue';
import PasswordConfirmedDeleteAction from '@/Components/PasswordConfirmedDeleteAction.vue';
import { Icon } from '@iconify/vue';

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
        <template #title>{{ $t('profile.sessions.title') }}</template>

        <template #description>
            {{ $t('profile.sessions.subtitle') }}
        </template>

        <template #content>
            <!-- Other Browser Sessions -->
            <div v-if="sessions.length > 0" class="mt-5 space-y-6">
                <div v-for="(session, i) in sessions" :key="i" class="flex items-center">
                    <Icon
                        class="h-8 w-8 text-gray-500"
                        :icon="session.agent.is_desktop ? 'heroicons:computer-desktop' : 'heroicons:device-phone-mobile'"
                    />

                    <div class="ms-3">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ session.agent.platform ? session.agent.platform : $t('general.unknown') }} -
                            {{ session.agent.browser ? session.agent.browser : $t('general.unknown') }}
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">
                                {{ session.ip_address }},

                                <span v-if="session.is_current_device" class="font-semibold text-green-500">
                                    {{ $t('profile.sessions.current_device') }}
                                </span>
                                <span v-else>
                                    {{ $t('profile.session.last_active', { active: session.last_active }) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <PasswordConfirmedDeleteAction :title="$t('profile.sessions.logout')" :route="route('other-browser-sessions.destroy')">
                    {{ $t('profile.sessions.confirm_password') }}
                </PasswordConfirmedDeleteAction>
            </div>
        </template>
    </ActionSection>
</template>
