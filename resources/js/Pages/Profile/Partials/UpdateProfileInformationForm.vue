<script setup lang="ts">
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { useToast } from '@/Components/ui/toast';
import { usePhoto } from '@/Composables/photo';
import type { SharedProps, User } from '@/types/inertia';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed, ref, watchEffect } from 'vue';

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

    console.log('updateProfileInformation');

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
    console.log('Form success', form.recentlySuccessful);
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
        <template #title>{{ $t('profile.information.title') }}</template>

        <template #description>{{ $t('profile.information.description') }}</template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="page.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <input id="photo" ref="photoInput" type="file" class="hidden" @change="updatePreview" />
                <Label for="photo">{{ $t('profile.information.photo') }}</Label>

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

                <Button v-if="user.profile_photo_path" variant="secondary" type="button" class="mt-2" @click.prevent="deletePhoto">
                    {{ trans('profile.information.photo.remove') }}
                </Button>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <Label for="name">{{ $t('auth.name') }}</Label>
                <Input id="name" v-model="form.name" type="text" class="mt-1" required autocomplete="name" />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <Label for="email">{{ $t('auth.email') }}</Label>
                <Input id="email" v-model="form.email" type="email" class="mt-1" required autocomplete="username" />
                <InputError :message="form.errors.email" class="mt-2" />

                <div v-if="page.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="mt-2 text-sm">
                        {{ $t('auth.email.unverified') }}

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="hover:text-primary focus:ring-ring rounded-md text-sm underline focus:ring-2 focus:ring-offset-2 focus:outline-hidden"
                            @click.prevent="sendEmailVerification"
                        >
                            {{ $t('auth.email.resend_verification') }}
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="text-success mt-2 text-sm font-medium">
                        {{ $t('auth.email.verification_was_sent') }}
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                {{ $t('general.saved_success') }}
            </ActionMessage>

            <Button type="submit" variant="default" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('general.save') }}
            </Button>
        </template>
    </FormSection>
</template>
