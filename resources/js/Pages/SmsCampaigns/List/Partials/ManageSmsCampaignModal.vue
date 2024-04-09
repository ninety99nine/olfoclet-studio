<template>

    <div>

        <!-- Add Sms Campaign Button -->
        <jet-button v-if="$inertia.page.props.projectPermissions.includes('Manage sms campaigns') && showAddbutton" @click="openModal()" class="float-right mb-6">
            Add Sms Campaign
        </jet-button>

        <div class="clear-both">

            <!-- Success Message -->
            <div v-if="showSuccessMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Sms campaign updated successfully</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Sms campaign deleted successfully</strong>
                <strong v-else class="font-bold">Sms campaign created successfully</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Error Message -->
            <div v-if="showErrorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Sms campaign update failed</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Sms campaign delete failed</strong>
                <strong v-else class="font-bold">Sms campaign creation failed</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Dialog Modal -->
            <jet-dialog-modal :show="showModal" :closeable="false">

                <!-- Modal Title -->
                <template #title>

                    <template v-if="wantsToUpdate">Update Sms Campaign</template>

                    <template v-else-if="wantsToDelete">Delete Sms Campaign</template>

                    <template v-else>Add Sms Campaign</template>


                </template>

                <!-- Modal Content -->
                <template #content>

                    <template v-if="wantsToDelete">

                        <span class="block mt-6 mb-6">Are you sure you want to delete this sms campaign?</span>

                        <p class="text-sm text-gray-500">{{ smsCampaign.content }}</p>

                    </template>

                    <template v-else>

                        <!-- Name -->
                        <div class="mb-4">
                            <jet-label for="name" value="Name" />
                            <jet-input id="name" type="text" class="w-full mt-1 block" v-model="form.name" />
                            <jet-input-error :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-8">
                            <jet-label for="description" value="Description" />
                            <jet-textarea id="description" class="w-full mt-1 block" v-model="form.description" />
                            <jet-input-error :message="form.errors.description" class="mt-2" />
                        </div>

                        <!-- Can Send Messages -->
                        <div class="mb-4">
                            <span class="text-sm text-gray-500">Send messages</span>
                            <el-switch v-model="form.can_send_messages" class="mx-2"></el-switch>
                            <span class="text-sm text-gray-400">— {{ form.can_send_messages ? 'Turn off to stop sending messages' : 'Turn on to start sending messages' }}</span>
                        </div>

                        <!-- Cancel Active Sprints -->
                        <div v-if="!form.can_send_messages" class="flex items-center mb-4">

                            <el-checkbox class="text-sm text-gray-500" v-model="form.cancel_batch_jobs">
                                <span class="text-sm text-gray-500">Cancel active sprints</span>
                            </el-checkbox>

                            <el-popover :width="300">
                                <template #reference>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                </template>
                                <template #default>
                                    <span class="break-normal">
                                        <span class="font-bold">Checked: </span>This will cancel the sending of queued messages (Running sprints will be stopped)
                                    </span>
                                    <hr class="my-2" />
                                    <span class="break-normal">
                                        <span class="font-bold">Unchecked: </span>This will not cancel the sending of queued messages (Running sprints will continue)
                                    </span>
                                </template>
                            </el-popover>

                        </div>

                        <div class="mb-8">

                            <el-divider content-position="left"><span class="font-semibold">Timing</span></el-divider>

                            <span class="block text-sm text-gray-500 mb-4">Decide when to send messages e.g send now, send later or send recurring (every 1 week)</span>

                            <el-select v-model="form.schedule_type" placeholder="Select schedule type" size="large">
                                <el-option v-for="option in scheduleTypeOptions" :key="option" :label="option" :value="option" > </el-option>
                            </el-select>

                            <jet-input-error :message="form.errors.schedule_type" />

                        </div>

                        <div v-if="isSendingLater || isSendingRecurring" class="mb-8">

                            <el-divider content-position="left"><span class="font-semibold mb-4">Schedule</span></el-divider>

                            <span class="block text-sm text-gray-500 mb-4">
                                {{ isSendingLater ? 'Decide on the date and time that the message must be sent' : 'Decide on the frequency that messages must be sent' }}
                            </span>

                            <!-- Recurring Frequency & Duration -->
                            <div v-if="isSendingRecurring" class="flex items-center mb-4">

                                <span class="block whitespace-nowrap text-sm text-gray-700 mr-4">Send every</span>

                                <div class="mr-4">
                                    <el-input-number v-model="form.recurring_duration" :min="1" controls-position="right" />
                                    <jet-input-error :message="form.errors.recurring_duration" />
                                </div>

                                <div class="w-full">
                                    <el-select v-model="form.recurring_frequency" clearable placeholder="Select frequency" class="w-full">
                                        <el-option v-for="option in frequencyOptions" :key="option.value" :label="option.name" :value="option.value" ></el-option>
                                    </el-select>
                                    <jet-input-error :message="form.errors.recurring_frequency" />
                                </div>

                            </div>

                            <!-- Date -->
                            <div class="flex items-center">

                                <div>
                                    <div class="flex items-center">

                                        <span class="block text-sm text-gray-700 mr-4">{{ isSendingLater ? 'Send date' : 'Date from' }}</span>

                                        <el-date-picker v-model="form.start_date" type="date" value-format="YYYY-MM-DD 00:00:00" format="DD MMM YYYY" placeholder="Start date"></el-date-picker>

                                    </div>
                                    <jet-input-error :message="form.errors.start_date" />
                                </div>

                                <div v-if="isSendingRecurring">
                                    <div class="flex items-center">

                                        <span class="block text-sm text-gray-700 ml-4 mr-4">To</span>

                                        <el-date-picker v-model="form.end_date" type="date" value-format="YYYY-MM-DD 00:00:00" format="DD MMM YYYY" placeholder="End date"></el-date-picker>

                                    </div>
                                    <jet-input-error :message="form.errors.end_date" />
                                </div>

                            </div>

                            <!-- Time -->
                            <div :class="['flex', 'items-center', 'mt-4', 'mb-4']">

                                <div>
                                    <div class="flex items-center">

                                        <span class="block text-sm text-gray-700 mr-4">{{ isSendingLater ? 'Send time' : 'Time from' }}</span>

                                        <el-time-select v-model="form.start_time" :max-time="form.end_time" placeholder="Start time" start="06:00" step="00:15" end="18:00"></el-time-select>

                                    </div>
                                    <jet-input-error :message="form.errors.start_time" />
                                </div>

                                <div v-if="isSendingRecurring">
                                    <div class="flex items-center">

                                        <span class="block text-sm text-gray-700 ml-4 mr-4">To</span>

                                        <el-time-select v-model="form.end_time" :min-time="form.start_time" placeholder="End time" start="06:00" step="00:15" end="18:00"></el-time-select>

                                    </div>
                                    <jet-input-error :message="form.errors.end_time" />
                                </div>

                            </div>

                            <!-- Recurring Days Of The Week -->
                            <div v-if="isSendingRecurring" class="flex items-center">

                                <span class="block whitespace-nowrap text-sm text-gray-700 mr-4">On</span>

                                <div class="w-full">
                                    <el-select v-model="form.days_of_the_week" multiple clearable placeholder="Select days of the week" class="w-full">
                                        <el-option v-for="option in daysOfTheWeekOptions" :key="option" :label="option" :value="option" ></el-option>
                                    </el-select>
                                    <jet-input-error :message="form.errors.days_of_the_week" />
                                </div>

                            </div>

                        </div>

                        <div class="mt-10 mb-10">

                            <el-divider content-position="left"><span class="font-semibold">Subscription Plans</span></el-divider>

                        </div>

                        <!-- Subscription Plans -->
                        <div class="mb-4">

                            <span class="block text-sm text-gray-500 mb-4">Choose subscriptions plans required to qualify for this sms campaign</span>

                            <div class="flex items-center">

                                <el-select id="subscription_plans" v-model="form.subcription_plan_ids" multiple class="w-full" placeholder="Select subcription plan" >
                                    <el-option v-for="plan in subscriptionPlanOptions" :key="plan.value" :value="plan.value" :label="plan.name"></el-option>
                                </el-select>

                            </div>

                            <jet-input-error :message="form.errors.subcription_plan_ids" />

                        </div>

                        <div class="mt-10 mb-10">

                            <el-divider content-position="left"><span class="font-semibold">Messages</span></el-divider>

                        </div>

                        <!-- Messages -->
                        <div>

                            <span class="block text-sm text-gray-500 mb-4">Choose messages to send for this sms campaign</span>

                            <el-select class="mb-4" v-model="form.message_to_send" @change="handleSwitchingModes()" placeholder="Select content to send" size="large">
                                <el-option v-for="option in contentToSendOptions" :key="option" :label="option" :value="option" > </el-option>
                            </el-select>

                            <!-- Messages to send -->
                            <div class="flex mb-4 items-center">
                                <span class="block whitespace-nowrap text-sm text-gray-700 mr-4">Messages</span>
                                <el-cascader v-model="form.message_ids" :props="getPropsForMessages()" collapse-tags collapse-tags-tooltip clearable class="w-full"/>
                            </div>

                            <jet-input-error :message="form.errors.message_ids" />

                        </div>

                    </template>

                </template>

                <!-- Modal Footer -->
                <template #footer>

                    <jet-secondary-button @click="closeModal()" class="mr-2">
                        Cancel
                    </jet-secondary-button>

                    <jet-button v-if="!hasSmsCampaign" @click.prevent="create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create
                    </jet-button>

                    <jet-button v-if="wantsToUpdate" @click.prevent="update()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update
                    </jet-button>

                    <jet-danger-button v-if="wantsToDelete" @click.prevent="destroy()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete
                    </jet-danger-button>

                </template>

            </jet-dialog-modal>

        </div>

    </div>

