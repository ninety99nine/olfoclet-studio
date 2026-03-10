<template>
    <app-layout title="SMS Message">
        <div class="min-h-screen bg-slate-50/50 p-4 lg:p-5 font-sans antialiased text-slate-700">
            <div class="max-w-4xl mx-auto space-y-4">
                <!-- Back + breadcrumb (same pattern as Topics / Messages) -->
                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        :href="route('show.subscriber.messages', { project: projectId })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all"
                    >
                        <ArrowLeft :size="14" />
                        Back
                    </Link>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <Link
                            :href="route('show.subscriber.messages', { project: projectId })"
                            class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold hover:underline"
                        >
                            SMS Messages
                        </Link>
                    </span>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <span class="text-slate-600 text-xs font-medium truncate" :title="messageTitle">{{ messageTitle }}</span>
                    </span>
                </div>

                <!-- Card -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 lg:p-5">
                        <!-- Header row: icon, title, type, date, badge -->
                        <div class="flex flex-wrap items-center gap-3 gap-y-1">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                <MessageSquare :size="20" class="text-indigo-600" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h1 class="text-xl font-black tracking-tight text-indigo-950">Message #{{ subscriberMessage?.id ?? '—' }}</h1>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ formatType(subscriberMessage?.type) }}
                                    <span v-if="subscriberMessage?.created_at" class="text-slate-400"> · {{ moment(subscriberMessage.created_at).format('DD MMM YYYY [at] HH:mm') }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Content (compact) -->
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Content</p>
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                <p class="text-sm text-slate-800 whitespace-pre-wrap break-words">{{ subscriberMessage?.content ?? '—' }}</p>
                                <p v-if="subscriberMessage?.character_count != null" class="text-[10px] text-slate-400 mt-1">{{ subscriberMessage.character_count }} characters</p>
                            </div>
                        </div>

                        <!-- Send status + Delivery side by side -->
                        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Send status</p>
                                <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                    <SubscriberMessageStatusBadge :subscriber-message="subscriberMessage" class="scale-90 origin-left" />
                                </div>
                                <template v-if="subscriberMessage && subscriberMessage.is_successful === false">
                                    <div v-if="subscriberMessage.failure_type" class="mt-2 bg-amber-50 rounded-lg p-2.5 border border-amber-100">
                                        <p class="text-[10px] font-black text-amber-700 uppercase tracking-wider">Failure type</p>
                                        <p class="text-xs text-amber-900 mt-0.5">{{ subscriberMessage.failure_type }}</p>
                                    </div>
                                    <div v-if="subscriberMessage.failure_reason" class="mt-2 bg-amber-50 rounded-lg p-2.5 border border-amber-100">
                                        <p class="text-[10px] font-black text-amber-700 uppercase tracking-wider">Failure reason</p>
                                        <p class="text-xs text-amber-900 whitespace-pre-wrap mt-0.5">{{ subscriberMessage.failure_reason }}</p>
                                    </div>
                                </template>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Delivery</p>
                                <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                    <SubscriberMessageDeliveryStatusBadge :subscriber-message="subscriberMessage" class="scale-90 origin-left" />
                                </div>
                                <p v-if="subscriberMessage && subscriberMessage.delivery_status_update_is_successful == null && subscriberMessage.delivery_status == null" class="text-xs text-slate-500 mt-1.5">Delivery status not yet updated.</p>
                                <template v-if="subscriberMessage && subscriberMessage.delivery_status_update_is_successful === false">
                                    <div v-if="subscriberMessage.delivery_status_update_failure_type" class="mt-2 bg-amber-50 rounded-lg p-2.5 border border-amber-100">
                                        <p class="text-[10px] font-black text-amber-700 uppercase tracking-wider">Update failure type</p>
                                        <p class="text-xs text-amber-900 mt-0.5">{{ subscriberMessage.delivery_status_update_failure_type }}</p>
                                    </div>
                                    <div v-if="subscriberMessage.delivery_status_update_failure_reason" class="mt-2 bg-amber-50 rounded-lg p-2.5 border border-amber-100">
                                        <p class="text-[10px] font-black text-amber-700 uppercase tracking-wider">Update failure reason</p>
                                        <p class="text-xs text-amber-900 whitespace-pre-wrap mt-0.5">{{ subscriberMessage.delivery_status_update_failure_reason }}</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Timeline + Endpoints -->
                        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 lg:grid-cols-2 gap-3">
                            <div class="bg-slate-50 rounded-xl border border-slate-100 p-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Timeline</p>
                                <div class="space-y-2">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-700">Created</p>
                                            <p class="text-[10px] text-slate-400">When the record was created</p>
                                        </div>
                                        <p class="text-xs font-mono text-slate-700 whitespace-nowrap">{{ formatDateTime(subscriberMessage?.created_at) }}</p>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-700">Sent</p>
                                            <p class="text-[10px] text-slate-400">When we attempted to send to the provider</p>
                                        </div>
                                        <p class="text-xs font-mono text-slate-700 whitespace-nowrap">{{ formatDateTime(subscriberMessage?.sent_at) }}</p>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-700">Delivery checked</p>
                                            <p class="text-[10px] text-slate-400">Last attempt to confirm delivery</p>
                                        </div>
                                        <p class="text-xs font-mono text-slate-700 whitespace-nowrap">{{ formatDateTime(subscriberMessage?.delivery_status_checked_at) }}</p>
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-700">Last updated</p>
                                            <p class="text-[10px] text-slate-400">Last DB update for this message</p>
                                        </div>
                                        <p class="text-xs font-mono text-slate-700 whitespace-nowrap">{{ formatDateTime(subscriberMessage?.updated_at) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-xl border border-slate-100 p-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Endpoints</p>

                                <div class="space-y-3">
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-1">
                                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Send endpoint</p>
                                            <button
                                                v-if="subscriberMessage?.send_endpoint"
                                                type="button"
                                                @click="copyToClipboard('send', subscriberMessage.send_endpoint)"
                                                class="text-[10px] font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest"
                                            >
                                                {{ copiedKey === 'send' ? 'Copied!' : 'Copy' }}
                                            </button>
                                        </div>
                                        <p class="text-xs text-slate-700 font-mono break-all bg-white rounded-lg px-3 py-2 border border-slate-100">
                                            {{ subscriberMessage?.send_endpoint ?? '—' }}
                                        </p>
                                    </div>

                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-1">
                                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Delivery status endpoint</p>
                                            <button
                                                v-if="subscriberMessage?.delivery_status_endpoint"
                                                type="button"
                                                @click="copyToClipboard('delivery', subscriberMessage.delivery_status_endpoint)"
                                                class="text-[10px] font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest"
                                            >
                                                {{ copiedKey === 'delivery' ? 'Copied!' : 'Copy' }}
                                            </button>
                                        </div>
                                        <p class="text-xs text-slate-700 font-mono break-all bg-white rounded-lg px-3 py-2 border border-slate-100">
                                            {{ subscriberMessage?.delivery_status_endpoint ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subscriber + Message template side by side -->
                        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Subscriber</p>
                                <Link
                                    v-if="subscriberMessage?.subscriber"
                                    :href="route('show.subscriber', { project: projectId, subscriber: subscriberMessage.subscriber.id })"
                                    class="flex items-center gap-2.5 p-3 rounded-lg bg-slate-50 border border-slate-100 hover:bg-indigo-50 hover:border-indigo-100 transition-all group"
                                >
                                    <div class="h-9 w-9 rounded-lg bg-white border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0">
                                        <User :size="16" class="text-xs" />
                                    </div>
                                    <div class="min-w-0 text-left">
                                        <div class="text-sm font-bold text-indigo-950 group-hover:text-indigo-700 truncate">{{ subscriberMessage.subscriber?.msisdn ?? '—' }}</div>
                                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">#{{ subscriberMessage.subscriber?.id }} · View →</div>
                                    </div>
                                </Link>
                                <p v-else class="text-xs text-slate-400 py-2">No subscriber linked</p>
                            </div>
                            <div v-if="subscriberMessage?.message">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Message template</p>
                                <Link
                                    :href="route('show.message', { project: projectId, message: subscriberMessage.message.id })"
                                    class="flex items-center gap-2.5 p-3 rounded-lg bg-slate-50 border border-slate-100 hover:bg-indigo-50 hover:border-indigo-100 transition-all group"
                                >
                                    <div class="min-w-0 text-left">
                                        <div class="text-sm font-bold text-indigo-950 group-hover:text-indigo-700">Message #{{ subscriberMessage.message.id }}</div>
                                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">View →</div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent, computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import moment from 'moment';
import { MessageSquare, ArrowLeft, ChevronRight, User } from 'lucide-vue-next';
import SubscriberMessageStatusBadge from '../List/Partials/SubscriberMessageStatusBadge.vue';
import SubscriberMessageDeliveryStatusBadge from '../List/Partials/SubscriberMessageDeliveryStatusBadge.vue';

export default defineComponent({
    components: {
        AppLayout,
        Link,
        MessageSquare,
        ArrowLeft,
        ChevronRight,
        User,
        SubscriberMessageStatusBadge,
        SubscriberMessageDeliveryStatusBadge,
    },
    props: {
        subscriberMessage: { type: Object, default: null },
    },
    setup(props) {
        const copiedKey = ref(null);
        const copiedTimer = ref(null);

        const projectId = computed(() => {
            try {
                return route().params.project;
            } catch {
                return null;
            }
        });
        const messageTitle = computed(() => {
            const m = props.subscriberMessage;
            if (!m) return 'Message';
            return `#${m.id} · ${m.subscriber?.msisdn ?? '—'}`;
        });
        return { copiedKey, copiedTimer, projectId, messageTitle, moment };
    },
    methods: {
        formatType(type) {
            if (!type) return '—';
            const map = {
                Content: 'Content',
                PaymentConfirmation: 'Payment confirmation',
                AutoBillingReminder: 'Auto billing reminder',
                AutoBillingDisabled: 'Auto billing disabled',
            };
            return map[type] ?? type;
        },
        formatDateTime(value) {
            if (!value) return '—';
            return moment(value).format('DD MMM YYYY [at] HH:mm');
        },
        async copyToClipboard(key, text) {
            try {
                if (this.copiedTimer) clearTimeout(this.copiedTimer);
                await navigator.clipboard.writeText(String(text));
                this.copiedKey = key;
                this.copiedTimer = setTimeout(() => {
                    if (this.copiedKey === key) this.copiedKey = null;
                }, 2500);
            } catch {
                // no-op: clipboard permissions may be blocked by the browser context
            }
        },
    },
});
</script>

<style>
.p-tag.p-tag-warn,
.p-tag-warn,
.tag-amber,
.p-tag.tag-amber {
    background: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fcd34d !important;
}
</style>
