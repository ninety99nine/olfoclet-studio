<template>
    <div>
        <!-- Quick stats (8 insights) - ticking state isolated here so parent tooltips do not re-render -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4 mt-8 pt-6 border-t border-slate-100">
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Subscriptions</p>
                <p class="text-xl font-bold text-indigo-900 tabular-nums mt-0.5">{{ subscriber?.subscriptions_count ?? 0 }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Messages</p>
                <p class="text-xl font-bold text-indigo-900 tabular-nums mt-0.5">{{ subscriber?.messages_count ?? 0 }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">User billing</p>
                <p class="text-xl font-bold text-indigo-900 tabular-nums mt-0.5">{{ subscriber?.user_billing_transactions_count ?? 0 }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Auto billing</p>
                <p class="text-xl font-bold text-indigo-900 tabular-nums mt-0.5">{{ subscriber?.auto_billing_transactions_count ?? 0 }}</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-wider">Successful</p>
                <p class="text-xl font-bold text-emerald-900 tabular-nums mt-0.5">{{ successfulTransactionsCount }}</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                <p class="text-[10px] font-black text-amber-600 uppercase tracking-wider">Failed</p>
                <p class="text-xl font-bold text-amber-900 tabular-nums mt-0.5">{{ failedTransactionsCount }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Trial</p>
                <p class="text-lg font-bold text-indigo-900 tabular-nums mt-0.5">{{ trialLabel }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Active</p>
                <p class="text-xl font-bold text-indigo-900 tabular-nums mt-0.5">{{ hasActiveSubscription ? 'Yes' : 'No' }}</p>
            </div>
        </div>

        <!-- Next schedules (auto billing & SMS) -->
        <div class="grid sm:grid-cols-2 gap-4 mt-6">
            <div class="bg-indigo-50/80 rounded-xl p-4 border border-indigo-100">
                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-wider mb-2">Next auto billing</p>
                <template v-if="scheduleNextAutoBilling">
                    <p class="text-sm font-semibold text-indigo-900">
                        {{ formatScheduleDate(scheduleNextAutoBilling.at) }}
                    </p>
                    <p v-if="scheduleNextAutoBilling.pricing_plan_name" class="text-xs text-indigo-700 mt-0.5">{{ scheduleNextAutoBilling.pricing_plan_name }}</p>
                    <p class="text-xs text-indigo-600 mt-2 flex items-center gap-1.5">
                        <Clock :size="12" />
                        <Countdown v-if="scheduleNextAutoBillingMs > 0" :time="scheduleNextAutoBillingMs" />
                        <span v-else>Due</span>
                    </p>
                </template>
                <p v-else class="text-sm text-slate-500">None scheduled</p>
            </div>
            <div class="bg-violet-50/80 rounded-xl p-4 border border-violet-100">
                <p class="text-[10px] font-black text-violet-600 uppercase tracking-wider mb-2">Next SMS</p>
                <template v-if="scheduleNextSms">
                    <p class="text-sm font-semibold text-violet-900">
                        {{ formatScheduleDate(scheduleNextSms.at) }}
                    </p>
                    <p v-if="scheduleNextSms.sms_campaign_name" class="text-xs text-violet-700 mt-0.5">{{ scheduleNextSms.sms_campaign_name }}</p>
                    <p class="text-xs text-violet-600 mt-2 flex items-center gap-1.5">
                        <Clock :size="12" />
                        <Countdown v-if="scheduleNextSmsMs > 0" :time="scheduleNextSmsMs" />
                        <span v-else>Due</span>
                    </p>
                </template>
                <p v-else class="text-sm text-slate-500">None scheduled</p>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import moment from 'moment';
import { Clock } from 'lucide-vue-next';
import Countdown from '@/Partials/Countdown.vue';

export default defineComponent({
    name: 'SubscriberShowStats',
    components: { Clock, Countdown },
    props: {
        subscriber: { type: Object, default: null },
        scheduleNextAutoBilling: { type: Object, default: null },
        scheduleNextSms: { type: Object, default: null },
    },
    data() {
        return {
            trialLabel: '—',
            scheduleTick: 0,
            trialIntervalId: null,
            scheduleIntervalId: null,
        };
    },
    computed: {
        successfulTransactionsCount() {
            const u = this.subscriber?.successful_user_billing_transactions_count ?? 0;
            const a = this.subscriber?.successful_auto_billing_transactions_count ?? 0;
            return u + a;
        },
        failedTransactionsCount() {
            const u = this.subscriber?.unsuccessful_user_billing_transactions_count ?? 0;
            const a = this.subscriber?.unsuccessful_auto_billing_transactions_count ?? 0;
            return u + a;
        },
        hasActiveSubscription() {
            const sub = this.subscriber?.latest_subscription ?? this.subscriber?.latestSubscription;
            return !!(sub?.is_active);
        },
        scheduleNextAutoBillingMs() {
            this.scheduleTick;
            if (!this.scheduleNextAutoBilling?.at) return 0;
            const ms = new Date(this.scheduleNextAutoBilling.at).getTime() - Date.now();
            return Math.max(0, ms);
        },
        scheduleNextSmsMs() {
            this.scheduleTick;
            if (!this.scheduleNextSms?.at) return 0;
            const ms = new Date(this.scheduleNextSms.at).getTime() - Date.now();
            return Math.max(0, ms);
        },
    },
    mounted() {
        this.updateTrialLabel();
        this.trialIntervalId = setInterval(() => this.updateTrialLabel(), 1000);
        this.scheduleIntervalId = setInterval(() => { this.scheduleTick += 1; }, 1000);
    },
    beforeUnmount() {
        if (this.trialIntervalId) {
            clearInterval(this.trialIntervalId);
            this.trialIntervalId = null;
        }
        if (this.scheduleIntervalId) {
            clearInterval(this.scheduleIntervalId);
            this.scheduleIntervalId = null;
        }
    },
    methods: {
        formatScheduleDate(isoString) {
            if (!isoString) return '—';
            return moment(isoString).format('DD MMM YYYY HH:mm');
        },
        updateTrialLabel() {
            const sub = this.subscriber?.latest_subscription ?? this.subscriber?.latestSubscription;
            const endAt = sub?.end_at;
            if (!endAt) {
                this.trialLabel = '—';
                return;
            }
            const end = moment(endAt);
            const now = moment();
            if (end.isSameOrBefore(now)) {
                this.trialLabel = 'Trial ended';
                return;
            }
            const diffMs = end.diff(now);
            const days = Math.floor(diffMs / (24 * 60 * 60 * 1000));
            const hours = Math.floor(diffMs / (60 * 60 * 1000));
            const minutes = Math.floor(diffMs / (60 * 1000));
            const seconds = Math.floor(diffMs / 1000);
            if (days >= 1) {
                this.trialLabel = `${days} day${days !== 1 ? 's' : ''} left`;
            } else if (hours >= 1) {
                this.trialLabel = `${hours} hour${hours !== 1 ? 's' : ''} left`;
            } else if (minutes >= 1) {
                this.trialLabel = `${minutes} min left`;
            } else {
                this.trialLabel = `${seconds} sec left`;
            }
        },
    },
});
</script>
