<template>
    <div class="bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
        <div class="max-w-[1600px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-indigo-950">
                        {{ parentMessage ? parentMessage.content : 'Messages' }}
                    </h1>
                    <p v-if="parentMessage" class="text-sm text-slate-500 mt-0.5">
                        Child messages
                    </p>
                </div>
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-0.5">Total</span>
                        <span class="text-xl font-bold text-indigo-900 tabular-nums leading-none">
                            {{ (messagesPayload.total ?? 0).toLocaleString() }}
                        </span>
                    </div>
                    <button
                        v-if="projectPermissions.includes('Manage messages')"
                        @click="$emit('open-modal', { action: 'create' })"
                        class="h-10 px-5 flex items-center gap-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 active:scale-95"
                    >
                        <Plus :size="14" class="text-xs" />
                        <span class="text-xs">Add Message</span>
                    </button>
                </div>
            </div>

            <!-- Breadcrumbs when viewing a nested message (Back goes one level up each time) -->
            <div v-if="parentMessage && breadcrumbs.length > 0" class="mt-4 flex flex-wrap items-center gap-2">
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
                        @click="navigateToMessage(crumb)"
                        class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold hover:underline"
                    >
                        {{ truncateContent(crumb.content) }}
                    </button>
                    <span v-else class="text-slate-600 text-xs font-medium">{{ truncateContent(crumb.content) }}</span>
                </span>
            </div>

            <!-- API link (subtle) -->
            <p class="mt-3 text-[11px] text-slate-400 font-medium">
                <span class="uppercase tracking-wider">API:</span>
                <code class="ml-1 text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                    {{ parentMessage ? apiMessageUrl : apiMessagesUrl }}
                </code>
            </p>
        </div>

        <div class="max-w-[1600px] mx-auto space-y-4">
            <div v-if="filteredPagination.length > 0" class="flex flex-col xl:flex-row items-center justify-end gap-4">
                <div class="flex items-center gap-1">
                    <button
                        v-for="(link, index) in filteredPagination"
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
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Content</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="message in messagesPayload.data"
                            :key="message.id"
                            class="group hover:bg-indigo-50/20 transition-colors cursor-pointer"
                            @click="goToMessage(message)"
                        >
                            <td class="px-8 py-4">
                                <p class="text-sm text-slate-700 max-w-[520px] whitespace-normal break-words leading-snug">
                                    {{ message.content || '—' }}
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">#{{ message.id }}</p>
                            </td>
                            <td class="px-8 py-4 text-right" @click.stop>
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        v-if="projectPermissions.includes('View messages')"
                                        type="button"
                                        @click.stop="goToMessage(message)"
                                        class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-500 hover:border-indigo-200 transition-all text-xs font-bold"
                                    >
                                        View
                                    </button>
                                    <button
                                        v-if="projectPermissions.includes('Manage messages')"
                                        type="button"
                                        @click.stop="$emit('open-modal', { action: 'update', message })"
                                        class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-500 hover:border-indigo-200 transition-all text-xs font-bold"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        v-if="projectPermissions.includes('Manage messages')"
                                        type="button"
                                        @click.stop="$emit('open-modal', { action: 'delete', message })"
                                        class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-rose-500 hover:text-rose-600 hover:border-rose-200 transition-all text-xs font-bold"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="messagesPayload.data && messagesPayload.data.length === 0" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                    <div class="h-20 w-20 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 mb-6">
                        <MessageSquare :size="40" class="text-slate-500" />
                    </div>
                    <h3 class="text-lg font-bold text-indigo-950 mb-1">No messages</h3>
                    <p class="text-sm text-slate-400 max-w-xs">
                        {{ parentMessage ? 'No child messages under this message.' : 'Create a message to get started.' }}
                    </p>
                    <button
                        v-if="!parentMessage && projectPermissions.includes('Manage messages')"
                        type="button"
                        @click="$emit('open-modal', { action: 'create' })"
                        class="mt-6 text-xs font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-700 underline underline-offset-4"
                    >
                        Add Message
                    </button>
                </div>
                <Pagination
                    v-if="(messagesPayload.data?.length ?? 0) > 0 || (messagesPayload.total ?? 0) > 0"
                    :pagination-payload="messagesPayload"
                    :update-data="['messagesPayload']"
                    :min-pages="1"
                    @page-change="changePage"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { Plus, ArrowLeft, ChevronRight, ChevronLeft, MessageSquare } from 'lucide-vue-next';
import Pagination from '@/Partials/Pagination.vue';

export default defineComponent({
    components: {
        Plus,
        ArrowLeft,
        ChevronRight,
        ChevronLeft,
        MessageSquare,
        Pagination,
    },
    props: {
        parentMessage: { type: Object, default: null },
        messagesPayload: { type: Object, required: true },
        breadcrumbs: { type: Array, default: () => [] },
    },
    emits: ['open-modal'],
    setup() {
        const page = usePage();
        const projectPermissions = computed(() => page.props.projectPermissions ?? []);
        return { projectPermissions };
    },
    computed: {
        filteredPagination() {
            const current = this.messagesPayload.current_page ?? 1;
            const last = this.messagesPayload.last_page ?? 1;
            if (last <= 1) return [];
            const pages = [];
            pages.push({ label: 'prev', page: current > 1 ? current - 1 : null });
            pages.push({ label: '1', active: current === 1, page: 1 });
            if (current > 3) pages.push({ label: '...', active: false, page: null });
            const start = Math.max(2, current - 1);
            const end = Math.min(last - 1, current + 1);
            for (let i = start; i <= end; i++) {
                if (i !== 1 && i !== last) pages.push({ label: String(i), active: current === i, page: i });
            }
            if (current < last - 2) pages.push({ label: '...', active: false, page: null });
            if (last > 1) pages.push({ label: String(last), active: current === last, page: last });
            pages.push({ label: 'next', page: current < last ? current + 1 : null });
            return pages;
        },
        apiMessagesUrl() {
            const project = route().params.project;
            return project ? route('api.show.messages', { project }) : '';
        },
        apiMessageUrl() {
            if (!this.parentMessage) return '';
            const project = route().params.project;
            return project ? route('api.show.message', { project, message: this.parentMessage.id, type: 'children' }) : '';
        },
    },
    methods: {
        /** Navigate back one stage in the hierarchy (e.g. from child messages to parent, then to root) */
        goBackOneStage() {
            if (!this.breadcrumbs || this.breadcrumbs.length === 0) return;
            if (this.breadcrumbs.length === 1) {
                this.navigateToMessage(null);
                return;
            }
            const parentLevel = this.breadcrumbs[this.breadcrumbs.length - 2];
            this.navigateToMessage(parentLevel);
        },
        truncateContent(content, max = 40) {
            if (!content) return '—';
            return content.length <= max ? content : content.slice(0, max) + '…';
        },
        navigateToMessage(message) {
            const project = route().params.project;
            if (!project) return;
            if (message) {
                router.get(route('show.message', { project, message: message.id }));
            } else {
                router.get(route('show.messages', { project }));
            }
        },
        goToMessage(message) {
            const project = route().params.project;
            if (!project || !message?.id) return;
            const url = route('show.message', { project, message: message.id });
            router.get(url);
        },
        changePage(page) {
            const project = route().params.project;
            if (!project) return;
            const url = this.parentMessage
                ? route('show.message', { project, message: this.parentMessage.id })
                : route('show.messages', { project });
            router.get(url, { page }, { preserveState: true });
        },
    },
});
</script>
