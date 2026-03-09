<template>
    <!--
        Standard table pagination footer. Use this component for all paginated tables.
        - Renders only when there are 5+ pages (configurable via minPages).
        - Pass paginationPayload (with total, current_page, last_page, from, to, data, links) and either
          apiMode + @page-change for API-driven lists, or updateData for Inertia.
    -->
    <div
        v-if="showFooter"
        class="bg-white px-4 py-3 rounded-b-2xl border-t border-slate-100 shadow-sm flex flex-col items-end"
    >
        <!-- Top row: pagination toolbar + Go to -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end sm:gap-4 mb-3 w-full sm:w-auto">
            <nav class="relative z-0 inline-flex rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden shrink-0" aria-label="Pagination">
                    <template v-if="apiMode">
                        <button
                            type="button"
                            :disabled="paginationPayload.current_page <= 1"
                            class="relative inline-flex items-center justify-center min-w-[40px] h-9 border-r border-slate-200 bg-white text-slate-500 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white text-sm font-medium transition-colors"
                            @click="paginationPayload.current_page > 1 && goToPage(paginationPayload.current_page - 1)"
                        >
                            <span class="sr-only">Previous</span>
                            <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                        </button>
                        <template v-for="(item, p) in pageItems" :key="p">
                            <button
                                v-if="item.type === 'page'"
                                type="button"
                                :class="[
                                    item.active
                                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 border-y border-slate-200 min-w-[40px] h-9 text-sm font-medium whitespace-nowrap'
                                        : 'min-w-[40px] h-9 border-r border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-sm font-medium transition-colors whitespace-nowrap',
                                    item.isLastLabel ? 'min-w-[4rem] px-3' : ''
                                ]"
                                @click="goToPage(item.page)"
                            >
                                {{ item.label }}
                            </button>
                            <span
                                v-else-if="item.type === 'ellipsis'"
                                class="inline-flex items-center justify-center min-w-[40px] h-9 border-r border-slate-200 bg-white text-slate-400 text-sm"
                            >
                                ...
                            </span>
                        </template>
                        <button
                            type="button"
                            :disabled="paginationPayload.current_page >= paginationPayload.last_page"
                            class="relative inline-flex items-center justify-center min-w-[40px] h-9 bg-white text-slate-500 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white text-sm font-medium transition-colors"
                            @click="paginationPayload.current_page < paginationPayload.last_page && goToPage(paginationPayload.current_page + 1)"
                        >
                            <span class="sr-only">Next</span>
                            <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                        </button>
                    </template>
                    <template v-else>
                        <a
                            v-if="previousLink && previousLink.url"
                            :href="previousLink.url"
                            class="relative inline-flex items-center justify-center min-w-[40px] h-9 border-r border-slate-200 bg-white text-slate-500 hover:bg-slate-50 text-sm font-medium transition-colors"
                        >
                            <span class="sr-only">Previous</span>
                            <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                        </a>
                        <template v-for="(link, p) in numericLinks" :key="p">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :only="updateData"
                                :class="[
                                    link.active
                                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 border-y border-slate-200 min-w-[40px] h-9 inline-flex items-center justify-center text-sm font-medium whitespace-nowrap'
                                        : 'min-w-[40px] h-9 border-r border-slate-200 bg-white text-slate-700 hover:bg-slate-50 inline-flex items-center justify-center text-sm font-medium transition-colors whitespace-nowrap',
                                    link.isLastLabel ? 'min-w-[4rem] px-3' : ''
                                ]"
                            >
                                {{ link.label }}
                            </Link>
                            <span
                                v-else-if="link.ellipsis"
                                class="inline-flex items-center justify-center min-w-[40px] h-9 border-r border-slate-200 bg-white text-slate-400 text-sm"
                            >
                                ...
                            </span>
                        </template>
                        <a
                            v-if="nextLink && nextLink.url"
                            :href="nextLink.url"
                            class="relative inline-flex items-center justify-center min-w-[40px] h-9 bg-white text-slate-500 hover:bg-slate-50 text-sm font-medium transition-colors"
                        >
                            <span class="sr-only">Next</span>
                            <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                        </a>
                    </template>
            </nav>
            <!-- Go to page: when many pages -->
            <div v-if="showGoToPage" class="flex items-center gap-2 shrink-0">
                <label for="pagination-goto-input" class="text-sm text-slate-500 whitespace-nowrap">Go to</label>
                <input
                    id="pagination-goto-input"
                    v-model.number="goToPageInput"
                    type="number"
                    min="1"
                    :max="paginationPayload.last_page"
                    :aria-label="`Page number of ${paginationPayload.last_page}`"
                    class="w-16 h-9 px-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 text-center focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-300 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                    placeholder="1"
                    @keydown.enter="submitGoToPage"
                >
                <button
                    type="button"
                    class="h-9 px-3 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors whitespace-nowrap"
                    @click="submitGoToPage"
                >
                    Go
                </button>
            </div>
        </div>
        <!-- Bottom row: summary + page info, refined info-bar look -->
        <div class="flex items-center justify-end gap-3 sm:gap-4 w-full sm:w-auto flex-wrap">
            <p class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-sm">
                <span class="text-slate-500">Showing</span>
                <span class="font-semibold text-slate-800 tabular-nums">{{ displayFrom }}</span>
                <span class="text-slate-500">to</span>
                <span class="font-semibold text-slate-800 tabular-nums">{{ displayTo }}</span>
                <span class="text-slate-500">of</span>
                <span class="font-semibold text-slate-800 tabular-nums">{{ paginationPayload.total.toLocaleString() }}</span>
                <span class="text-slate-500">results</span>
            </p>
            <span class="shrink-0 inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50/80 border border-indigo-100 text-sm font-medium text-indigo-800 tabular-nums">
                Page {{ (paginationPayload.current_page ?? 1).toLocaleString() }} of {{ paginationPayload.last_page.toLocaleString() }}
            </span>
        </div>
    </div>
