<template>
    <div>
        <!-- Add button slot: only when showAddbutton is true (legacy, now handled by parent) -->
        <div v-if="projectPermissions.includes('Manage subscribers') && showAddbutton" class="flex justify-end mb-4">
            <Button label="Add Subscriber" @click="openModal()">
            <template #icon><Plus :size="16" /></template>
        </Button>
        </div>

        <Dialog
            v-model:visible="showModal"
            :header="dialogTitle"
            :modal="true"
            :closable="true"
            :draggable="false"
            :style="{ width: wantsToDelete ? '400px' : '560px' }"
            :dismissableMask="!processing"
            :pt="{
                root: { class: 'rounded-3xl border-none shadow-2xl overflow-hidden' },
                header: { class: 'bg-white px-6 pt-5 pb-0 border-none text-indigo-900 font-black uppercase text-sm tracking-widest' },
                pcCloseButton: { root: { class: 'h-8 w-8 bg-slate-50 text-slate-400 hover:text-rose-500 transition-all !border-0 !border-none shadow-none' } },
                content: { class: 'pt-4 pb-6' }
            }"
            @hide="emitModelValue"
        >
            <template v-if="wantsToDelete" #default>
                <p class="m-0 text-slate-600">Are you sure you want to delete this subscriber?</p>
                <p class="font-bold text-indigo-950 mt-2">{{ subscriber?.msisdn }}</p>
            </template>

            <template v-else #default>
                <div class="flex flex-col gap-4">
                    <div class="space-y-1">
                        <label for="msisdn" class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1 block">Mobile number</label>
                        <InputText
                            id="msisdn"
                            v-model="form.msisdn"
                            placeholder="26772000001"
                            class="w-full"
                            :class="{ 'p-invalid': v$.msisdn.$error }"
                            @blur="v$.msisdn.$touch()"
                        />
                        <InlineMessage v-if="v$.msisdn.$error" severity="error" class="mt-1">
                            {{ v$.msisdn.required.$message }}
                        </InlineMessage>
                        <InlineMessage v-else-if="formErrors.msisdn" severity="error" class="mt-1">
                            {{ formErrors.msisdn }}
                        </InlineMessage>
                    </div>
                    <div class="space-y-1">
                        <label for="metadata" class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1 block">Metadata (JSON)</label>
                        <Textarea
                            id="metadata"
                            v-model="form.metadata"
                            rows="12"
                            class="w-full font-mono text-sm"
                            :class="{ 'p-invalid': v$.metadata.$error }"
                            placeholder="{}"
                            @blur="v$.metadata.$touch()"
                        />
                        <InlineMessage v-if="v$.metadata.$error && v$.metadata.validJson" severity="error" class="mt-1">
                            {{ v$.metadata.validJson.$message }}
                        </InlineMessage>
                        <InlineMessage v-else-if="formErrors.metadata" severity="error" class="mt-1">
                            {{ formErrors.metadata }}
                        </InlineMessage>
                    </div>
                </div>
            </template>

            <template #footer>
                <Button label="Cancel" severity="secondary" outlined :disabled="processing" @click="closeModal()" />
                <Button
                    v-if="!hasSubscriber"
                    label="Create"
                    :loading="processing"
                    :disabled="processing"
                    @click="create()"
                >
                    <template #icon><Check :size="16" /></template>
                </Button>
                <Button
                    v-if="wantsToDelete"
                    label="Clear metadata"
                    severity="secondary"
                    outlined
                    :loading="processing"
                    :disabled="processing"
                    class="opacity-80"
                    @click="wipeMetadata()"
                >
                    <template #icon><Eraser :size="16" /></template>
                </Button>
                <Button
                    v-if="wantsToDelete"
                    label="Delete"
                    severity="danger"
                    :loading="processing || deletingSubscriber"
                    :disabled="processing || deletingSubscriber"
                    @click="destroy()"
                >
                    <template #icon><Trash2 :size="16" /></template>
                </Button>
            </template>
        </Dialog>
    </div>
</template>

<script>
import { defineComponent, reactive, computed, watch } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { required } from '@vuelidate/validators';

import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import InlineMessage from 'primevue/inlinemessage';
import { useToast } from 'primevue/usetoast';
import { Plus, Check, Trash2, Eraser } from 'lucide-vue-next';

function validJson(value) {
    if (!value || String(value).trim() === '') return true;
    try {
        JSON.parse(value);
        return true;
    } catch {
        return false;
    }
}

