<template>

    <div>

        <!-- Add Subscription Plan Button -->
        <div v-if="showHeader" class="grid grid-cols-2 mb-6 gap-4">

            <div>
                <div class="bg-gray-50 pt-3 pl-6 border-b rounded-t">

                    <div class="text-2xl font-semibold leading-6 text-gray-500 mb-4">{{ parentSubscriptionPlan ? parentSubscriptionPlan.name : 'Subscription Plans' }}</div>

                    <template v-if="parentSubscriptionPlan">

                        <el-breadcrumb separator=">" class="mb-4">
                            <el-breadcrumb-item @click="nagivateToSubscriptionPlan()">
                                <span class="hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer">Subscription Plans</span>
                            </el-breadcrumb-item>

                            <el-breadcrumb-item v-for="breadcrumb in breadcrumbs" :key="breadcrumb.id" @click="nagivateToSubscriptionPlan(breadcrumb)">
                                <span class="hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer">{{ breadcrumb.name }}</span>
                            </el-breadcrumb-item>
                        </el-breadcrumb>

                        <jet-secondary-button @click="goBackToPreviousPage()" class="py-1 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                            </svg>
                            <span class="ml-2">Go Back</span>
                        </jet-secondary-button>

                    </template>

                </div>

                <div class="bg-gray-50 border-b pl-6 py-3 rounded-t text-gray-500 text-sm mb-6">
                    <span class="font-bold mr-2">Api Link:</span>
                    <span v-if="parentSubscriptionPlan">{{ route('api.show.subscription.plan', { project: route().params.project, subscription_plan: parentSubscriptionPlan.id, type: 'children' }) }}</span>
                    <span v-else>{{ route('api.show.subscription.plans', { project: route().params.project }) }}</span>
                </div>
            </div>

            <div v-if="$inertia.page.props.projectPermissions.includes('Manage subscription plans')">
                <jet-button @click="openModal()" class="w-fit float-right">Add Subscription Plan</jet-button>
                <div class="clear-both"></div>
            </div>

        </div>

        <div>

            <!-- Success Message -->
            <div v-if="showSuccessMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 mt-3" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Subscription plan updated successfully</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Subscription plan deleted successfully</strong>
                <strong v-else class="font-bold">Subscription plan created successfully</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Error Message -->
            <div v-if="showErrorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 mt-3" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Subscription plan update failed</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Subscription plan delete failed</strong>
                <strong v-else class="font-bold">Subscription plan creation failed</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Dialog Modal -->
            <jet-dialog-modal :show="showModal" :closeable="false">

                <!-- Modal Title -->
                <template #title>

                    <template v-if="wantsToUpdate">Update Subscription Plan</template>

                    <template v-else-if="wantsToDelete">Delete Subscription Plan</template>

                    <template v-else>Add Subscription Plan</template>

                </template>

                <!-- Modal Content -->
                <template #content>

                    <template v-if="wantsToDelete">

                        <span class="block mt-6 mb-6">Are you sure you want to delete this subscription plan?</span>

                        <p class="text-sm text-gray-500">{{ subscriptionPlan.name }}</p>

                    </template>

                    <template v-else>

                        <span class="block mt-6 mb-6">

                            <span v-if="parentSubscriptionPlan">
                                You are {{ wantsToUpdate ? 'updating' : 'adding' }} a subscription plan for
                                <span class="rounded-lg py-1 px-2 border border-green-400 text-green-500 text-sm">
                                    {{ parentSubscriptionPlan.name }}
                                </span>
                            </span>

                            <div v-if="parentSubscriptionPlan" class="bg-gray-50 py-3 px-3 mt-6 mb-2">

                                <el-breadcrumb separator=">">
                                    <el-breadcrumb-item>
                                        <span class="hover:text-green-600 text-green-500 font-semibold">Subscription Plans</span>
                                    </el-breadcrumb-item>

                                    <el-breadcrumb-item v-for="breadcrumb in breadcrumbs" :key="breadcrumb.id">
                                        <span class="text-green-500 font-semibold">{{ breadcrumb.name }}</span>
                                    </el-breadcrumb-item>
                                </el-breadcrumb>

                            </div>

                        </span>

                        <!-- Name -->
                        <div class="mb-4">
                            <jet-label for="name" value="Name" />
                            <jet-input id="name" type="text" class="w-full mt-1 block " v-model="form.name" placeholder="Daily @ P95.00"/>
                            <jet-input-error :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Active -->
                        <div class="mb-4">
                            <span class="text-sm text-gray-500">Active</span>
                            <el-switch v-model="form.active" class="mx-2"></el-switch>
                            <span class="text-sm text-gray-400">— {{ form.active ? 'Turn off to disable this subscription plan' : 'Turn on to enable this subscription plan' }}</span>
                        </div>

                        <!-- Folder -->
                        <div class="mb-4">
                            <span class="text-sm text-gray-500">Folder</span>
                            <el-switch v-model="form.is_folder" class="mx-2"></el-switch>
                            <span class="text-sm text-gray-400">— {{ form.is_folder ? 'Turn off to make this a subscription plan' : 'Turn on to make this a folder' }}</span>
                        </div>

                        <template v-if="form.is_folder == false">

                            <div class="grid gap-4 grid-cols-2">

                                <!-- Duration -->
                                <div class="mb-4">
                                    <jet-label for="duration" value="Duration" />
                                    <jet-number-input id="duration" class="w-full mt-1 block " v-model.string="form.duration" placeholder="1"/>
                                    <jet-input-error :message="form.errors.duration" class="mt-2" />
                                </div>

                                <!-- Frequency -->
                                <div class="mb-4">
                                    <jet-select-input placeholder="Select frequency" :options="frequencyOptions" v-model="form.frequency" class="mt-6" />
                                    <jet-input-error :message="form.errors.frequency" class="mt-2" />
                                </div>

                            </div>

                            <!-- Price -->
                            <div class="mb-4">
                                <jet-label for="price" value="Price" class="mb-1" />
                                <jet-number-input id="price" class="w-full mt-1 block " v-model="form.price" placeholder="95.00"/>
                                <jet-input-error :message="form.errors.price" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <div class="flex mb-1">
                                    <jet-label for="sub-pl-description" value="Description" />

                                    <el-popover :width="300">
                                        <template #reference>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                            </svg>
                                        </template>
                                        <template #default>
                                            <span class="break-normal">
                                                When billing the subscriber and creating airtime transation records, this description will be used as the transaction description.
                                            </span>
                                        </template>
                                    </el-popover>

                                </div>
                                <jet-textarea id="sub-pl-description" class="w-full mt-1 block " v-model="form.description" />
                                <jet-input-error :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Insufficient Funds Message -->
                            <div class="mb-4">
                                <div class="flex mb-1">
                                    <jet-label for="sub-pl-insufficient-funds-message" value="Insufficient Funds Message" />

                                    <el-popover :width="300">
                                        <template #reference>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                            </svg>
                                        </template>
                                        <template #default>
                                            <span class="break-normal">
                                                While billing, this message will be shown to the subscriber if they have insufficient funds to complete the transaction
                                            </span>
                                        </template>
                                    </el-popover>

                                </div>
                                <jet-textarea id="sub-pl-insufficient-funds-message" class="w-full mt-1 block " v-model="form.insufficient_funds_message" />
                                <jet-input-error :message="form.errors.insufficient_funds_message" class="mt-2" />
                            </div>

                            <!-- Successful Payment SMS Message -->
                            <div class="mb-4">
                                <div class="flex mb-1">
                                    <jet-label for="sub-pl-successful-payment-sms-message" value="Successful Payment SMS Message" />

                                    <el-popover :width="300">
                                        <template #reference>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                            </svg>
                                        </template>
                                        <template #default>
                                            <span class="break-normal">
                                                While billing, this SMS message will be sent to the subscriber if they have sufficient funds to complete the transaction
                                            </span>
                                        </template>
                                    </el-popover>

                                </div>
                                <jet-textarea id="sub-pl-successful-payment-sms-message" class="w-full mt-1 block " v-model="form.successful_payment_sms_message" />
                                <jet-input-error :message="form.errors.successful_payment_sms_message" class="mt-2" />
                            </div>

                            <!-- Auto Bill -->
                            <div class="mb-4">
                                <span class="text-sm text-gray-500">Can Auto Bill</span>
                                <el-switch v-model="form.can_auto_bill" class="mx-2"></el-switch>
                                <span class="text-sm text-gray-400">— {{ form.can_auto_bill ? 'Turn off to disable auto billing on this subscription plan' : 'Turn on to enable auto billing on this subscription plan' }}</span>
                            </div>

                            <template v-if="form.can_auto_bill">

                                <!-- Maximum Auto Billing Attempts -->
                                <div class="mb-4">
                                    <jet-label for="max_auto_billing_attempts" value="Maximum Auto Billing Attempts" />
                                    <jet-number-input id="max_auto_billing_attempts" class="w-full mt-1" v-model.string="form.max_auto_billing_attempts" min="1" max="3" placeholder="3"/>
                                    <jet-input-error :message="form.errors.max_auto_billing_attempts" class="mt-2" />
                                </div>

                                <!-- Next Auto Billing Reminder Options -->
                                <div class="flex items-center mb-4">
                                    <span class="font-medium text-sm text-gray-700 whitespace-nowrap mr-2">Next Auto Billing Reminder:</span>
                                    <div class="w-full">
                                        <el-select v-model="form.auto_billing_reminder_ids" multiple placeholder="Set Reminders" class="w-full">
                                            <el-option v-for="option in autoBillingReminderOptions" :key="option.value" :value="option.value" :label="option.name"></el-option>
                                        </el-select>
                                        <jet-input-error :message="form.errors.auto_billing_reminder_ids" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Next Auto Billing Reminder SMS Message -->
                                <div class="mb-4">
                                    <div class="flex mb-1">
                                        <jet-label for="sub-pl-next-auto-billing-reminder-sms-message" value="Next Auto Billing Reminder SMS Message" />

                                        <el-popover :width="300">
                                            <template #reference>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                </svg>
                                            </template>
                                            <template #default>
                                                <span class="break-normal">
                                                    Before billing, this SMS message will be sent to the subscriber to notify them 24 hours before auto billing
                                                </span>
                                            </template>
                                        </el-popover>
                                    </div>
                                    <jet-textarea id="sub-pl-next-auto-billing-reminder-sms-message" class="w-full mt-1 block " v-model="form.next_auto_billing_reminder_sms_message" />
                                    <jet-input-error :message="form.errors.next_auto_billing_reminder_sms_message" class="mt-2" />
                                </div>

                            </template>

                        </template>

                    </template>

                </template>

                <!-- Modal Footer -->
                <template #footer>

                    <jet-secondary-button @click="closeModal()" class="mr-2">
                        Cancel
                    </jet-secondary-button>

                    <jet-button v-if="!hasSubscriptionPlan" @click.prevent="create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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

    import { defineComponent } from 'vue'
    import JetLabel from '@/Components/InputLabel.vue'
    import JetInput from '@/Components/TextInput.vue'
    import JetTextarea from '@/Components/Textarea.vue'
    import JetButton from '@/Components/PrimaryButton.vue'
    import JetInputError from '@/Components/InputError.vue'
    import JetNumberInput from '@/Components/NumberInput.vue'
    import JetSelectInput from '@/Components/SelectInput.vue'
    import JetDialogModal from '@/Components/DialogModal.vue'
    import JetDangerButton from '@/Components/DangerButton.vue'
    import JetSecondaryButton from '@/Components/SecondaryButton.vue'

    export default defineComponent({
        components: {
            JetLabel, JetInput, JetNumberInput, JetTextarea, JetButton, JetInputError, JetSelectInput,
            JetDialogModal, JetSecondaryButton, JetDangerButton
        },
        props: {
            action: String,
            breadcrumbs: Array,
            modelValue: Boolean,
            subscriptionPlan: Object,
            autoBillingReminders: Array,
            parentSubscriptionPlan: Object,
            showHeader: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                //  Form attributes
                form: null,

                //  Modal attributes
                showModal: this.modelValue,

                showSuccessMessage: false,
                showErrorMessage: false
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
            hasSubscriptionPlan(){
                return this.subscriptionPlan == null ? false : true;
            },
            wantsToUpdate(){
                return (this.hasSubscriptionPlan && this.action == 'update') ? true : false;
            },
            wantsToDelete(){
                return (this.hasSubscriptionPlan && this.action == 'delete') ? true : false;
            },
            frequencyOptions(){
                return [
                    {
                        name: this.form.duration == '1' ? 'Minute': 'Minutes',
                        value: 'Minutes'
                    },
                    {
                        name: this.form.duration == '1' ? 'Hour': 'Hours',
                        value: 'Hours'
                    },
                    {
                        name: this.form.duration == '1' ? 'Day': 'Days',
                        value: 'Days'
                    },
                    {
                        name: this.form.duration == '1' ? 'Week': 'Weeks',
                        value: 'Weeks'
                    },
                    {
                        name: this.form.duration == '1' ? 'Month': 'Months',
                        value: 'Months'
                    },
                    {
                        name: this.form.duration == '1' ? 'Year': 'Years',
                        value: 'Years'
                    }
                ];
            },
            autoBillingReminderOptions() {
                return this.autoBillingReminders.map(function(autoBillingReminder){
                    return {
                        'name': autoBillingReminder.name,
                        'value': autoBillingReminder.id,
                    };
                });
            }
        },
        methods: {
            nagivateToSubscriptionPlan(subscriptionPlan = null){
                if( subscriptionPlan ){

                    this.$inertia.get(route('show.subscription.plan', { project: route().params.project, subscription_plan: subscriptionPlan.id }));

                }else{

                    this.$inertia.get(route('show.subscription.plans', { project: route().params.project }));

                }
            },
            goBackToPreviousPage(){
                window.history.back();
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

                this.form.transform((data) => {
                    if(data.is_folder) {
                        return {
                            'name': data.name,
                            'active': data.active,
                            'is_folder': data.is_folder,
                            'parent_id': data.parent_id,
                        };
                    }else if(data.can_auto_bill == false) {
                        delete data.next_auto_billing_reminder_sms_message;
                        delete data.auto_billing_reminder_ids;
                        return data;
                    }else {
                        return data;
                    }
                }).post(route('create.subscription.plan', { project: route().params.project }), options);
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

                this.form.transform((data) => {
                    if(data.is_folder) {
                        return {
                            'name': data.name,
                            'active': data.active,
                            'is_folder': data.is_folder,
                            'parent_id': data.parent_id,
                        };
                    }else if(data.can_auto_bill == false) {
                        delete data.next_auto_billing_reminder_sms_message;
                        delete data.auto_billing_reminder_ids;
                        return data;
                    }else {
                        return data;
                    }
                }).put(route('update.subscription.plan', { project: route().params.project, subscription_plan: this.subscriptionPlan.id }), options);
            },
            destroy() {

                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                this.form.delete(route('delete.subscription.plan', { project: route().params.project, subscription_plan: this.subscriptionPlan.id }), options);
            },
            handleOnSuccess(){

                this.reset();
                this.closeModal();

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
                    name: this.hasSubscriptionPlan ? this.subscriptionPlan.name : null,
                    active: this.hasSubscriptionPlan ? this.subscriptionPlan.active : true,
                    is_folder: this.hasSubscriptionPlan ? this.subscriptionPlan.is_folder : false,
                    frequency: this.hasSubscriptionPlan ? this.subscriptionPlan.frequency : 'Days',
                    parent_id: this.parentSubscriptionPlan ? this.parentSubscriptionPlan.id : null,
                    description: this.hasSubscriptionPlan ? this.subscriptionPlan.description : null,
                    can_auto_bill: this.hasSubscriptionPlan ? this.subscriptionPlan.can_auto_bill : true,
                    price: this.hasSubscriptionPlan ? this.subscriptionPlan.price.amount_without_currency : null,
                    max_auto_billing_attempts: this.hasSubscriptionPlan ? this.subscriptionPlan.max_auto_billing_attempts : 5,
                    duration: this.hasSubscriptionPlan ? (this.subscriptionPlan.is_folder ? null : this.subscriptionPlan.duration.toString()) : '3',
                    auto_billing_reminder_ids: this.hasSubscriptionPlan ? this.subscriptionPlan.auto_billing_reminders.map((autoBillingReminder) => autoBillingReminder.id) : [],
                    successful_payment_sms_message: this.hasSubscriptionPlan ? this.subscriptionPlan.successful_payment_sms_message : 'Your payment was successful. Thank you',
                    insufficient_funds_message: this.hasSubscriptionPlan ? this.subscriptionPlan.insufficient_funds_message : 'You do not have enough funds to complete this transaction',
                    next_auto_billing_reminder_sms_message: this.hasSubscriptionPlan ? this.subscriptionPlan.next_auto_billing_reminder_sms_message : 'You will be billed $price for $name on $date. To unsubscribe dial *123#',
                });
            },
        },
        created(){

            this.reset();

        }
    })
</script>
