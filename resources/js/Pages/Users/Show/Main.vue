<template>
    <app-layout title="User">
        <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
            <ManageUserModal
                v-model="isShowingModal"
                :action="modalAction"
                :user="userForModal"
                :available-permissions="availablePermissions"
                :show-addbutton="false"
                @onDeleted="onDeleted"
            />

            <div class="max-w-[1400px] mx-auto space-y-6">
                <!-- Back + breadcrumb (same pattern as Topics / Messages) -->
                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        :href="route('show.users', { project: project?.id })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all"
                    >
                        <ArrowLeft :size="14" />
                        Back
                    </Link>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <Link
                            :href="route('show.users', { project: project?.id })"
                            class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold hover:underline"
                        >
                            Users
                        </Link>
                    </span>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <span class="text-slate-600 text-xs font-medium truncate max-w-[280px]" :title="user?.name">
                            {{ user?.name ?? '—' }}
                        </span>
                    </span>
                </div>

                <!-- Header card -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 lg:p-8">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                            <div class="flex items-start gap-4">
                                <div class="h-14 w-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                    <User :size="24" class="text-indigo-600" />
                                </div>
                                <div>
                                    <h1 class="text-2xl font-black tracking-tight text-indigo-950">
                                        {{ user?.name ?? '—' }}
                                    </h1>
                                    <p class="text-sm text-slate-600 mt-1">{{ user?.email ?? '—' }}</p>
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">
                                        User #{{ user?.id ?? '—' }}
                                    </p>
                                    <p class="text-sm text-slate-500 mt-2">
                                        Joined {{ user?.created_at ? moment(user.created_at).format('DD MMM YYYY [at] HH:mm') : '—' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <button
                                    v-if="projectPermissions.includes('Manage users')"
                                    type="button"
                                    @click="showModal('update')"
                                    class="h-10 px-4 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 hover:border-indigo-200 hover:text-indigo-700 transition-all text-sm"
                                >
                                    <Pencil :size="14" />
                                    Edit
                                </button>
                                <button
                                    v-if="projectPermissions.includes('Manage users')"
                                    type="button"
                                    @click="showModal('delete')"
                                    class="h-10 px-4 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-rose-600 font-bold hover:bg-rose-50 hover:border-rose-200 transition-all text-sm"
                                >
                                    <Trash2 :size="14" />
                                    Delete
                                </button>
                            </div>
                        </div>

                        <!-- Permissions summary -->
                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Project permissions</p>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="perm in (user?.pivot?.permissions ?? [])"
                                    :key="perm"
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full"
                                >
                                    {{ perm }}
                                </span>
                                <span
                                    v-if="!user?.pivot?.permissions?.length"
                                    class="text-sm text-slate-400"
                                >
                                    No permissions assigned
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ManageUserModal from '@/Pages/Users/List/Partials/ManageUserModal.vue';
import { User, Pencil, Trash2, ArrowLeft, ChevronRight } from 'lucide-vue-next';
import moment from 'moment';

export default defineComponent({
    name: 'UserShow',
    components: {
        AppLayout,
        Link,
        ManageUserModal,
        User,
        Pencil,
        Trash2,
        ArrowLeft,
        ChevronRight,
    },
    props: {
        availablePermissions: { type: Array, default: () => [] },
        project: { type: Object, required: true },
        user: { type: Object, required: true },
    },
    setup() {
        const projectPermissions = computed(() => usePage().props.projectPermissions ?? []);
        return { projectPermissions };
    },
    data() {
        return {
            isShowingModal: false,
            modalAction: 'update',
            moment,
        };
    },
    computed: {
        userForModal() {
            return this.user ?? null;
        },
    },
    methods: {
        showModal(action) {
            this.modalAction = action;
            this.isShowingModal = true;
        },
        onDeleted() {
            this.$inertia.visit(route('show.users', { project: this.project?.id }));
        },
    },
});
</script>
