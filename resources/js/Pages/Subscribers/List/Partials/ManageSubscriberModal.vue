<template>

    <div>

        <div v-if="$inertia.page.props.projectPermissions.includes('Manage subscribers') && showAddbutton" class="grid grid-cols-2 mb-6 gap-4">

            <div class="bg-gray-50 pt-4 px-6 border-b rounded-t">

                <div class="text-2xl font-semibold leading-6 text-gray-500 pb-4">Subscribers</div>

            </div>

            <!-- Add Subscriber Button -->
            <div>
                <jet-button @click="openModal()" class="float-right w-fit">
                    Add Subscriber
                </jet-button>
                <div class="clear-both"></div>
            </div>

        </div>

        <div class="clear-both">

            <!-- Success Message -->
            <div v-if="showSuccessMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Subscriber updated successfully</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Subscriber deleted successfully</strong>
                <strong v-else class="font-bold">Subscriber created successfully</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Error Message -->
            <div v-if="showErrorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Subscriber update failed</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Subscriber delete failed</strong>
                <strong v-else class="font-bold">Subscriber creation failed</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Dialog Modal -->
            <jet-dialog-modal :show="showModal" :closeable="false">

                <!-- Modal Title -->
                <template #title>

                    <template v-if="wantsToUpdate">Update Subscriber</template>

                    <template v-else-if="wantsToDelete">Delete Subscriber</template>

                    <template v-else>Add Subscriber</template>

                </template>

                <!-- Modal Content -->
                <template #content>

                    <template v-if="wantsToDelete">

                        <span class="block mt-6 mb-6">Are you sure you want to delete this subscriber?</span>

                        <p class="text-sm text-gray-500">{{ subscriber.msisdn }}</p>

                    </template>

                    <template v-else>

                        <!-- Mobile -->
                        <div class="mb-4">
                            <jet-label for="msisdn" value="Mobile" />
                            <jet-input id="msisdn" type="text" class="w-full mt-1 block" v-model="form.msisdn" placeholder = "26772000001" />
                            <jet-input-error :message="form.errors.msisdn" class="mt-2" />
                        </div>

                        <!-- Metadata -->
                        <jet-input-error :message="form.errors.metadata" class="mb-2" />
                        <CodeEditor v-model="form.metadata" :languages="[['json', 'JSON']]" :line-nums="true" :tab-spaces="4" theme="gradient-dark" :header="false" width="100%" height="400px" font-size="14px"></CodeEditor>

                    </template>

                </template>

                <!-- Modal Footer -->
                <template #footer>

                    <jet-secondary-button @click="closeModal()" class="mr-2">
                        Cancel
                    </jet-secondary-button>

                    <jet-button v-if="!hasSubscriber" @click.prevent="create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
    import JetButton from '@/Components/PrimaryButton.vue'
    import JetInputError from '@/Components/InputError.vue'
    import JetSelectInput from '@/Components/SelectInput.vue'
    import JetDialogModal from '@/Components/DialogModal.vue'
    import JetDangerButton from '@/Components/DangerButton.vue'
    import JetActionMessage from '@/Components/ActionMessage.vue'
    import JetSecondaryButton from '@/Components/SecondaryButton.vue'

    /**
     *  Package Reference: https://github.com/justcaliturner/simple-code-editor
     */
    import hljs from 'highlight.js';
    import CodeEditor from "simple-code-editor";

    export default defineComponent({
        components: {
            JetLabel, JetInput, JetButton, JetInputError, JetSelectInput, JetDialogModal, JetSecondaryButton, JetActionMessage,
            JetDangerButton, CodeEditor
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
            subscriber: {
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
            hasSubscriber(){
                return this.subscriber == null ? false : true;
            },
            wantsToUpdate(){
                return (this.hasSubscriber && this.action == 'update') ? true : false;
            },
            wantsToDelete(){
                return (this.hasSubscriber && this.action == 'delete') ? true : false;
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
             *  JSON METHODS
             */
            isValidJsonString(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            },

            /**
             *  FORM METHODS
             */
            create() {

                if( this.form.metadata.trim() != '' && this.isValidJsonString(this.form.metadata) == false) {

                    this.form.setError('metadata', 'Subscriber metadata is not valid JSON format');
                    setTimeout(() => { this.form.clearErrors('metadata'); }, 3000);
                    return;

                }

                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },

                };

                this.form.post(route('create.subscriber', { project: route().params.project }), options);
            },
            update() {

                if( this.form.metadata.trim() != '' && this.isValidJsonString(this.form.metadata) == false) {

                    this.form.setError('metadata', 'Subscriber metadata is not valid JSON format');
                    setTimeout(() => { this.form.clearErrors('metadata'); }, 3000);
                    return;

                }

                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                this.form.put(route('update.subscriber', { project: route().params.project, subscriber: this.subscriber.id }), options);
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

                this.form.delete(route('delete.subscriber', { project: route().params.project, subscriber: this.subscriber.id }), options);
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

                var metadata = '';

                if( this.hasSubscriber ) {

                    if(this.subscriber.metadata != null) {

                        metadata = JSON.stringify(this.subscriber.metadata, null, 4);

                    }

                }

                this.form = this.$inertia.form({
                    msisdn: this.hasSubscriber ? this.subscriber.msisdn : null,
                    metadata: metadata
                });
            },
        },
        created(){

            this.reset();

        }
    })
</script>