</template>

<script>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';
import { defineComponent } from 'vue';

/** Show footer when there are at least this many pages (default 1 = show whenever there is data). */
const MIN_PAGES_DEFAULT = 1;
/** When last_page exceeds this, show "Last" instead of the actual page number. */
const LARGE_PAGE_THRESHOLD = 100;

export default defineComponent({
    components: {
        ChevronLeftIcon,
        ChevronRightIcon,
        Link,
    },
    props: {
        paginationPayload: Object,
        updateData: {
            type: Array,
            default: () => [],
        },
        apiMode: {
            type: Boolean,
            default: false,
        },
        /** Only show this footer when there are at least this many pages (default 5). */
        minPages: {
            type: Number,
            default: MIN_PAGES_DEFAULT,
        },
    },
    computed: {
        showFooter() {
            const p = this.paginationPayload;
            if (!p) return false;
            const total = p.total ?? 0;
            const hasData = Array.isArray(p.data) && p.data.length > 0;
            const hasTotal = total > 0;
            if (!hasTotal && !hasData) return false;
            const lastPage = p.last_page ?? (hasTotal ? Math.max(1, Math.ceil(total / (p.per_page ?? 15))) : 1);
            return lastPage >= this.minPages;
        },
        displayFrom() {
            const p = this.paginationPayload;
            if (!p) return 0;
            if (p.from != null) return p.from.toLocaleString();
            const per = p.per_page ?? 15;
            const cur = p.current_page ?? 1;
            return Math.max(1, (cur - 1) * per + 1).toLocaleString();
        },
        displayTo() {
            const p = this.paginationPayload;
            if (!p) return 0;
            if (p.to != null) return p.to.toLocaleString();
            const per = p.per_page ?? 15;
            const cur = p.current_page ?? 1;
            const total = p.total ?? 0;
            return Math.min(cur * per, total).toLocaleString();
        },
        links() {
            return this.paginationPayload ? (this.paginationPayload.links || []) : [];
        },
        previousLink() {
            return this.links.length ? this.links[0] : null;
        },
        nextLink() {
            return this.links.length ? this.links[this.links.length - 1] : null;
        },
        /** For Inertia: numeric links + ellipsis. For very large last_page, show "Last" instead of huge numbers. */
        numericLinks() {
            const last = this.paginationPayload?.last_page ?? 1;
            const raw = this.links.filter(
                (l) => l.label !== '&laquo; Previous' && l.label !== 'Next &raquo;'
            );
            const useLastLabel = last > LARGE_PAGE_THRESHOLD;
            if (last <= 11) {
                return raw.map((l) => ({ ...l, ellipsis: false, isLastLabel: false }));
            }
            const out = [];
            if (raw[0]) out.push({ ...raw[0], ellipsis: false, isLastLabel: false });
            for (let i = 2; i <= 10 && raw[i - 1]; i++) {
                out.push({ ...raw[i - 1], ellipsis: false, isLastLabel: false });
            }
            out.push({ label: '...', url: null, active: false, ellipsis: true, isLastLabel: false });
            if (useLastLabel && raw[last - 1]) {
                out.push({ ...raw[last - 1], label: 'Last', ellipsis: false, isLastLabel: true });
            } else {
                if (last > 1 && raw[last - 2]) out.push({ ...raw[last - 2], ellipsis: false, isLastLabel: false });
                if (raw[last - 1]) out.push({ ...raw[last - 1], ellipsis: false, isLastLabel: false });
            }
            return out;
        },
        /** For apiMode: items to show. For very large last_page, show "Last" instead of huge numbers. */
        pageItems() {
            const cur = this.paginationPayload?.current_page ?? 1;
            const last = this.paginationPayload?.last_page ?? 1;
            const useLastLabel = last > LARGE_PAGE_THRESHOLD;
            const items = [];
            if (last <= 12) {
                for (let i = 1; i <= last; i++) {
                    items.push({ type: 'page', label: String(i), page: i, active: cur === i, isLastLabel: false });
                }
                return items;
            }
            for (let i = 1; i <= 10; i++) {
                items.push({ type: 'page', label: String(i), page: i, active: cur === i, isLastLabel: false });
            }
            items.push({ type: 'ellipsis' });
            if (useLastLabel) {
                items.push({ type: 'page', label: 'Last', page: last, active: cur === last, isLastLabel: true });
            } else {
                items.push({ type: 'page', label: String(last - 1), page: last - 1, active: cur === last - 1, isLastLabel: false });
                items.push({ type: 'page', label: String(last), page: last, active: cur === last, isLastLabel: false });
            }
            return items;
        },
        /** Show "Go to page" input whenever there are at least 2 pages so users can jump. */
        showGoToPage() {
            const last = this.paginationPayload?.last_page ?? 0;
            return last >= 2;
        },
    },
    data() {
        return { goToPageInput: null };
    },
    watch: {
        'paginationPayload.current_page': {
            handler(page) {
                if (page != null && this.showGoToPage) this.goToPageInput = page;
            },
            immediate: true,
        },
    },
    methods: {
        goToPage(page) {
            this.$emit('page-change', page);
        },
        submitGoToPage() {
            const last = this.paginationPayload?.last_page ?? 1;
            let num = Number(this.goToPageInput);
            if (Number.isNaN(num) || num < 1) num = 1;
            if (num > last) num = last;
            this.goToPageInput = num;
            this.goToPage(num);
        },
    },
});
</script>
