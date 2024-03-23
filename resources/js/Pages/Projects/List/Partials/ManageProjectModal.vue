<template>

    <div>

        <!-- Add Project Button -->
        <jet-button v-if="showAddbutton" @click="openModal()" class="float-right mb-6">
            Add Project
        </jet-button>

        <div class="clear-both">

            <!-- Success Message -->
            <div v-if="showSuccessMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Project updated successfully</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Project deleted successfully</strong>
                <strong v-else class="font-bold">Project created successfully</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Error Message -->
            <div v-if="showErrorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Project update failed</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Project delete failed</strong>
                <strong v-else class="font-bold">Project creation failed</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Dialog Modal -->
            <jet-dialog-modal :show="showModal" :closeable="false">

                <!-- Modal Title -->
                <template #title>

                    <template v-if="wantsToUpdate">Update Project</template>

                    <template v-else-if="wantsToDelete">Delete Project</template>

                    <template v-else>Add Project</template>

                </template>

                <!-- Modal Content -->
                <template #content>

                    <template v-if="wantsToDelete">

                        <span class="block mt-6 mb-6">Are you sure you want to delete this project?</span>

                        <p class="text-sm text-gray-500">{{ project.name }}</p>

                    </template>

                    <template v-else>

                        <div class="grid grid-cols-12 gap-6">

                            <div class="col-span-12">
                                <!-- Name -->
                                <jet-label for="name" value="Project Name" />
                                <jet-input id="name" type="text" class="w-full mt-1 block " v-model="form.name" />
                                <jet-input-error :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="col-span-12">
                                <!-- Description -->
                                <jet-label for="description" value="Project Description" />
                                <jet-textarea id="description" class="w-full mt-1 block " v-model="form.description" />
                                <jet-input-error :message="form.errors.description" class="mt-2" />
                            </div>

                            <div class="col-span-12">
                                <!-- About URL -->
                                <jet-label for="about_url" value="About URL" />
                                <jet-input id="about_url" type="text" class="w-full mt-1 block " v-model="form.about_url" />
                                <jet-input-error :message="form.errors.about_url" class="mt-2" />
                            </div>

                            <div class="col-span-12">
                                <!-- Can Send Messages -->
                                <span>
                                    <span class="text-sm text-gray-500">Send messages</span>
                                    <el-switch v-model="form.can_send_messages" class="mx-2"></el-switch>
                                    <span class="text-sm text-gray-400">— {{ form.can_send_messages ? 'Turn off to stop sending messages' : 'Turn on to start sending messages' }}</span>
                                </span>
                            </div>

                            </div>

                            <div class="mt-10 mb-10">

                            <el-divider content-position="left"><span class="font-semibold">Sms Account Settings</span></el-divider>

                            </div>

                            <div class="grid grid-cols-6 gap-6">

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="sms_sender_name" value="Sender Name" />
                                <jet-input id="sms_sender_name" type="text" placeholder="Company XYZ" :maxlength="20" class="w-full mt-1 block " v-model="form.settings.sms_sender_name" />
                                <jet-input-error :message="form.errors['settings.sms_sender_name']" class="mt-2" />
                            </div>

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="sms_sender_number" value="Sender Number" />
                                <jet-input id="sms_sender_number" type="text" placeholder="26772012345" :maxlength="11" class="w-full mt-1 block " v-model="form.settings.sms_sender_number" />
                                <jet-input-error :message="form.errors['settings.sms_sender_number']" class="mt-2" />
                            </div>

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="sms_client_credentials" value="Client Credentials" />
                                <jet-input id="sms_client_credentials" :type="showClientCredentials ? 'text' : 'password'" placeholder="*************************" class="w-full mt-1 block " v-model="form.settings.sms_client_credentials" />
                                <jet-input-error :message="form.errors['settings.sms_client_credentials']" class="mt-2" />
                                <div class="flex items-center mt-2">
                                    <input v-model="showClientCredentials" id="show_client_credentials" name="show_client_credentials" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                    <label for="show_client_credentials" class="ml-3 block text-sm font-medium text-gray-700">Show credentials</label>
                                </div>
                            </div>

                            </div>

                            <div class="mt-10 mb-10">

                            <el-divider content-position="left"><span class="font-semibold">Auto Billing Settings</span></el-divider>

                            </div>

                            <div class="grid grid-cols-6 gap-6">

                            <div class="col-span-12">
                                <!-- Can Send Messages -->
                                <span>
                                    <span class="text-sm text-gray-500">Auto Bill</span>
                                    <el-switch v-model="form.can_auto_bill" class="mx-2"></el-switch>
                                    <span class="text-sm text-gray-400">— {{ form.can_auto_bill ? 'Turn off to stop auto billing on subscription plans' : 'Turn on to start auto billing on subscription plans' }}</span>
                                </span>
                            </div>

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="auto_billing_client_id" value="Client ID" />
                                <jet-input id="auto_billing_client_id" :type="showAutoBillingClientSecret ? 'text' : 'password'" placeholder="*************************" class="w-full mt-1 block " v-model="form.settings.auto_billing_client_id" />
                                <jet-input-error :message="form.errors['settings.auto_billing_client_id']" class="mt-2" />
                                <div class="flex items-center mt-2">
                                    <input v-model="showAutoBillingClientSecret" id="show_auto_billing_client_secret" name="show_auto_billing_client_secret" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                    <label for="show_auto_billing_client_secret" class="ml-3 block text-sm font-medium text-gray-700">Show credentials</label>
                                </div>
                            </div>

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="auto_billing_client_secret" value="Client Secret" />
                                <jet-input id="auto_billing_client_secret" :type="showAutoBillingClientID ? 'text' : 'password'" placeholder="*************************" class="w-full mt-1 block " v-model="form.settings.auto_billing_client_secret" />
                                <jet-input-error :message="form.errors['settings.auto_billing_client_secret']" class="mt-2" />
                                <div class="flex items-center mt-2">
                                    <input v-model="showAutoBillingClientID" id="show_auto_billing_client_id" name="show_auto_billing_client_id" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                    <label for="show_auto_billing_client_id" class="ml-3 block text-sm font-medium text-gray-700">Show credentials</label>
                                </div>
                            </div>

                        </div>

                    </template>

                </template>

                <!-- Modal Footer -->
                <template #footer>

                    <jet-secondary-button @click="closeModal()" class="mr-2">
                        Cancel
                    </jet-secondary-button>

                    <jet-button v-if="!hasProject" @click.prevent="create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
            JetLabel, JetInput, JetButton, JetTextarea, JetInputError, JetSelectInput, JetDialogModal, JetSecondaryButton, JetActionMessage,
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
            project: {
                type: Object,
                default: null
            },
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

                showAutoBillingClientSecret: false,
                showAutoBillingClientID: false,
                showClientCredentials: false,
                showSuccessMessage: false,
                showErrorMessage: false,
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
            hasProject(){
                return this.project == null ? false : true;
            },
            wantsToUpdate(){
                return (this.hasProject && this.action == 'update') ? true : false;
            },
            wantsToDelete(){
                return (this.hasProject && this.action == 'delete') ? true : false;
            }
        },
        methods: {

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

                    if(data.settings.sms_sender_name == '') data.settings.sms_sender_name = null;
                    if(data.settings.sms_sender_number == '') data.settings.sms_sender_number = null;
                    if(data.settings.sms_client_credentials == '') data.settings.sms_client_credentials = null;
                    if(data.settings.auto_billing_client_id == '') data.settings.auto_billing_client_id = null;
                    if(data.settings.auto_billing_client_secret == '') data.settings.auto_billing_client_secret = null;

                    return data;

                }).post(route('create.project'), options);
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

                this.form.put(route('update.project', { project: this.project.id }), options);
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

                this.form.delete(route('delete.project', { project: this.project.id }), options);
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
                    name: this.hasProject ? this.project.name : null,
                    about_url: this.hasProject ? this.project.about_url : null,
                    description: this.hasProject ? this.project.description : null,
                    can_auto_bill: this.hasProject ? this.project.can_auto_bill : false,
                    can_send_messages: this.hasProject ? this.project.can_send_messages : false,
                    settings: this.hasProject ? this.project.settings : {
                        sms_sender_name: '',
                        sms_sender_number: '',
                        sms_client_credentials: '',
                        auto_billing_client_id: '',
                        auto_billing_client_secret: '',
                    }
                });
            },
        },
        created(){

            this.reset();

        }
    })
</script>
