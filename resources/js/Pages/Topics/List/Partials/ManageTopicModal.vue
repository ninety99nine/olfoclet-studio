<template>
    <div>
        <Dialog
            v-model:visible="showModal"
            :header="dialogTitle"
            :modal="true"
            :closable="true"
            :draggable="false"
            :style="{ width: wantsToDelete ? '400px' : '520px' }"
            :dismissableMask="!form?.processing"
            :pt="{
                root: { class: 'rounded-3xl border-none shadow-2xl overflow-hidden' },
                header: { class: 'bg-white px-6 pt-5 pb-0 border-none text-indigo-900 font-black uppercase text-sm tracking-widest' },
                pcCloseButton: { root: { class: 'h-8 w-8 bg-slate-50 text-slate-400 hover:text-rose-500 transition-all !border-0 !border-none shadow-none' } },
                content: { class: 'pt-4 pb-6' }
            }"
            @hide="emitModelValue"
        >
            <template v-if="wantsToDelete" #default>
                <p class="m-0 text-slate-600">Are you sure you want to delete this topic?</p>
                <p class="font-bold text-indigo-950 mt-2">{{ topic?.title }}</p>
            </template>

            <template v-else #default>
                <div class="flex flex-col gap-4">
                    <div v-if="parentTopic" class="rounded-xl bg-slate-50 border border-slate-100 py-3 px-4">
                        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Parent</p>
                        <p class="text-sm font-semibold text-indigo-900">{{ parentTopic.title }}</p>
                        <nav v-if="breadcrumbs && breadcrumbs.length" class="mt-2 flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                            <span>Topics</span>
                            <span v-for="crumb in breadcrumbs" :key="crumb.id" class="flex items-center gap-1.5">
                                <span>/</span>
                                <span class="text-indigo-600">{{ crumb.title }}</span>
                            </span>
                        </nav>
                    </div>

                    <div class="space-y-1">
                        <label for="topic-title" class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1 block">Title</label>
                        <InputText
                            id="topic-title"
                            v-model="form.title"
                            placeholder="Topic title"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors?.title }"
                        />
                        <InlineMessage v-if="form.errors?.title" severity="error" class="mt-1">
                            {{ form.errors.title }}
                        </InlineMessage>
                    </div>

                    <div class="space-y-1">
                        <label for="topic-content" class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1 block">Content</label>
                        <Textarea
                            id="topic-content"
                            v-model="form.content"
                            rows="5"
                            placeholder="Topic content (optional)"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors?.content || form.errors?.parent_topic_id }"
                        />
                        <InlineMessage v-if="form.errors?.content" severity="error" class="mt-1">
                            {{ form.errors.content }}
                        </InlineMessage>
                        <InlineMessage v-if="form.errors?.parent_topic_id" severity="error" class="mt-1">
                            {{ form.errors.parent_topic_id }}
                        </InlineMessage>
                    </div>
                </div>
            </template>

            <template #footer>
                <Button label="Cancel" severity="secondary" outlined :disabled="form?.processing" @click="closeModal()" />
                <Button
                    v-if="!hasTopic"
                    label="Create"
                    :loading="form?.processing"
                    :disabled="form?.processing"
                    @click="create()"
                >
                    <template #icon><Check :size="16" /></template>
                </Button>
                <Button
                    v-if="wantsToUpdate"
                    label="Update"
                    :loading="form?.processing"
                    :disabled="form?.processing"
                    @click="update()"
                >
                    <template #icon><Check :size="16" /></template>
                </Button>
                <Button
                    v-if="wantsToDelete"
                    label="Delete"
                    severity="danger"
                    :loading="form?.processing"
                    :disabled="form?.processing"
                    @click="destroy()"
                >
                    <template #icon><Trash2 :size="16" /></template>
                </Button>
            </template>
        </Dialog>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import InlineMessage from 'primevue/inlinemessage';
import { Check, Trash2 } from 'lucide-vue-next';

export default defineComponent({
    components: {
        Dialog,
        Button,
        InputText,
        Textarea,
        InlineMessage,
        Check,
        Trash2,
    },
    props: {
        topic: { type: Object, default: null },
        action: { type: String, default: null },
        parentTopic: { type: Object, default: null },
        modelValue: { type: Boolean, default: false },
        breadcrumbs: { type: Array, default: () => [] },
    },
    emits: ['update:modelValue', 'onDeleted'],
    data() {
        return {
            form: null,
            showModal: this.modelValue,
        };
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
    computed: {
        hasTopic() {
            return this.topic != null;
        },
        wantsToUpdate() {
            return this.hasTopic && this.action === 'update';
        },
        wantsToDelete() {
            return this.hasTopic && this.action === 'delete';
        },
        dialogTitle() {
            if (this.wantsToUpdate) return 'Update Topic';
            if (this.wantsToDelete) return 'Delete Topic';
            return 'Add Topic';
        },
    },
    created() {
        this.reset();
    },
    methods: {
        closeModal() {
            this.showModal = false;
        },
        emitModelValue() {
            this.$emit('update:modelValue', this.showModal);
        },
        create() {
            this.form.post(route('create.topic', { project: route().params.project }), {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onSuccess: () => this.handleOnSuccess(),
                onError: () => this.handleOnError(),
            });
        },
        update() {
            this.form.put(route('update.topic', { project: route().params.project, topic: this.topic.id }), {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onSuccess: () => this.handleOnSuccess(),
                onError: () => this.handleOnError(),
            });
        },
        destroy() {
            this.form.delete(route('delete.topic', { project: route().params.project, topic: this.topic.id }), {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onSuccess: () => this.handleOnSuccess(true),
                onError: () => this.handleOnError(),
            });
        },
        handleOnSuccess(hasDeleted = false) {
            this.reset();
            this.closeModal();
            if (hasDeleted) this.$emit('onDeleted');
        },
        handleOnError() {
            // Inertia keeps form.errors; InlineMessage will show them
        },
        reset() {
            this.form = this.$inertia.form({
                title: this.hasTopic ? this.topic.title : '',
                content: this.hasTopic ? (this.topic.content ?? '') : '',
                parent_id: this.parentTopic ? this.parentTopic.id : null,
            });
        },
    },
});
</script>