</template>

<script>

    import moment from "moment";
    import { defineComponent } from 'vue'

    import axios from "axios";
    import JetInput from '@/Components/TextInput.vue'
    import JetLabel from '@/Components/InputLabel.vue'
    import JetTextarea from '@/Components/Textarea.vue'
    import JetButton from '@/Components/PrimaryButton.vue'
    import JetInputError from '@/Components/InputError.vue'
    import JetSelectInput from '@/Components/SelectInput.vue'
    import JetDialogModal from '@/Components/DialogModal.vue'
    import JetDangerButton from '@/Components/DangerButton.vue'
    import JetSecondaryButton from '@/Components/SecondaryButton.vue'
import { isArray, isInteger } from "lodash";

    export default defineComponent({
        components: {
            JetLabel, JetInput, JetTextarea, JetButton, JetInputError, JetSelectInput, JetDialogModal, JetSecondaryButton,
            JetDangerButton
        },
        props: {
            action: {
                type: String,
                default: 'update'
            },
            modelValue: {
                type: Boolean,
                default: false
            },
            showAddbutton: {
                type: Boolean,
                default: false
            },
            smsCampaign: {
                type: Object,
                default: null
            },
            contentToSendOptions: Array,
            scheduleTypeOptions: Array,
            subscriptionPlans: Array,
            show: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                moment: moment,

                //  Form attributes
                form: null,

                //  Modal attributes
                showModal: this.modelValue,

                showSuccessMessage: false,
                showErrorMessage: false,

                daysOfTheWeekOptions: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
            }
        },

        watch: {

            showModal: {
                handler: function (val, oldVal) {

                    if(val != this.modelValue){
                        this.$emit('update:modelValue', val);
                    }

                }
            },

            modelValue: {
                handler: function (val, oldVal) {

                    if(val != this.showModal){
                        this.showModal = val;
                        this.reset();
                    }

                }
            },

        },

        computed: {
            isSendingNow(){
                return this.form.schedule_type == 'Send Now';
            },
            isSendingLater(){
                return this.form.schedule_type == 'Send Later';
            },
            isSendingRecurring(){
                return this.form.schedule_type == 'Send Recurring';
            },
            hasSmsCampaign(){
                return this.smsCampaign == null ? false : true;
            },
            wantsToUpdate(){
                return (this.hasSmsCampaign && this.action == 'update') ? true : false;
            },
            wantsToDelete(){
                return (this.hasSmsCampaign && this.action == 'delete') ? true : false;
            },
            frequencyOptions(){
                return [
                    {
                        name: this.form.recurring_duration == '1' ? 'Minute': 'Minutes',
                        value: 'Minutes'
                    },
                    {
                        name: this.form.recurring_duration == '1' ? 'Hour': 'Hours',
                        value: 'Hours'
                    },
                    {
                        name: this.form.recurring_duration == '1' ? 'Day': 'Days',
                        value: 'Days'
                    },
                    {
                        name: this.form.recurring_duration == '1' ? 'Week': 'Weeks',
                        value: 'Weeks'
                    },
                    {
                        name: this.form.recurring_duration == '1' ? 'Month': 'Months',
                        value: 'Months'
                    },
                    {
                        name: this.form.recurring_duration == '1' ? 'Year': 'Years',
                        value: 'Years'
                    }
                ];
            },
            subscriptionPlanOptions() {
                return this.subscriptionPlans.map(function(subscriptionPlan){
                    return {
                        'name': subscriptionPlan.name,
                        'value': subscriptionPlan.id,
                    };
                });
            }
        },
        methods: {
            getPropsForMessages() {

                var allowMultipleEntries = (this.form.message_to_send == 'Any Message');

                return {
                    lazy: true,
                    multiple: allowMultipleEntries,
                    checkStrictly: allowMultipleEntries,
                    lazyLoad: function(node, resolve) {

                        const { level } = node;

                        //  If this is the first list of options
                        if( level === 0  ){

                            var url = route('api.show.messages', { project: route().params.project });

                        //  If this is the nested list of options
                        }else{

                            var url = route('api.show.message', { project: route().params.project, message: node.data.value, type: 'children' });

                        }

                        axios.get(url)
                            .then((response) => {

                                var nodes = response.data.data.map((message) => {
                                    return {
                                        value: message.id,
                                        label: message.content.length < 40 ? String (message.content) : String (message.content).substring(0, 40),
                                        leaf: message.children_count == 0
                                    }
                                });

                                resolve(nodes);

                            }).catch(() => resolve([]));
                    },
                }
            },
            handleSwitchingModes() {

                /**
                 *  (1) Specific Message
                 *  ---------------------
                 *
                 *  If the message_to_send is "Specific Message" then the
                 *  message_ids will contain one array with a list of ids
                 *  from the parent to the child message we want to send
                 *  e.g
                 *
                 *  [ 1, 10, 20, 30 ]
                 *
                 *  In the case above we want to target the message with
                 *  id of 30 which is a descendant of message 1, 10,
                 *  and 20.
                 *
                 *  (2) Any Message
                 *  ---------------
                 *
                 *  If the message_to_send is "Any Message" then the
                 *  message_ids will contain one array with a list
                 *  of arrays of ids from the parent to the child
                 *  message we want to send e.g
                 *
                 *  [ [1, 10, 20, 30], [1, 10, 20, 35], .... , .e.t.c ]
                 *
                 *  In the case above we want the message with id of 30
                 *  and message with id 35 which are both descendants
                 *  of message 1, 10, and 20
                 *
                 *  Handling The Switching Of These 2 Modes
                 *  ---------------------------------------
                 *
                 *  With this in mind we need to make sure that as we switch between
                 *  these two modes, we make sure that the data is structured
                 *  properly to be compatible with that mode e.g When we
                 *  select multiple messages while on the "Any Message"
                 *  mode, then we need to only select one message when
                 *  we switch to the "Specific Message" mode. We also
                 *  need to wrap the single message in a nested array
                 *  when switching back to "Any Message" mode.
                 */
                if(this.form.message_to_send == 'Specific Message') {

                    /**
                     *  Suppose that we were switching from "Any Message" to "Specific Message" mode.
                     *  We need to then only select one array since we cannot support multiple nested
                     *  arrays e.g
                     *
                     *  from: [ [1, 10, 20, 30], [1, 10, 20, 35], .... , .e.t.c ]
                     *  to:   [1, 10, 20, 30]
                     */
                    if(this.form.message_ids.length) {

                        //  If the first item in the list is an array
                        if( isArray(this.form.message_ids[0]) ) {

                            //  Get the first item as the result to use
                            this.form.message_ids = this.form.message_ids[0];

                        }

                    }

                }else{

                    /**
                     *  Suppose that we were switching from "Specific Message" to "Any Message" mode.
                     *  We need to then wrap the result in an array since we can support multiple
                     *  nested arrays e.g
                     *
                     *  from: [1, 10, 20, 30]
                     *  to:   [ [1, 10, 20, 30], ... then more arrays can be added here ]
                     */
                    if(this.form.message_ids.length) {

                        //  If the first item in the list is an integer
                        if( isInteger(this.form.message_ids[0]) ) {

                            //  Wrap the items in an array
                            this.form.message_ids = [this.form.message_ids];

                        }

                    }

                }

            },
            /**
             *  MODAL METHODS
             */
            openModal() {
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
            },

            /**
             *  FORM METHODS
             */
            create() {
                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },

                };

                this.form.post(route('create.sms.campaign', { project: route().params.project }), options);
            },
            update() {
                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                this.form.put(route('update.sms.campaign', { project: route().params.project, sms_campaign: this.smsCampaign.id }), options);
            },
            destroy() {

                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess(true);

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                this.form.delete(route('delete.sms.campaign', { project: route().params.project, sms_campaign: this.smsCampaign.id }), options);
            },
            handleOnSuccess(hasDeleted = false){

                this.reset();
                this.closeModal();
                if(hasDeleted) this.$emit('onDeleted');

                this.showSuccessMessage = true;

                setTimeout(() => {
                    this.showSuccessMessage = false;
                }, 3000);

            },
            handleOnError(){

                this.showErrorMessage = true;

                setTimeout(() => {
                    this.showErrorMessage = false;
                }, 3000);

            },
            reset() {
                this.form = this.$inertia.form({
                    name: this.hasSmsCampaign ? this.smsCampaign.name : null,
                    description: this.hasSmsCampaign ? this.smsCampaign.description : null,
                    can_send_messages: this.hasSmsCampaign ? this.smsCampaign.can_send_messages : false,
                    schedule_type: this.hasSmsCampaign ? this.smsCampaign.schedule_type : this.scheduleTypeOptions[0],
                    recurring_duration: this.hasSmsCampaign ? this.smsCampaign.recurring_duration : 1,
                    recurring_frequency: this.hasSmsCampaign ? this.smsCampaign.recurring_frequency : 'Days',

                    message_to_send: this.hasSmsCampaign ? this.smsCampaign.message_to_send : 'Specific Message',
                    message_ids: this.hasSmsCampaign ? this.smsCampaign.message_ids : [],

                    subcription_plan_ids: this.hasSmsCampaign ? this.smsCampaign.subscription_plans.map((subscriptionPlan) => subscriptionPlan.id) : [],

                    //  Set start date to today
                    start_date: this.hasSmsCampaign ? moment(this.smsCampaign.start_date).format('YYYY-MM-DD HH:mm:ss') : new Date(),

                    //  Set end date 1 year from now
                    end_date: this.hasSmsCampaign ? moment(this.smsCampaign.end_date).format('YYYY-MM-DD HH:mm:ss') : (new Date()).setFullYear((new Date()).getFullYear() + 1),

                    start_time: this.hasSmsCampaign ? this.smsCampaign.start_time : '06:00',
                    end_time: this.hasSmsCampaign ? this.smsCampaign.end_time : '18:00',

                    days_of_the_week: this.daysOfTheWeekOptions,

                    cancel_batch_jobs: this.hasSmsCampaign ? this.smsCampaign.cancel_batch_jobs : false,
                });
            }
        },
        created(){

            this.reset();

        }
    })
</script>
