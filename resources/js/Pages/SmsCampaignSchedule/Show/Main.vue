<template>
    <app-layout title="SMS Schedule">
        <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
            <div class="max-w-[1400px] mx-auto space-y-6">
                <!-- Back + breadcrumb -->
                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        :href="route('show.sms.campaign.schedules', { project: project?.id })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all"
                    >
                        <ArrowLeft :size="14" />
                        Back
                    </Link>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <Link
                            :href="route('show.sms.campaign.schedules', { project: project?.id })"
                            class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold hover:underline"
                        >
                            SMS Schedules
                        </Link>
                    </span>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <span class="text-slate-600 text-xs font-medium truncate max-w-[200px]">
                            Schedule #{{ schedule?.id ?? '—' }}
                        </span>
                    </span>
                </div>

                <!-- Header card -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 lg:p-8">
                        <div class="flex items-start gap-4">
                            <div class="h-14 w-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                <MessageSquare :size="24" class="text-indigo-600" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-black tracking-tight text-indigo-950">
                                    SMS Schedule #{{ schedule?.id ?? '—' }}
                                </h1>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">
                                    ID {{ schedule?.id ?? '—' }}
                                </p>
                                <p class="mt-2 text-sm text-slate-600">
                                    Next message: {{ schedule?.next_message_date ? moment(schedule.next_message_date).format('DD MMM YYYY HH:mm') : '—' }}
                                    <span v-if="schedule?.next_message_date_milli_seconds_left != null && schedule.next_message_date_milli_seconds_left > 0" class="text-slate-500 ml-1">
                                        (<Countdown :time="schedule.next_message_date_milli_seconds_left" />)
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Key metrics -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-slate-100">
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Next message</p>
                                <p class="text-sm font-bold text-indigo-900 mt-0.5">
                                    {{ schedule?.next_message_date ? moment(schedule.next_message_date).format('DD MMM YYYY HH:mm') : '—' }}
                                </p>
                                <p v-if="schedule?.next_message_date_milli_seconds_left != null && schedule.next_message_date_milli_seconds_left > 0" class="text-[10px] text-slate-500 mt-0.5">
                                    <Countdown :time="schedule.next_message_date_milli_seconds_left" />
                                </p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Messages sent</p>
                                <p class="text-sm font-bold text-indigo-900 mt-0.5 tabular-nums">{{ schedule?.attempts ?? '—' }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Successful</p>
                                <p class="text-sm font-bold text-emerald-600 mt-0.5 tabular-nums">{{ (schedule?.total_successful_attempts ?? 0).toLocaleString() }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Failed</p>
                                <p class="text-sm font-bold text-rose-600 mt-0.5 tabular-nums">{{ (schedule?.total_failed_attempts ?? 0).toLocaleString() }}</p>
                            </div>
                        </div>

                        <!-- Campaign -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Campaign</p>
                            <Link
                                v-if="schedule?.sms_campaign && project?.id"
                                :href="route('show.sms.campaign', { project: project.id, sms_campaign: schedule.sms_campaign.id })"
                                class="block rounded-xl border border-slate-200 bg-slate-50/80 p-4 hover:bg-indigo-50/20 hover:border-indigo-100 transition-all group"
                            >
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-bold text-indigo-950 group-hover:text-indigo-700">{{ schedule.sms_campaign?.name ?? '—' }}</p>
                                        <Tag
                                            :value="schedule.sms_campaign?.can_send_messages === true ? 'Active' : 'Inactive'"
                                            :severity="schedule.sms_campaign?.can_send_messages === true ? 'success' : 'warn'"
                                            :class="['text-xs scale-90 origin-left mt-1', { 'tag-amber': schedule.sms_campaign?.can_send_messages !== true }]"
                                        />
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest group-hover:text-indigo-600">View campaign →</span>
                                </div>
                            </Link>
                            <p v-else class="text-sm text-slate-400">No campaign linked</p>
                        </div>

                        <!-- Subscriber -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Subscriber</p>
                            <Link
                                v-if="schedule?.subscriber && project?.id"
                                :href="route('show.subscriber', { project: project.id, subscriber: schedule.subscriber.id })"
                                class="block w-full"
                            >
                                <span class="inline-flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100 hover:bg-indigo-50 hover:border-indigo-100 transition-all group w-full">
                                    <div class="h-10 w-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0">
                                        <User :size="18" class="text-xs" />
                                    </div>
                                    <div class="text-left min-w-0 flex-1">
                                        <div class="text-sm font-bold text-indigo-950 group-hover:text-indigo-700">{{ schedule.subscriber?.msisdn ?? '—' }}</div>
                                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Subscriber #{{ schedule.subscriber?.id ?? '—' }} · View subscriber →</div>
                                    </div>
                                </span>
                            </Link>
                            <p v-else class="text-sm text-slate-400">No subscriber linked</p>
                        </div>

                        <!-- Schedule metadata -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Schedule details</p>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Schedule ID</p>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ schedule?.id ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Subscriber ID</p>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ schedule?.subscriber_id ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Campaign ID</p>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ schedule?.sms_campaign_id ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Project ID</p>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ schedule?.project_id ?? '—' }}</p>
                                </div>
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
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Tag from 'primevue/tag';
import moment from 'moment';
import { ArrowLeft, ChevronRight, User, MessageSquare } from 'lucide-vue-next';
import Countdown from '@/Partials/Countdown.vue';

export default defineComponent({
    name: 'SmsCampaignScheduleShow',
    components: {
        AppLayout,
        Link,
        Tag,
        Countdown,
        ArrowLeft,
        ChevronRight,
        User,
        MessageSquare,
    },
    props: {
        smsCampaignSchedule: { type: Object, required: true },
        project: { type: Object, required: true },
    },
    setup(props) {
        const schedule = computed(() => props.smsCampaignSchedule ?? {});
        return { schedule };
    },
    data() {
        return { moment };
    },
});
</script>

<style scoped>
.tag-amber.p-tag {
    background: #fef3c7;
    color: #92400e;
    border: none;
    font-weight: 600;
}
</style>
