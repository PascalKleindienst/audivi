<script setup lang="ts">
import type { SharedProps, User } from '@/types/inertia';
import { route } from 'ziggy-js';
import { computed, ref, watchEffect } from 'vue';
import { trans } from 'laravel-vue-i18n';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import { usePhoto } from '@/Composables/photo';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { useToast } from '@/Components/ui/toast';

const { toast } = useToast();

const props = defineProps<{ user: User }>();
const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null as File | null
});

const page = computed(() => usePage<SharedProps>().props);

const verificationLinkSent = ref(false);
const photoPreview = ref<string | ArrayBuffer | null>(null);
const photoInput = ref<HTMLInputElement | null>(null);

const { updatePreview, deletePhoto, clearFileInput, selectNewPhoto } = usePhoto(photoInput, photoPreview);

const updateProfileInformation = () => {
    const files = photoInput.value?.files;
    if (files && files.length) {
        form.photo = files[0] ?? null;
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearFileInput()
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

watchEffect(() => {
    if (form.recentlySuccessful) {
        toast({
            variant: 'success',
            description: trans('general.saved_success')
        });
    }
});
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>{{ trans('profile.information.title') }}</template>

        <template #description>{{ trans('profile.information.description') }}</template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="page.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <input id="photo" ref="photoInput" type="file" class="hidden" @change="updatePreview" />
                <Label for="photo">{{ trans('profile.information.photo') }}</Label>

                <!-- Current Profile Photo -->
                <div v-show="!photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.name" class="h-20 w-20 rounded-full object-cover" />
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat"
                        :style="'background-image: url(\'' + photoPreview + '\');'"
                    />
                </div>

                <Button variant="secondary" class="me-2 mt-2" type="button" @click.prevent="selectNewPhoto">
                    {{ trans('profile.information.photo.select') }}
                </Button>

                <Button
                    v-if="user.profile_photo_path"
                    variant="secondary"
                    type="button"
                    class="mt-2"
                    @click.prevent="deletePhoto"
                >
                    {{ trans('profile.information.photo.remove') }}
                </Button>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <Label for="name">{{ trans('auth.name') }}</Label>
                <Input id="name" v-model="form.name" type="text" class="mt-1" required autocomplete="name" />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <Label for="email">{{ trans('auth.email') }}</Label>
                <Input id="email" v-model="form.email" type="email" class="mt-1" required autocomplete="username" />
                <InputError :message="form.errors.email" class="mt-2" />

                <div v-if="page.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="mt-2 text-sm">
                        {{ trans('auth.email.unverified') }}

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="rounded-md text-sm underline hover:text-primary focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                            @click.prevent="sendEmailVerification"
                        >
                            {{ trans('auth.email.resend_verification') }}
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 text-sm font-medium text-success">
                        {{ trans('auth.email.verification_was_sent') }}
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <Button
                type="submit"
                variant="default"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                {{ trans('general.save') }}
            </Button>
        </template>
    </FormSection>
</template>
