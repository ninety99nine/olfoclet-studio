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
                                <jet-input id="name" type="text" class="w-full mt-1 block" v-model="form.name" />
                                <jet-input-error :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="col-span-12">
                                <!-- Description -->
                                <jet-label for="description" value="Project Description" />
                                <jet-textarea id="description" class="w-full mt-1 block" v-model="form.description" />
                                <jet-input-error :message="form.errors.description" class="mt-2" />
                            </div>

                            <div class="col-span-12">
                                <!-- Website URL -->
                                <jet-label for="website_url" value="Website URL" />
                                <jet-input id="website_url" type="text" class="w-full mt-1 block" v-model="form.website_url" />
                                <jet-input-error :message="form.errors.website_url" class="mt-2" />
                            </div>

                            <div class="col-span-12">

                                <div class="flex justify-between items-center">

                                    <div>
                                        <!-- PDF Upload -->
                                        <jet-label for="pdf" :value="form.pdf_path ? 'Change PDF' : 'Upload PDF'" />
                                        <input type="file" id="pdf" class="w-full mt-1" @change="handleSelectedPDF" accept=".pdf" />
                                        <jet-input-error :message="form.errors.pdf" class="mt-2" />
                                    </div>

                                    <div v-if="form.pdf_path != null">

                                        <jet-danger-button @click="form.pdf_path = null" class="mr-2">
                                            Remove PDF
                                        </jet-danger-button>

                                    </div>

                                </div>

                                <!-- Preview PDF if selected -->
                                <div v-if="form.pdf">
                                    <embed :src="pdfPreview" type="application/pdf" width="100%" height="400px" class="mt-4 block border border-gray-500">
                                </div>

                                <!-- Preview PDF if already uploaded -->
                                <div v-else-if="form.pdf_path">
                                    <embed :src="form.pdf_path" type="application/pdf" width="100%" height="400px" class="mt-4 block border border-gray-500">
                                </div>

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
                                <jet-input id="sms_sender_name" type="text" placeholder="Company XYZ" :maxlength="20" class="w-full mt-1 block" v-model="form.settings.sms_sender_name" />
                                <jet-input-error :message="form.errors['settings.sms_sender_name']" class="mt-2" />
                            </div>

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="sms_sender_number" value="Sender Number" />
                                <jet-input id="sms_sender_number" type="text" placeholder="26772012345" :maxlength="11" class="w-full mt-1 block" v-model="form.settings.sms_sender_number" />
                                <jet-input-error :message="form.errors['settings.sms_sender_number']" class="mt-2" />
                            </div>

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="sms_client_credentials" value="Client Credentials" />
                                <jet-input id="sms_client_credentials" :type="showClientCredentials ? 'text' : 'password'" placeholder="*************************" class="w-full mt-1 block" v-model="form.settings.sms_client_credentials" />
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

                        <div class="grid grid-cols-12 gap-6">

                            <div class="col-span-12">
                                <!-- Can Auto Bill -->
                                <span>
                                    <span class="text-sm text-gray-500">Auto Bill</span>
                                    <el-switch v-model="form.can_auto_bill" class="mx-2"></el-switch>
                                    <span class="text-sm text-gray-400">— {{ form.can_auto_bill ? 'Turn off to stop auto billing on subscription plans' : 'Turn on to start auto billing on subscription plans' }}</span>
                                </span>
                            </div>

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="auto_billing_client_id" value="Client ID" />
                                <jet-input id="auto_billing_client_id" :type="showAutoBillingClientSecret ? 'text' : 'password'" placeholder="*************************" class="w-full mt-1 block" v-model="form.settings.auto_billing_client_id" />
                                <jet-input-error :message="form.errors['settings.auto_billing_client_id']" class="mt-2" />
                                <div class="flex items-center mt-2">
                                    <input v-model="showAutoBillingClientSecret" id="show_auto_billing_client_secret" name="show_auto_billing_client_secret" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                    <label for="show_auto_billing_client_secret" class="ml-3 block text-sm font-medium text-gray-700">Show credentials</label>
                                </div>
                            </div>

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="auto_billing_client_secret" value="Client Secret" />
                                <jet-input id="auto_billing_client_secret" :type="showAutoBillingClientID ? 'text' : 'password'" placeholder="*************************" class="w-full mt-1 block" v-model="form.settings.auto_billing_client_secret" />
                                <jet-input-error :message="form.errors['settings.auto_billing_client_secret']" class="mt-2" />
                                <div class="flex items-center mt-2">
                                    <input v-model="showAutoBillingClientID" id="show_auto_billing_client_id" name="show_auto_billing_client_id" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                    <label for="show_auto_billing_client_id" class="ml-3 block text-sm font-medium text-gray-700">Show credentials</label>
                                </div>
                            </div>

                        </div>

                        <div class="mt-10 mb-10">

                            <el-divider content-position="left"><span class="font-semibold">Billing Report Settings</span></el-divider>

                            <!-- Can Create Billing Reports -->
                            <p class="mb-4">
                                <span class="text-sm text-gray-500">Create Billing Reports</span>
                                <el-switch v-model="form.can_create_billing_reports" class="mx-2"></el-switch>
                                <span class="text-sm text-gray-400">— {{ form.can_create_billing_reports ? 'Turn off to stop creation of billing reports' : 'Turn on to start creation of billing reports' }}</span>
                            </p>

                            <div class="grid grid-cols-12 gap-6 bg-gray-100 p-4 mb-4">

                                <div class="col-span-6 sm:col-span-12">
                                    <jet-label for="their_share_percentage" value="Their Share (%)" />
                                    <jet-input id="their_share_percentage" type="number" placeholder="40" :maxlength="100" class="w-full mt-1 block" v-model="form.their_share_percentage" />
                                    <jet-input-error :message="form.errors.their_share_percentage" class="mt-2" />
                                </div>

                                <div class="col-span-6 sm:col-span-12">
                                    <jet-label for="our_share_percentage" value="Our Share (%)" />
                                    <jet-input id="our_share_percentage" type="number" placeholder="60" :maxlength="100" class="w-full mt-1 block" v-model="form.our_share_percentage" />
                                    <jet-input-error :message="form.errors.our_share_percentage" class="mt-2" />
                                </div>

                            </div>

                            <div class="bg-gray-100 p-4 mb-4">

                                <span class="block text-sm font-medium text-gray-700 mt-4 mb-2">Add or Remove Billing Costs:</span>

                                <div v-for="(cost, index) in form.costs" :key="index" class="hover:bg-gray-100 p-2 -mx-2 rounded-md">

                                    <div class="flex space-x-2">

                                        <div class="w-full">
                                            <!-- Name -->
                                            <jet-input id="cost_name" type="text" class="w-full" v-model="cost.name" />
                                        </div>

                                        <div>
                                            <div class="flex items-center space-x-4">
                                                <div class="flex space-x-1 items-center">
                                                    <div class="w-20">
                                                        <!-- Percentage -->
                                                        <jet-input id="cost_amount" type="number" class="w-full" v-model="cost.percentage" :min="1" :max="100" />
                                                    </div>
                                                    <span>%</span>
                                                </div>
                                                <div>
                                                    <!-- Cancel -->
                                                    <svg @click="removeCost(index)" class="w-4 h-4 hover:opacity-80 cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <jet-input-error :message="form.errors['costs.'+index+'.name']" class="mt-2 ml-2" />
                                    <jet-input-error :message="form.errors['costs.'+index+'.percentage']" class="mt-2 ml-2" />

                                </div>

                                <jet-input-error :message="form.errors.costs" class="mt-2 ml-2" />

                                <div class="flex justify-end mt-2">
                                    <jet-secondary-button @click="addCost()">
                                        <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        <span>Add Cost</span>
                                    </jet-secondary-button>
                                </div>

                            </div>

                            <div>

                                <!-- Billing Report Email Addresses -->
                                <div class="flex items-center bg-gray-100 p-4 mb-4">

                                    <span class="block text-sm font-medium text-gray-700 mr-2 whitespace-nowrap">Email Reports:</span>

                                    <div>

                                        <el-tag v-for="billing_report_email_address in form.billing_report_email_addresses" :key="billing_report_email_address" class="mx-1" closable :disable-transitions="false" @close="handleRemoveTag(billing_report_email_address)">{{ billing_report_email_address }}</el-tag>

                                        <span v-if="showAddTagInput" class="w-20">
                                            <el-input ref="addTagInputRef" v-model="addTagInput" size="small"
                                                @keyup.enter="handleAddTag"
                                                @blur="handleAddTag"/>
                                        </span>

                                        <el-button v-else class="button-new-tag ml-1" size="small" @click="showInput">
                                            + New Email
                                        </el-button>

                                    </div>

                                </div>

                                <div v-for="(billing_report_email_address, index) in form.billing_report_email_addresses" :key="index">
                                    <jet-input-error  :message="form.errors['billing_report_email_addresses.'+index]" class="mt-2 ml-2" />
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

    import { useForm } from '@inertiajs/vue3';
    import { nextTick, defineComponent } from 'vue';
    import JetInput from '@/Components/TextInput.vue';
    import JetLabel from '@/Components/InputLabel.vue';
    import JetTextarea from '@/Components/Textarea.vue';
    import JetButton from '@/Components/PrimaryButton.vue';
    import JetInputError from '@/Components/InputError.vue';
    import JetSelectInput from '@/Components/SelectInput.vue';
    import JetDialogModal from '@/Components/DialogModal.vue';
    import JetDangerButton from '@/Components/DangerButton.vue';
    import JetActionMessage from '@/Components/ActionMessage.vue';
    import JetSecondaryButton from '@/Components/SecondaryButton.vue';

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

                addTagInput: '',
                showAddTagInput: false,

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
            handleSelectedPDF(event) {

                //  Unset any existing PDF file reference
                this.form.pdf_path = null;

                //  Set the PDF file on the form
                this.form.pdf = event.target.files[0];

                // Update PDF preview source
                this.pdfPreview = URL.createObjectURL(event.target.files[0]);

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

            addCost() {
                this.form.costs.push({
                    'name': 'Cost #' + this.form.costs.length,
                    'percentage': 1
                });
            },
            removeCost(index) {
                this.form.costs.splice(index, 1);
            },

            /**
             *  EMAIL ADDRESS TAG METHODS
             */
             handleAddTag() {
                if (this.addTagInput) {
                    const newTag = this.addTagInput.trim();
                    if (!this.form.billing_report_email_addresses.includes(newTag)) {
                        this.form.billing_report_email_addresses.push(newTag);
                    }
                }
                this.showAddTagInput = false;
                this.addTagInput = '';
            },
            handleRemoveTag(email) {
                this.form.billing_report_email_addresses.splice(this.form.billing_report_email_addresses.indexOf(email), 1);
            },
            showInput() {
                this.showAddTagInput = true;
                nextTick(() => {
                    this.$refs.addTagInputRef.focus();
                });
            },

            create() {

                var options = {

                    replace: true,
                    preserveState: true,
                    preserveScroll: true,

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
                    replace: true,
                    preserveState: true,
                    preserveScroll: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                /**
                 *  This form.post is preferred to support the file upload. Inertia will
                 *  allow Laravel to covert this post to put since we have included the
                 *  { _method: 'put' } as part of the post data. Refer to the reset()
                 *  method below. Also check out the inertia docs.
                 *
                 *  Reference: https://inertiajs.com/file-uploads
                 */
                this.form.post(route('update.project', { project: this.project.id }), options);
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

                var data = {
                    pdf: null,
                    name: this.hasProject ? this.project.name : null,
                    pdf_path: this.hasProject ? this.project.pdf_path : null,
                    website_url: this.hasProject ? this.project.website_url : null,
                    description: this.hasProject ? this.project.description : null,
                    can_auto_bill: this.hasProject ? this.project.can_auto_bill : false,
                    can_send_messages: this.hasProject ? this.project.can_send_messages : false,
                    settings: this.hasProject ? this.project.settings : {
                        sms_sender_name: '',
                        sms_sender_number: '',
                        sms_client_credentials: '',
                        auto_billing_client_id: '',
                        auto_billing_client_secret: '',
                    },
                    costs: this.hasProject ? this.project.costs : [
                        {
                            'name': 'USAF',
                            'percentage': 1
                        },
                        {
                            'name': 'BOCRA',
                            'percentage': 3
                        },
                        {
                            'name': 'VAT (14%)',
                            'percentage': 14
                        },
                        {
                            'name': 'Dealer Commission (Airtime)',
                            'percentage': 13.5
                        }
                    ],
                    can_create_billing_reports: this.hasProject ? this.project.can_create_billing_reports :false,
                    billing_report_email_addresses: this.hasProject ? this.project.billing_report_email_addresses : [],
                    our_share_percentage: this.hasProject ? (this.project.our_share_percentage ?? {}).toString() : '60',
                    their_share_percentage: this.hasProject ? (this.project.their_share_percentage ?? {}).toString() : '40',
                };

                if(this.hasProject) {

                    /**
                     *  Uploading files using a multipart/form-data request is not natively supported in some server-side
                     *  frameworks when using the PUT,PATCH, or DELETE HTTP methods. The simplest workaround for this
                     *  limitation is to simply upload files using a POST request instead.
                     *
                     *  However, some frameworks, such as Laravel and Rails, support form method spoofing, which allows you
                     *  to upload the files using POST, but have the framework handle the request as a PUT or PATCH request.
                     *  This is done by including a _method attribute in the data of your request.
                     *
                     *  Also check out the inertia docs.
                     *
                     *  Reference: https://inertiajs.com/file-uploads
                     */
                    data._method = 'put';

                }

                this.form = useForm(data);
            },
        },
        created(){

            this.reset();

        }
    })
</script>
