<template>
    <div class="bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
        <ManageTopicModal
            v-model="isShowingModal"
            :action="modalAction"
            :topic="topic"
            :parent-topic="parentTopic"
            :breadcrumbs="breadcrumbs"
            @onDeleted="onDeleted"
        />

        <div class="max-w-[1600px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-indigo-950">
                        {{ parentTopic ? parentTopic.title : 'Topics' }}
                    </h1>
                    <p v-if="parentTopic" class="text-sm text-slate-500 mt-0.5">
                        Child topics
                    </p>
                </div>
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-0.5">Total</span>
                        <span class="text-xl font-bold text-indigo-900 tabular-nums leading-none">
                            {{ (topicsPayload.total ?? 0).toLocaleString() }}
                        </span>
                    </div>
                    <button
                        v-if="projectPermissions.includes('Manage topics')"
                        @click="showModal(null, 'create')"
                        class="h-10 px-5 flex items-center gap-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 active:scale-95"
                    >
                        <Plus :size="14" class="text-xs" />
                        <span class="text-xs">Add Topic</span>
                    </button>
                </div>
            </div>

            <!-- Breadcrumbs when viewing a nested topic (Back goes one level up each time) -->
            <div v-if="parentTopic && breadcrumbs.length > 0" class="mt-4 flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    @click="goBackOneStage"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all"
                >
                    <ArrowLeft :size="14" />
                    Back
                </button>
                <span v-for="(crumb, idx) in breadcrumbs" :key="crumb.id" class="flex items-center gap-2">
                    <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                    <button
                        v-if="idx < breadcrumbs.length - 1"
                        type="button"
                        @click="goToTopic(crumb.id)"
                        class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold hover:underline"
                    >
                        {{ crumb.title }}
                    </button>
                    <span v-else class="text-slate-600 text-xs font-medium">{{ crumb.title }}</span>
                </span>
            </div>

            <!-- API link (subtle, same as Messages) -->
            <p class="mt-3 text-[11px] text-slate-400 font-medium">
                <span class="uppercase tracking-wider">API:</span>
                <code class="ml-1 text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded break-all">
                    {{ apiLink }}
                </code>
            </p>
        </div>

        <div class="max-w-[1600px] mx-auto space-y-4">
            <div v-if="paginationLinks.length > 0" class="flex flex-col xl:flex-row items-center justify-end gap-4">
                <div class="flex items-center gap-1">
                    <button
                        v-for="(link, index) in paginationLinks"
                        :key="index"
                        :disabled="!link.page"
                        @click="link.page && changePage(link.page)"
                        class="h-9 min-w-[36px] flex items-center justify-center rounded-lg transition-all font-bold text-[10px]"
                        :class="[
                            link.active
                                ? 'bg-indigo-600 text-white shadow-sm px-3'
                                : link.label === '...' ? 'text-slate-300 cursor-default' : 'bg-transparent text-slate-500 hover:bg-slate-100',
                            !link.page && link.label !== '...' ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'
                        ]"
                    >
                        <ChevronLeft v-if="link.label === 'prev'" :size="16" />
                        <ChevronRight v-else-if="link.label === 'next'" :size="16" />
                        <span v-else>{{ link.label }}</span>
                    </button>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Title</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Content</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="row in topicsPayload.data"
                            :key="row.id"
                            class="group hover:bg-indigo-50/20 transition-colors cursor-pointer"
                            @click="projectPermissions.includes('View topics') && goToTopic(row.id)"
                        >
                            <td class="px-8 py-4">
                                <p class="text-sm font-bold text-indigo-950">{{ row.title }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">#{{ row.id }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-700 max-w-[520px] whitespace-normal break-words leading-snug">
                                    {{ row.content || '—' }}
                                </p>
                            </td>
                            <td class="px-8 py-4 text-right" @click.stop>
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        v-if="projectPermissions.includes('View topics')"
                                        type="button"
                                        @click.stop="goToTopic(row.id)"
                                        class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-500 hover:border-indigo-200 transition-all text-xs font-bold"
                                    >
                                        View
                                    </button>
                                    <button
                                        v-if="projectPermissions.includes('Manage topics')"
                                        type="button"
                                        @click.stop="showModal(row, 'update')"
                                        class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-500 hover:border-indigo-200 transition-all text-xs font-bold"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        v-if="projectPermissions.includes('Manage topics')"
                                        type="button"
                                        @click.stop="showModal(row, 'delete')"
                                        class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-rose-500 hover:text-rose-600 hover:border-rose-200 transition-all text-xs font-bold"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="topicsPayload.data && topicsPayload.data.length === 0" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                    <div class="h-20 w-20 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 mb-6">
                        <FolderOpen :size="40" class="text-slate-500" />
                    </div>
                    <h3 class="text-lg font-bold text-indigo-950 mb-1">No topics</h3>
                    <p class="text-sm text-slate-400 max-w-xs">
                        {{ parentTopic ? 'No child topics under this topic.' : 'Create a topic to get started.' }}
                    </p>
                    <button
                        v-if="!parentTopic && projectPermissions.includes('Manage topics')"
                        type="button"
                        @click="showModal(null, 'create')"
                        class="mt-6 text-xs font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-700 underline underline-offset-4"
                    >
                        Add Topic
                    </button>
                </div>
                <Pagination
                    :pagination-payload="topicsPayload"
                    :update-data="['topicsPayload']"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import { router } from '@inertiajs/vue3';
import { Plus, ArrowLeft, ChevronRight, ChevronLeft, Trash2, FolderOpen } from 'lucide-vue-next';
import ManageTopicModal from './ManageTopicModal.vue';
import Pagination from '@/Partials/Pagination.vue';

export default defineComponent({
    components: {
        ManageTopicModal,
        Pagination,
        Plus,
        ArrowLeft,
        ChevronRight,
        ChevronLeft,
        Trash2,
        FolderOpen,
    },
    props: {
        parentTopic: { type: Object, default: null },
        topicsPayload: { type: Object, required: true },
        breadcrumbs: { type: Array, default: () => [] },
        projectPermissions: { type: Array, default: () => [] },
    },
    data() {
        return {
            isShowingModal: false,
            modalAction: null,
            topic: null,
        };
    },
    computed: {
        apiLink() {
            const project = route().params.project;
            if (this.parentTopic) {
                return route('api.show.topic', { project, topic: this.parentTopic.id, type: 'children' });
            }
            return route('api.show.topics', { project });
        },
        paginationLinks() {
            const current = this.topicsPayload.current_page ?? 1;
            const last = this.topicsPayload.last_page ?? 1;
            if (last <= 1) return [];
            const pages = [];
            pages.push({ label: 'prev', page: current > 1 ? current - 1 : null, active: false });
            pages.push({ label: '1', active: current === 1, page: 1 });
            if (current > 3) pages.push({ label: '...', active: false, page: null });
            const start = Math.max(2, current - 1);
            const end = Math.min(last - 1, current + 1);
            for (let i = start; i <= end; i++) {
                if (i !== 1 && i !== last) pages.push({ label: i.toString(), active: current === i, page: i });
            }
            if (current < last - 2) pages.push({ label: '...', active: false, page: null });
            if (last > 1) pages.push({ label: last.toString(), active: current === last, page: last });
            pages.push({ label: 'next', page: current < last ? current + 1 : null, active: false });
            return pages;
        },
    },
    methods: {
        onDeleted() {
            this.topic = null;
        },
        showModal(topic, action) {
            this.topic = topic;
            this.modalAction = action;
            this.isShowingModal = true;
        },
        /** Navigate back one stage in the hierarchy (from child to parent until root) */
        goBackOneStage() {
            if (!this.breadcrumbs || this.breadcrumbs.length === 0) return;
            if (this.breadcrumbs.length === 1) {
                router.get(route('show.topics', { project: route().params.project }));
                return;
            }
            const parentCrumb = this.breadcrumbs[this.breadcrumbs.length - 2];
            router.get(route('show.topic', { project: route().params.project, topic: parentCrumb.id }));
        },
        goToRoot() {
            router.get(route('show.topics', { project: route().params.project }));
        },
        goToTopic(topicId) {
            router.visit(route('show.topic', { project: route().params.project, topic: topicId }));
        },
        changePage(page) {
            const project = route().params.project;
            const opts = { preserveState: true };
            if (this.parentTopic) {
                router.get(route('show.topic', { project, topic: this.parentTopic.id }), { page }, opts);
            } else {
                router.get(route('show.topics', { project }), { page }, opts);
            }
        },
    },
});
</script>