export default defineComponent({
    components: {
        Dialog,
        Button,
        InputText,
        Textarea,
        InlineMessage,
        Plus,
        Check,
        Trash2,
        Eraser,
    },
    setup(props, { emit }) {
        const toast = useToast();
        const form = reactive({ msisdn: '', metadata: '' });
        const rules = computed(() => ({
            msisdn: { required },
            metadata: {
                validJson: {
                    $validator: (v) => validJson(v),
                    $message: () => 'Must be valid JSON or empty',
                },
            },
        }));
        const v$ = useVuelidate(rules, form);
        return { toast, form, v$ };
    },
    props: {
        action: { type: String, default: 'update' },
        modelValue: { type: Boolean, default: false },
        showAddbutton: { type: Boolean, default: false },
        subscriber: { type: Object, default: null },
        show: { type: Boolean, default: false },
        projectPermissions: { type: Array, default: () => [] },
        deletingSubscriber: { type: Boolean, default: false },
    },
    emits: ['update:modelValue', 'onCreated', 'onUpdated', 'onDeleted', 'requestDelete'],
    data() {
        return {
            processing: false,
            showModal: this.modelValue,
            formErrors: {},
        };
    },
    computed: {
        dialogTitle() {
            if (this.wantsToDelete) return 'Delete Subscriber';
            return 'Add Subscriber';
        },
        hasSubscriber() {
            return this.subscriber != null;
        },
        wantsToDelete() {
            return this.hasSubscriber && this.action === 'delete';
        },
    },
    watch: {
        showModal(val) {
            if (val !== this.modelValue) this.$emit('update:modelValue', val);
        },
        modelValue(val) {
            if (val !== this.showModal) {
                this.showModal = val;
                this.reset();
            }
        },
    },
    methods: {
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        emitModelValue() {
            this.$emit('update:modelValue', this.showModal);
        },
        reset() {
            let metadata = '';
            if (this.hasSubscriber && this.subscriber.metadata != null) {
                metadata = JSON.stringify(this.subscriber.metadata, null, 4);
            }
            this.form.msisdn = this.hasSubscriber ? this.subscriber.msisdn : '';
            this.form.metadata = metadata;
            this.formErrors = {};
            this.v$.$reset();
        },
        async create() {
            this.v$.$touch();
            if (this.v$.$invalid) return;
            this.processing = true;
            this.formErrors = {};
            const url = route('create.subscriber', { project: route().params.project });
            window.axios
                .post(url, { msisdn: this.form.msisdn, metadata: this.form.metadata })
                .then(() => {
                    this.handleOnSuccess('created');
                    this.$emit('onCreated');
                })
                .catch((err) => {
                    if (err.response?.status === 422 && err.response.data?.errors) {
                        const raw = err.response.data.errors;
                        this.formErrors = Object.fromEntries(
                            Object.entries(raw).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
                        );
                    }
                    const detail = err.response?.data?.message || 'Subscriber creation failed.';
                    this.toast.add({ severity: 'error', summary: 'Error', detail, life: 5000 });
                })
                .finally(() => (this.processing = false));
        },
        destroy() {
            const raw = this.subscriber?.id;
            const subscriberId = raw != null ? Number(raw) : NaN;
            if (!Number.isInteger(subscriberId) || subscriberId <= 0) {
                this.toast.add({ severity: 'error', summary: 'Error', detail: 'Cannot delete: subscriber not identified.', life: 5000 });
                return;
            }
            // Parent builds URL and performs DELETE so the path always includes the subscriber id
            this.$emit('requestDelete', subscriberId);
        },
        wipeMetadata() {
            const subscriberId = this.subscriber?.id;
            if (!subscriberId) return;
            if (!window.confirm('Clear all metadata for this subscriber? The subscriber account will not be deleted. Use this for data deletion requests.')) return;
            this.processing = true;
            const project = route().params.project;
            const url = `${route('show.subscriber', { project, subscriber: subscriberId })}/wipe-metadata`;
            window.axios
                .patch(url)
                .then(() => {
                    this.toast.add({ severity: 'success', summary: 'Success', detail: 'Subscriber metadata cleared. Account kept.', life: 4000 });
                    this.reset();
                    this.closeModal();
                    this.$emit('onUpdated');
                })
                .catch((err) => {
                    const detail = err.response?.data?.message || 'Failed to clear metadata.';
                    this.toast.add({ severity: 'error', summary: 'Error', detail, life: 5000 });
                })
                .finally(() => (this.processing = false));
        },
        handleOnSuccess(action) {
            this.reset();
            this.closeModal();
            if (action === 'deleted') this.$emit('onDeleted');
            const summary = 'Success';
            const detail = action === 'deleted' ? 'Subscriber deleted successfully.' : 'Subscriber created successfully.';
            this.toast.add({ severity: 'success', summary, detail, life: 4000 });
        },
    },
    created() {
        this.reset();
    },
});
</script>

<style scoped>
:deep(.p-inputtext) {
    @apply h-11 bg-slate-50 border-slate-100 rounded-xl transition-all;
}
:deep(.p-inputtext:focus) {
    @apply border-indigo-400 bg-white ring-2 ring-indigo-500/10;
}
:deep(textarea.p-inputtext) {
    @apply rounded-xl bg-slate-50 border-slate-100 font-mono text-sm;
}
</style>
