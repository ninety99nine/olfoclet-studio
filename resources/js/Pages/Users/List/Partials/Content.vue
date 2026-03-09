<template>
    <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
        <ManageUserModal
            v-model="isShowingModal"
            :action="modalAction"
            :user="user"
            :available-permissions="availablePermissions"
            :show-addbutton="false"
            @onDeleted="onDeleted"
        />

        <div class="max-w-[1600px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h1 class="text-2xl font-black tracking-tight text-indigo-950">Users</h1>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-0.5">Total</span>
                        <span class="text-xl font-bold text-indigo-900 tabular-nums leading-none">
                            {{ (usersPayload.total ?? 0).toLocaleString() }}
                        </span>
                    </div>

                    <button
                        v-if="projectPermissions.includes('Manage users')"
                        @click="showModal(null, 'create')"
                        class="h-10 px-5 flex items-center gap-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 active:scale-95"
                    >
                        <Plus :size="14" class="text-xs" />
                        <span class="text-xs">Add User</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-[1600px] mx-auto">
            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Name</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Email</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Permissions</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Created</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="u in usersPayload.data"
                            :key="u.id"
                            class="group hover:bg-indigo-50/20 transition-colors cursor-pointer"
                            @click="goToUser(u.id)"
                        >
                            <td class="px-8 py-4" @click.stop="goToUser(u.id)">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 group-hover:border-indigo-100 transition-all shrink-0">
                                        <User :size="14" class="text-xs" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-indigo-950">{{ u.name }}</div>
                                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">#{{ u.id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-700">{{ u.email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-indigo-900">{{ permissionsCount(u) }}</span>
                                    <span class="text-slate-400 text-xs">/ {{ availablePermissions.length }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ u.created_at ? moment(u.created_at).format('DD MMM YYYY') : '—' }}
                            </td>
                            <td class="px-8 py-4 text-right" @click.stop>
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        v-if="projectPermissions.includes('Manage users')"
                                        type="button"
                                        @click="showModal(u, 'update')"
                                        class="h-8 px-3 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 hover:text-indigo-700 transition-all"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        v-if="projectPermissions.includes('Manage users')"
                                        type="button"
                                        @click="showModal(u, 'delete')"
                                        class="h-8 px-3 rounded-lg text-xs font-bold text-rose-600 hover:bg-rose-50 transition-all"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="usersPayload.data.length === 0">
                            <td colspan="5" class="px-8 py-12 text-center text-slate-500 text-sm">
                                No users
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination class="mt-6" :pagination-payload="usersPayload" :update-data="['usersPayload']" />
        </div>
    </div>
</template>

<script>
import { defineComponent, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ManageUserModal from './ManageUserModal.vue';
import Pagination from '@/Partials/Pagination.vue';
import { Plus, User } from 'lucide-vue-next';
import moment from 'moment';

export default defineComponent({
    components: {
        ManageUserModal,
        Pagination,
        Plus,
        User,
    },
    props: {
        availablePermissions: { type: Array, default: () => [] },
        project: { type: Object, default: null },
        usersPayload: { type: Object, required: true },
    },
    setup() {
        const projectPermissions = computed(() => usePage().props.projectPermissions ?? []);
        return { projectPermissions };
    },
    data() {
        return {
            isShowingModal: false,
            modalAction: null,
            user: null,
            moment,
        };
    },
    methods: {
        permissionsCount(u) {
            const perms = u.pivot?.permissions;
            return Array.isArray(perms) ? perms.length : 0;
        },
        goToUser(userId) {
            if (!this.project?.id || !userId) return;
            this.$inertia.visit(route('show.user', { project: this.project.id, user: userId }));
        },
        onDeleted() {
            this.user = null;
            this.$inertia.reload();
        },
        showModal(user, action) {
            this.user = user;
            this.modalAction = action;
            this.isShowingModal = true;
        },
    },
});
</script>
