<template>

    <div>

        <div class="mb-6 gap-4">

            <div class="bg-gray-50 pt-3 pl-6 border-b rounded-t">

                <div class="text-2xl font-semibold leading-6 text-gray-500 mb-4">{{ smsCampaign.name }}</div>

                    <el-breadcrumb separator=">" class="mb-4">
                        <el-breadcrumb-item @click="nagivateToSmsCampaign()">
                            <span class="hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer">SMS Campaigns</span>
                        </el-breadcrumb-item>

                        <el-breadcrumb-item @click="nagivateToSmsCampaign(smsCampaign)">
                            <span class="hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer">{{ smsCampaign.name }}</span>
                        </el-breadcrumb-item>
                    </el-breadcrumb>

                <jet-secondary-button @click="goBackToPreviousPage()" class="py-1 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                    </svg>
                    <span class="ml-2">Go Back</span>
                </jet-secondary-button>

            </div>

            <div class="bg-gray-50 border-b pl-6 py-3 rounded-t text-gray-500 text-sm">

                <!-- Description -->
                <span>{{ smsCampaign.description }}</span>

            </div>

            <div class="bg-gray-50 border-b pl-6 py-3 rounded-t text-gray-500 text-sm mb-6">

                <!-- Schedule Type -->
                <span>{{ smsCampaign.schedule_type }}</span>

                <!-- Recurring Schedule -->
                <span v-if="smsCampaign.schedule_type == 'Send Recurring'">
                    <span class="text-gray-300 mx-4">|</span>
                    <span>Every {{ smsCampaign.recurring_duration }} {{ recurringFrequencyWord }}</span>
                    <span class="text-gray-300 mx-4">|</span>
                    <span><span class="text-gray-400">Start: </span>{{ moment(smsCampaign.start_datetime).format('lll') }}</span>
                    <span class="text-gray-300 mx-4">|</span>
                    <span><span class="text-gray-400">End: </span>{{ moment(smsCampaign.end_datetime).format('lll') }}</span>
                </span>

            </div>

        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

            <!-- Table -->
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Name</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Status</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                <span>Total</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                <span>Pending</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                <span>Processed</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                <span>Progress</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Sprint Date</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="smsCampaignBatchJob in smsCampaignBatchJobsPayload.data" :key="smsCampaignBatchJob.id">
                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ smsCampaignBatchJob.name }}</div>
                                </td>
                                <!-- Status -->
                                <td class="px-6 py-4">
                                    <SmsCampaignStatusBadge :smsCampaignBatchJob="smsCampaignBatchJob"></SmsCampaignStatusBadge>
                                </td>
                                <!-- Total -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <div class="text-sm text-gray-900">{{ smsCampaignBatchJob.total_jobs }}</div>
                                </td>
                                <!-- Pending -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <div class="text-sm text-gray-900">{{ smsCampaignBatchJob.pending_jobs }}</div>
                                </td>
                                <!-- Processed -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <div class="text-sm text-gray-900">{{ smsCampaignBatchJob.processed_jobs }}</div>
                                </td>
                                <!-- Progress -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <span v-if="smsCampaignBatchJob.progress == 100" class="text-lg text-green-600">{{ smsCampaignBatchJob.progress }} {{ smsCampaignBatchJob.progress ? '%' : '' }}</span>
                                    <div v-else class="w-full bg-gray-200 rounded-full">
                                        <div class="bg-green-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" :style="'width: '+smsCampaignBatchJob.progress+'%'"> {{ smsCampaignBatchJob.progress }}%</div>
                                    </div>
                                </td>
                                <!-- Sprint Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ smsCampaignBatchJob.created_at == null ? '...' : moment(smsCampaignBatchJob.created_at).format('lll') }}
                                </td>
                            </tr>

                            <tr v-if="smsCampaignBatchJobsPayload.data.length == 0">
                                <!-- Content -->
                                <td :colspan="7" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-center text-gray-900 text-sm p-6">No sms campaign sprints</div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="smsCampaignBatchJobsPayload" :updateData="['smsCampaignBatchJobsPayload']" />

        </div>

    </div>

</template>
<script>

    import Pagination from '../../../../../../Partials/Pagination.vue'
    import JetSecondaryButton from '@/Components/SecondaryButton.vue'
    import SmsCampaignStatusBadge from './SmsCampaignStatusBadge.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            Pagination, SmsCampaignStatusBadge, JetSecondaryButton
        },
        props: {
            smsCampaign: Object,
            smsCampaignBatchJobsPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                moment: moment
            }
        },
        computed: {
            recurringFrequencyWord(){
                if(this.smsCampaign.recurring_duration == 1) {

                    if( this.smsCampaign.recurring_frequency == 'Years' ){

                        return 'Year';

                    }else if( this.smsCampaign.recurring_frequency == 'Months' ){

                        return 'Month';

                    }else if( this.smsCampaign.recurring_frequency == 'Weeks' ){

                        return 'Week';

                    }else if( this.smsCampaign.recurring_frequency == 'Days' ){

                        return 'Day';

                    }else if( this.smsCampaign.recurring_frequency == 'Hours' ){

                        return 'Hour';

                    }else if( this.smsCampaign.recurring_frequency == 'Minutes' ){

                        return 'Minute';

                    }else{

                        return '';

                    }

                }else{

                    //  This returns Years, Months, Weeks, Days, Hours or Minutes
                    return this.smsCampaign.recurring_frequency;

                }
            },
        },
        methods: {
            refreshContent()
            {
                this.$inertia.reload();
            },
            nagivateToSmsCampaign(smsCampaign = null){
                if( smsCampaign ){

                    this.$inertia.get(route('show.sms.campaign.job.batches', { project: route().params.project, sms_campaign: smsCampaign.id }))

                }else{

                    this.$inertia.get(route('show.sms.campaigns', { project: route().params.project }));

                }
            },
            goBackToPreviousPage(){
                window.history.back();
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
