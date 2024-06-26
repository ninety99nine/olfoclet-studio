<template>

    <div>

        <div v-if="$inertia.page.props.projectPermissions.includes('Manage subscriptions') && showAddbutton" class="grid grid-cols-2 mb-6 gap-4">

            <div class="bg-gray-50 pt-4 pl-6 border-b rounded-t">

                <div class="text-2xl font-semibold leading-6 text-gray-500 border-b pb-4 mb-4">Subscriptions</div>

                <div class="text-sm text-gray-500 my-2">
                    <span class="font-bold mr-2">GET / POST:</span>
                    <span class="text-green-500 font-semibold">{{ route('api.create.subscription', { project: route().params.project}) }}</span>
                </div>

            </div>

            <!-- Add Subscription Button -->
            <div>
                <jet-button @click="openModal()" class="float-right w-fit">
                    Add Subscription
                </jet-button>
                <div class="clear-both"></div>
            </div>

        </div>

        <div>

            <!-- Success Message -->
            <div v-if="showSuccessMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Subscription updated successfully</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Subscription deleted successfully</strong>
                <strong v-else class="font-bold">Subscription created successfully</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Error Message -->
            <div v-if="showErrorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Subscription update failed</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Subscription delete failed</strong>
                <strong v-else class="font-bold">Subscription creation failed</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>

            </div>

            <!-- Dialog Modal -->
            <jet-dialog-modal :show="showModal" :closeable="false">

                <!-- Modal Title -->
                <template #title>

                    <template v-if="wantsToUpdate">Update Subscription</template>

                    <template v-else-if="wantsToDelete">Delete Subscription</template>

                    <template v-else>Add Subscription</template>

                </template>

                <!-- Modal Content -->
                <template #content>

                    <template v-if="wantsToDelete">

                        <span class="block mt-6 mb-6">Are you sure you want to delete this subscription?</span>

                        <p class="text-sm text-gray-500">{{ hasSubscriber ? subscription.subscriber.msisdn : 'Unknown' }}</p>

                    </template>

                    <template v-else>

                        <!-- Mobile -->
                        <div class="mb-4">
                            <jet-label for="msisdn" value="Mobile" />
                            <jet-input id="msisdn" type="text" class="w-full mt-1 block" v-model="form.msisdn" placeholder = "26772000001" />
                            <jet-input-error :message="form.errors.msisdn" class="mt-2" />
                        </div>

                        <div>

                            <!-- Subscription Plan -->
                            <div class="mb-4">
                                <jet-label for="subscription-plan" value="Subscription Plan" class="mb-1" />
                                <el-cascader id="subscription-plan" v-model="form.subscription_plan_id" :props="getPropsForSubscriptionPlans()" collapse-tags collapse-tags-tooltip clearable class="w-full"/>
                                <jet-input-error :message="form.errors.subscription_plan_id" class="mt-2" />
                            </div>

                        </div>

                    </template>

                </template>

                <!-- Modal Footer -->
                <template #footer>

                    <jet-secondary-button @click="closeModal()" class="mr-2">
                        Close
                    </jet-secondary-button>

                    <jet-button v-if="!hasSubscription" @click.prevent="create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create
                    </jet-button>

                    <jet-button v-if="wantsToUpdate && form.cancelled_at == null" @click.prevent="cancel()" :class="[{ 'opacity-25': form.processing }, 'mr-2']" :disabled="form.processing">
                        Cancel
                    </jet-button>

                    <jet-button v-if="wantsToUpdate && form.cancelled_at != null" @click.prevent="uncancel()" :class="[{ 'opacity-25': form.processing }, 'mr-2']" :disabled="form.processing">
                        Uncancel
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

    import JetInput from '@/Components/TextInput.vue'
    import JetLabel from '@/Components/InputLabel.vue'
    import JetTextarea from '@/Components/Textarea.vue'
    import JetButton from '@/Components/PrimaryButton.vue'
    import JetInputError from '@/Components/InputError.vue'
    import JetSelectInput from '@/Components/SelectInput.vue'
    import JetDialogModal from '@/Components/DialogModal.vue'
    import JetDangerButton from '@/Components/DangerButton.vue'
    import JetActionMessage from '@/Components/ActionMessage.vue'
    import JetSecondaryButton from '@/Components/SecondaryButton.vue'

    export default defineComponent({
        components: {
            JetLabel, JetInput, JetTextarea, JetButton, JetInputError, JetSelectInput, JetDialogModal, JetSecondaryButton, JetActionMessage,
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
            subscription: {
                type: Object,
                default: null
            },
            subscriptionPlans: Array,
            show: {
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
            hasSubscription(){
                return this.subscription == null ? false : true;
            },
            hasSubscriber(){
                return this.hasSubscription && (this.subscription.subscriber == null ? false : true);
            },
            wantsToUpdate(){
                return (this.hasSubscription && this.action == 'update') ? true : false;
            },
            wantsToDelete(){
                return (this.hasSubscription && this.action == 'delete') ? true : false;
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

            getPropsForSubscriptionPlans() {

                return {
                    lazy: true,
                    multiple: false,
                    checkStrictly: false,
                    lazyLoad: function(node, resolve) {

                        const { level } = node;

                        //  If this is the first list of options
                        if( level === 0  ){

                            var url = route('api.show.subscription.plans', { project: route().params.project });

                        //  If this is the nested list of options
                        }else{

                            var url = route('api.show.subscription.plan', { project: route().params.project, subscription_plan: node.data.value, type: 'children' });

                        }

                        axios.get(url)
                            .then((response) => {

                                var nodes = response.data.data.map((subscriptionPlan) => {

                                    var isActive = subscriptionPlan.active;
                                    var isFolder = subscriptionPlan.isFolder;
                                    var hasChildren = subscriptionPlan.childrenCount > 0;

                                    var leaf = !hasChildren;
                                    var value = subscriptionPlan.id;
                                    var label = subscriptionPlan.name.length < 40 ? String (subscriptionPlan.name) : String (subscriptionPlan.name).substring(0, 40);

                                    return {
                                        leaf: leaf,
                                        value: value,
                                        label: label,
                                        disabled: (isFolder && !hasChildren) || !isActive
                                    }

                                });

                                resolve(nodes);

                            }).catch(() => resolve([]));
                    },
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

                this.form.transform((data) => {

                    if(data.subscription_plan_id.length > 0) {

                        //  Cpature the last id on this array of subscription ids
                        data.subscription_plan_id = data.subscription_plan_id[data.subscription_plan_id.length - 1];

                    }

                    return data;

                }).post(route('create.subscription', { project: route().params.project }), options);
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

                this.form.put(route('update.subscription', { project: route().params.project, subscription: this.subscription.id }), options);
            },
            cancel() {
                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                this.form.post(route('cancel.subscription', { project: route().params.project, subscription: this.subscription.id }), options);
            },
            uncancel() {
                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                this.form.post(route('uncancel.subscription', { project: route().params.project, subscription: this.subscription.id }), options);
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

                this.form.delete(route('delete.subscription', { project: route().params.project, subscription: this.subscription.id }), options);
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
                    cancelled_at: this.hasSubscriber ? this.subscription.cancelled_at : null,
                    msisdn: this.hasSubscriber ? (this.subscription.subscriber.msisdn ?? null) : null,
                    subscription_plan_id: this.hasSubscription ? [this.subscription.subscription_plan_id] : []
                });
            },
        },
        created(){

            this.reset();

        }
    })
</script>
