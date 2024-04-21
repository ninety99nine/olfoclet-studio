<template>

    <div>

        <div class="grid grid-cols-12 gap-4">

            <div class="col-span-6">
                <div class="bg-gray-50 border-b px-6 py-4 rounded-t text-gray-500 text-sm mb-4">
                    <div class="text-2xl font-semibold leading-6 text-gray-500">Sms Campaign Schedules</div>
                </div>
            </div>

        </div>

        <div class="bg-white shadow-xl sm:rounded-lg">

            <!-- Table -->
            <div class="flex flex-col overflow-y-auto">
                <div class="align-middle inline-block min-w-full">
                    <div class="shadow border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">

                                <tr>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300">

                                    </th>
                                    <th scope="col" colspan="7" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <div class="font-bold text-teal-500">NEXT MESSAGE SCHEDULE</div>
                                    </th>
                                    <th scope="col" colspan="9" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <div class="font-bold text-fuchsia-500">SMS CAMPAIGN</div>
                                    </th>
                                </tr>

                                <tr>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300">
                                        <span>Mobile</span>
                                    </th>




                                    <!-- Next Message Date -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Next Message Date</span>
                                    </th>
                                    <!-- Next Message Countdown -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Countdown</span>
                                    </th>
                                    <!-- Attempts -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Attempts</span>
                                    </th>
                                    <!-- Total Successful Attempts -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Total Successful Attempts</span>
                                    </th>
                                    <!-- Total Failed Attempts -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Total Failed Attempts</span>
                                    </th>
                                    <!-- Created Date -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Created Date</span>
                                    </th>
                                    <!-- Updated Date -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <span>Updated Date</span>
                                    </th>




                                    <!-- Sms Campaign Name -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Name</span>
                                    </th>
                                    <!-- Sms Campaign Send Messages -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Send Messages</span>
                                    </th>
                                    <!-- Sms Campaign Status -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Status</span>
                                    </th>
                                    <!-- Sms Campaign Sprints -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Sprints</span>
                                    </th>
                                    <!-- Sms Campaign Total -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100 border-l border-t border-dotted border-fuchsia-300">
                                        <span>Total</span>
                                    </th>
                                    <!-- Sms Campaign Pending -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100 border-t border-dotted border-fuchsia-300">
                                        <span>Pending</span>
                                    </th>
                                    <!-- Sms Campaign Processed -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100 border-t border-dotted border-fuchsia-300">
                                        <span>Processed</span>
                                    </th>
                                    <!-- Sms Campaign Progress -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100 border-r border-t border-dotted border-fuchsia-300">
                                        <span>Progress</span>
                                    </th>
                                    <!-- Sms Campaign Last Sprint Date -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Last Sprint Date</span>
                                    </th>

                                </tr>

                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">

                                <tr v-for="smsCampaignSchedule in smsCampaignSchedulesPayload.data" :key="smsCampaignSchedule.id">

                                    <!-- Mobile -->
                                    <td class="px-6 py-3 whitespace-nowrap border-r border-dotted border-r-teal-300">
                                        <div class="text-sm text-gray-900">{{ smsCampaignSchedule.subscriber.msisdn }}</div>
                                    </td>




                                    <!-- Next Attempt Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        {{ smsCampaignSchedule.next_message_date == null ? '...' : moment(smsCampaignSchedule.next_message_date).format('lll') }}
                                    </td>
                                    <!-- Next Attempt Countdown -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        <vue-countdown :time="smsCampaignSchedule.next_message_date_milli_seconds_left" v-slot="{ days, hours, minutes, seconds }">

                                            <span v-if="days > 0">{{ days }} {{ days == 1 ? 'day ' : 'days '}}</span>
                                            <span v-if="hours > 0">{{ hours }} {{ hours == 1 ? 'hr ' : 'hrs '}}</span>
                                            <span v-if="days == 0 && minutes > 0">{{ minutes }} {{ minutes == 1 ? 'min ' : 'mins '}}</span>
                                            <span v-if="hours == 0 && seconds > 0">{{ seconds }} {{ seconds == 1 ? 'sec ' : 'secs '}}</span>

                                            <span v-if="days == 0 && hours == 0 && minutes == 0 && seconds == 0">Ended</span>

                                        </vue-countdown>
                                    </td>
                                    <!-- Attempts -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        {{ smsCampaignSchedule.attempts == null ? '...' : smsCampaignSchedule.attempts }}
                                    </td>
                                    <!-- Total Successful Attempts -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        {{ smsCampaignSchedule.total_successful_attempts == null ? '...' : smsCampaignSchedule.total_successful_attempts }}
                                    </td>
                                    <!-- Total Failed Attempts -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        {{ smsCampaignSchedule.total_failed_attempts == null ? '...' : smsCampaignSchedule.total_failed_attempts }}
                                    </td>
                                    <!-- Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        {{ smsCampaignSchedule.created_at == null ? '...' : moment(smsCampaignSchedule.created_at).format('lll') }}
                                    </td>
                                    <!-- Updated Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        {{ smsCampaignSchedule.updated_at == null ? '...' : moment(smsCampaignSchedule.updated_at).format('lll') }}
                                    </td>





                                    <!-- Name -->
                                    <td class="px-6 py-3 whitespace-nowrap bg-fuchsia-50">
                                        <div class="text-sm text-gray-900">{{ smsCampaignSchedule.sms_campaign.name }}</div>
                                    </td>
                                    <!-- Send SMS -->
                                    <td class="px-6 py-3 bg-fuchsia-50">
                                        <SmsCampaignCanSendSmsBadge :smsCampaign="smsCampaignSchedule.sms_campaign"></SmsCampaignCanSendSmsBadge>
                                    </td>
                                    <!-- Status -->
                                    <td class="px-6 py-3 bg-fuchsia-50">
                                        <SmsCampaignStatusBadge :smsCampaignBatchJob="getLatestSmsCampaignBatchJob(smsCampaignSchedule.sms_campaign)"></SmsCampaignStatusBadge>
                                    </td>
                                    <!-- Sprints -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                        <div class="text-sm text-gray-900">{{ smsCampaignSchedule.sms_campaign.sms_campaign_batch_jobs_count }}</div>
                                    </td>
                                    <!-- Total -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-100 border-l border-dotted border-fuchsia-300">
                                        <div class="text-sm text-gray-900">{{ getLatestSmsCampaignBatchJob(smsCampaignSchedule.sms_campaign).total_jobs }}</div>
                                    </td>
                                    <!-- Pending -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-100">
                                        <div class="text-sm text-gray-900">{{ getLatestSmsCampaignBatchJob(smsCampaignSchedule.sms_campaign).pending_jobs }}</div>
                                    </td>
                                    <!-- Processed -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-100">
                                        <div class="text-sm text-gray-900">{{ getLatestSmsCampaignBatchJob(smsCampaignSchedule.sms_campaign).processed_jobs }}</div>
                                    </td>
                                    <!-- Progress -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-100 border-r border-dotted border-fuchsia-300">
                                        <span class="text-lg text-green-600">{{ getLatestSmsCampaignBatchJob(smsCampaignSchedule.sms_campaign).progress }} {{ getLatestSmsCampaignBatchJob(smsCampaignSchedule.sms_campaign).progress ? '%' : '' }}</span>
                                    </td>
                                    <!-- Last Sprint Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 bg-fuchsia-50">
                                        {{ getLatestSmsCampaignBatchJob(smsCampaignSchedule.sms_campaign).created_at == null ? '...' : moment(getLatestSmsCampaignBatchJob(smsCampaignSchedule.sms_campaign).created_at).format('lll') }}
                                    </td>

                                </tr>

                                <tr v-if="smsCampaignSchedulesPayload.data.length == 0">

                                    <!-- Content -->
                                    <td :colspan="8" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No sms campaign schedules</div>
                                    </td>
                                    <td :colspan="8" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No sms campaign schedules</div>
                                    </td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="smsCampaignSchedulesPayload" :updateData="['smsCampaignSchedulesPayload']" />

        </div>

    </div>

