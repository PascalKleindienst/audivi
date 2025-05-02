<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardFooter } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Switch } from '@/Components/ui/switch';
import { Head, Link, useForm } from '@inertiajs/vue3';
import UserData = App.Data.UserData;

const props = defineProps<{
    user: UserData;
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    is_admin: props.user.is_admin,
    password: undefined,
    password_confirmation: undefined
});

const submit = () => {
    form.patch(route('admin.users.update', props.user.id), {
        preserveScroll: true,

        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
            }
        }
    });
};
</script>

<template>
    <Head :title="$t('user.title')" />
    <form class="mx-auto w-full max-w-6xl p-4" @submit.prevent="submit">
        <Card>
            <CardContent class="grid grid-cols-12 gap-6">
                <div class="col-span-12 grid place-items-center md:col-span-4">
                    <img :src="user.avatar" :alt="user.name" class="size-40 rounded-full object-cover text-center" />
                </div>
                <div class="col-span-12 space-y-6 md:col-span-8">
                    <div class="grid gap-2">
                        <Label for="name">{{ $t('user.fields.name') }}</Label>
                        <Input id="name" v-model="form.name" type="text" />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">{{ $t('user.fields.email') }}</Label>
                        <Input id="email" v-model="form.email" type="text" />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center gap-2">
                            <Switch id="is_admin" v-model="form.is_admin" />
                            <Label for="is_admin">{{ $t('user.fields.is_admin') }}</Label>
                        </div>

                        <InputError :message="form.errors.is_admin" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">{{ $t('user.fields.password') }}</Label>
                        <Input id="password" v-model="form.password" type="password" autocomplete="new-password" />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">{{ $t('user.fields.confirm_password') }}</Label>
                        <Input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" />
                        <InputError :message="form.errors.password_confirmation" class="mt-2" />
                    </div>
                </div>
            </CardContent>
            <CardFooter class="flex justify-between">
                <Button variant="default" type="submit">{{ $t('general.save') }}</Button>
                <Button :as="Link" variant="secondary" :href="route('admin.users.index')">
                    {{ $t('general.cancel') }}
                </Button>
            </CardFooter>
        </Card>
    </form>
</template>
