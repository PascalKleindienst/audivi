<script setup lang="ts">
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Pagination from '@/Components/Pagination.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Switch } from '@/Components/ui/switch';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { PaginatedDataCollection } from '@/types/inertia';
import { Head, Link, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ArrowUpDownIcon, PencilLineIcon, TrashIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import UserData = App.Data.UserData;

const props = defineProps<{
    users: PaginatedDataCollection<UserData>;
}>();

const onSearch = useDebounceFn(() => {
    router.reload({
        only: ['users'],
        data: {
            search: search.value
        }
    });
}, 250);

const userBeingDeleted = ref<number | null>(null);
const deleteUser = () => {
    router.delete(route('admin.users.destroy', userBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (userBeingDeleted.value = null)
    });
};

const direction = ref('asc');
const sort = (field: string) => {
    router.reload({
        only: ['users'],
        data: {
            sort: field,
            direction: direction.value
        }
    });

    direction.value = direction.value === 'asc' ? 'desc' : 'asc';
};

const search = ref('');
</script>

<template>
    <Head :title="$t('user.title')" />
    <section class="container mx-auto p-4">
        <!--<Card>-->
        <Input v-model="search" type="search" class="mb-4" :placeholder="$t('user.search')" @input="onSearch" />

        <Table class="bg-card text-card-foreground border-border rounded border">
            <TableHeader>
                <TableRow>
                    <TableHead>{{ $t('user.fields.avatar') }}</TableHead>
                    <TableHead>
                        {{ $t('user.fields.name') }}
                        <Button
                            size="icon"
                            variant="ghost"
                            class="ml-2"
                            :title="$t('user.sort_by', { field: $t('user.fields.name') })"
                            @click="sort('name')"
                        >
                            <ArrowUpDownIcon class="size-4" />
                        </Button>
                    </TableHead>
                    <TableHead>
                        {{ $t('user.fields.email') }}
                        <Button
                            size="icon"
                            variant="ghost"
                            class="ml-2"
                            :title="$t('user.sort_by', { field: $t('user.fields.email') })"
                            @click="sort('email')"
                        >
                            <ArrowUpDownIcon class="size-4" />
                        </Button>
                    </TableHead>
                    <TableHead>{{ $t('user.fields.is_admin') }}</TableHead>
                    <TableHead></TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="user in props.users.data" :key="user.id">
                    <TableCell>
                        <img :src="user.avatar" alt="" class="size-8 rounded-full" />
                    </TableCell>
                    <TableCell>{{ user.name }}</TableCell>
                    <TableCell>{{ user.email }}</TableCell>
                    <TableCell>
                        <Switch :model-value="user.is_admin" disabled aria-readonly="true" />
                    </TableCell>
                    <TableCell class="space-x-2 text-right">
                        <Button size="icon" variant="default" :as="Link" :href="route('admin.users.edit', user.id)" :title="$t('general.edit')">
                            <PencilLineIcon class="size-4" />
                        </Button>
                        <Button size="icon" variant="destructive" :title="$t('general.delete')" @click="userBeingDeleted = user.id">
                            <TrashIcon class="size-4" />
                        </Button>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <!--</Card>-->

        <ConfirmationModal :show="userBeingDeleted != null" @close="userBeingDeleted = null">
            <template #title>{{ $t('user.delete.confirmation.title') }}</template>

            <template #content>{{ $t('user.delete.confirmation.description') }}</template>

            <template #footer>
                <Button variant="ghost" @click="userBeingDeleted = null"> {{ $t('Cancel') }}</Button>

                <Button variant="destructive" @click="deleteUser">
                    {{ $t('Delete') }}
                </Button>
            </template>
        </ConfirmationModal>

        <Pagination :paginator="users" :only="['users']" />
    </section>
</template>
