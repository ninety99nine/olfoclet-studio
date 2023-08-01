<template>


    <div class="mt-10 sm:mt-0">
        <div class="grid grid-cols-8 gap-4">
            <div class="col-start-3 col-span-4">
                <h1 class="text-2xl font-semibold leading-6 text-gray-500">Project Settings</h1>
            </div>
        </div>
        <div class="grid grid-cols-8 gap-4 mt-4 mb-4">
            <div class="col-start-3 col-span-4">
                <!-- Success Message -->
                <div v-if="showSuccessMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Project updated successfully</strong>
                    <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
                </div>

                <!-- Error Message -->
                <div v-if="showErrorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Project update failed</strong>
                    <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-8 gap-4">
            <div class="col-start-3 col-span-4">
                <div class="shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 bg-white sm:p-6">

                        <div class="grid grid-cols-12 gap-6">

                            <div class="col-span-12">
                                <!-- Name -->
                                <jet-label for="name" value="Project Name" />
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" />
                                <jet-input-error :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="col-span-12">
                                <!-- Description -->
                                <jet-label for="description" value="Description" />
                                <jet-textarea id="description" class="mt-1 block w-full" v-model="form.description" />
                                <jet-input-error :message="form.errors.description" class="mt-2" />
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
                                <jet-input id="sms_sender_name" type="text" placeholder="Company XYZ" :maxlength="20" class="mt-1 block w-full" v-model="form.settings.sms_sender_name" />
                                <jet-input-error :message="form.errors['settings.sms_sender_name']" class="mt-2" />
                            </div>

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="sms_sender_number" value="Sender Number" />
                                <jet-input id="sms_sender_number" type="text" placeholder="26772012345" :maxlength="11" class="mt-1 block w-full" v-model="form.settings.sms_sender_number" />
                                <jet-input-error :message="form.errors['settings.sms_sender_number']" class="mt-2" />
                            </div>

                            <div class="col-span-6 sm:col-span-12">
                                <jet-label for="sms_client_credentials" value="Client Credentials" />
                                <jet-input id="sms_client_credentials" :type="showClientCredentials ? 'text' : 'password'" placeholder="*************************" class="mt-1 block w-full" v-model="form.settings.sms_client_credentials" />
                                <jet-input-error :message="form.errors['settings.sms_client_credentials']" class="mt-2" />
                                <div class="flex items-center mt-2">
                                    <input v-model="showClientCredentials" id="show_password" name="show_password" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                    <label for="show_password" class="ml-3 block text-sm font-medium text-gray-700">Show credentials</label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div v-if="$inertia.page.props.projectPermissions.includes('Manage project settings')" class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <jet-button @click.prevent="update()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save Changes
                        </jet-button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
<script>

    import moment from "moment";
    import { defineComponent } from 'vue'

    import JetInput from '@/Components/TextInput.vue'
    import JetLabel from '@/Components/InputLabel.vue'
    import JetTextarea from '@/Components/Textarea.vue'
    import JetButton from '@/Components/PrimaryButton.vue'
    import JetInputError from '@/Components/InputError.vue'

    export default defineComponent({
        components: {
            JetInputError, JetTextarea, JetButton, JetLabel, JetInput
        },
        props: {
            project: Object
        },
        data() {
            return {
                showSuccessMessage: false,
                showErrorMessage: false,
                showClientCredentials: false,
                moment: moment,
                form: null,
            }
        },
        methods: {
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
            handleOnSuccess(){

                this.reset();

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
                    name: this.project.name,
                    settings: this.project.settings,
                    description: this.project.description,
                    can_send_messages: this.project.can_send_messages,
                });
            },

        },
        created(){

            this.reset();

        }
    })
</script>
