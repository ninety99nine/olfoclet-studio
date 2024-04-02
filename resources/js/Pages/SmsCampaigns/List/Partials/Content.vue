<template>

    <div>

        <manage-sms-campaign-modal v-model="isShowingModal" :action="modalAction" :smsCampaign="smsCampaign" :subscriptionPlans="subscriptionPlans" :contentToSendOptions="contentToSendOptions" :scheduleTypeOptions="scheduleTypeOptions" />

        <div class="bg-gray-50 border-b px-6 py-4 rounded-t text-gray-500 text-sm mb-4">

            <div class="text-2xl font-semibold leading-6 text-gray-500 border-b pb-4 mb-4">Sms Campaigns</div>

            <div class="flex items-center">

                <!-- Note -->
                <span v-if="projectPayload.can_send_messages" class="text-gray-400"><span class="text-green-500 font-bold">Sending messages is enabled</span> — You can turn off "Send messages" in settings (This affects all sms campaigns)</span>
                <span v-else class="text-gray-400"><span class="text-red-500 font-bold">Sending messages is disabled</span> — You can turn on "Send messages" in settings</span>

                <el-popover :width="400">
                    <template #reference>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </template>
                    <template #default>
                        <span v-if="projectPayload.can_send_messages" class="break-normal">
                            Turning off "Send Messages" from the project settings means that every sms campaign won't be able to send messages even if the sms campaign "Send Messages" option is turned on
                            <hr class="my-4">
                            After turning off "Send Messages" from the project settings, any running sms campaigns will complete their last sprint before completely stopping to send messages.
                        </span>
                        <span v-else class="break-normal">
                            Turning on "Send Messages" from the project settings means that every sms campaign will be able to send messages as long as the sms campaign "Send Messages" option is turned on
                        </span>
                    </template>
                </el-popover>

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
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Name</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Send Messages</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Status</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Sprints</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                    <span>Total</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                    <span>Pending</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                    <span>Processed</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                    <span>Progress</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Last Sprint Date</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="smsCampaign in smsCampaignsPayload.data" :key="smsCampaign.id">
                                    <!-- Name -->
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ smsCampaign.name }}</div>
                                    </td>
                                    <!-- Send SMS -->
                                    <td class="px-6 py-3">
                                        <SmsCampaignCanSendSmsBadge :smsCampaign="smsCampaign"></SmsCampaignCanSendSmsBadge>
                                    </td>
                                    <!-- Status -->
                                    <td class="px-6 py-3">
                                        <SmsCampaignStatusBadge :smsCampaignBatchJob="getLatestSmsCampaignBatchJob(smsCampaign)"></SmsCampaignStatusBadge>
                                    </td>
                                    <!-- Sprints -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <div class="text-sm text-gray-900">{{ smsCampaign.sms_campaign_batch_jobs_count }}</div>
                                    </td>
                                    <!-- Total -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                        <div class="text-sm text-gray-900">{{ getLatestSmsCampaignBatchJob(smsCampaign).total_jobs }}</div>
                                    </td>
                                    <!-- Pending -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                        <div class="text-sm text-gray-900">{{ getLatestSmsCampaignBatchJob(smsCampaign).pending_jobs }}</div>
                                    </td>
                                    <!-- Processed -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                        <div class="text-sm text-gray-900">{{ getLatestSmsCampaignBatchJob(smsCampaign).processed_jobs }}</div>
                                    </td>
                                    <!-- Progress -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                        <span class="text-lg text-green-600">{{ getLatestSmsCampaignBatchJob(smsCampaign).progress }} {{ getLatestSmsCampaignBatchJob(smsCampaign).progress ? '%' : '' }}</span>
                                    </td>
                                    <!-- Last Sprint Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ getLatestSmsCampaignBatchJob(smsCampaign).created_at == null ? '...' : moment(getLatestSmsCampaignBatchJob(smsCampaign).created_at).format('lll') }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a v-if="$inertia.page.props.projectPermissions.includes('View sms campaigns')" href="#" @click.prevent="$inertia.get(route('show.sms.campaign.job.batches', { project: route().params.project, sms_campaign: smsCampaign.id }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage sms campaigns')" href="#" @click.prevent="showModal(smsCampaign, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage sms campaigns')" href="#" @click.prevent="showModal(smsCampaign, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                    </td>
                                </tr>

                                <tr v-if="smsCampaignsPayload.data.length == 0">
                                    <!-- Content -->
                                    <td :colspan="10" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No sms campaigns</div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="smsCampaignsPayload" :updateData="['smsCampaignsPayload']" />

        </div>

    </div>

</template>
<script>
    import SmsCampaignCanSendSmsBadge from '../JobBatches/List/Partials/SmsCampaignCanSendSmsBadge.vue'
    import SmsCampaignStatusBadge from '../JobBatches/List/Partials/SmsCampaignStatusBadge.vue'
    import ManageSmsCampaignModal from './ManageSmsCampaignModal.vue'
    import Pagination from '../../../../Partials/Pagination.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            ManageSmsCampaignModal, Pagination, SmsCampaignCanSendSmsBadge, SmsCampaignStatusBadge
        },
        props: {
            contentToSendOptions: Array,
            scheduleTypeOptions: Array,
            subscriptionPlans: Array,
            smsCampaignsPayload: Object,
            projectPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                isShowingModal: false,
                modalAction: null,
                smsCampaign: null,
                moment: moment
            }
        },
        methods: {
            refreshContent()
            {
                this.$inertia.reload();
            },
            getLatestSmsCampaignBatchJob(smsCampaign)
            {
                if( smsCampaign.latest_sms_campaign_batch_job.length ) {
                    return smsCampaign.latest_sms_campaign_batch_job[0];
                }
                return {};
            },
            showModal(smsCampaign, action)
            {
                this.smsCampaign = smsCampaign;
                this.modalAction = action;
                this.isShowingModal = true
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