</template>
<script>
    import SmsCampaignCanSendSmsBadge from './../../../SmsCampaigns/List/JobBatches/List/Partials/SmsCampaignCanSendSmsBadge.vue';
    import SmsCampaignStatusBadge from './../../../SmsCampaigns/List/JobBatches/List/Partials/SmsCampaignStatusBadge.vue';
    import Pagination from '../../../../Partials/Pagination.vue';
    import VueCountdown from '@chenfengyuan/vue-countdown';
    import { defineComponent } from 'vue';
    import moment from "moment";

    export default defineComponent({
        components: {
            SmsCampaignCanSendSmsBadge, SmsCampaignStatusBadge, Pagination, VueCountdown,
        },
        props: {
            smsCampaignSchedulesPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                moment: moment
            }
        },
        methods: {
            getLatestSmsCampaignBatchJob(smsCampaign)
            {
                if( smsCampaign.latest_sms_campaign_batch_job.length ) {
                    return smsCampaign.latest_sms_campaign_batch_job[0];
                }
                return {};
            },
            refreshContent()
            {
                this.$inertia.reload();
            },
            cleanUp()
            {
                clearInterval( this.refreshContentInterval );
                this.refreshContentInterval = null;
            }
        },
        created() {

            //  Keep refreshing this page content every 5 seconds
            this.refreshContentInterval = setInterval(function() {
                this.refreshContent();
            }.bind(this), 5000);
        },
        unmounted() {
            this.cleanUp()
        }
    })
</script>
