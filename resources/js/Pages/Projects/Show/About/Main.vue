<template>
    <app-layout title="About">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-6 pb-12">
                <!-- Page header (always visible) -->
                <div class="mb-6">
                    <h1 class="text-2xl font-black tracking-tight text-indigo-950">About</h1>
                    <p v-if="project?.name" class="mt-1 text-sm text-slate-500">{{ project.name }}</p>
                </div>

                <!-- Website URL: iframe in card -->
                <template v-if="project?.website_url">
                    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                        <div v-show="loading" class="flex flex-col justify-center items-center py-24 px-8 min-h-[420px]">
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 mb-4">
                                <SpiningLoader size="md" />
                            </span>
                            <p class="text-sm font-medium text-slate-500">Loading...</p>
                        </div>
                        <div v-show="!loading" class="relative min-h-[70vh]">
                            <iframe
                                :src="project.website_url"
                                class="absolute inset-0 w-full h-full min-h-[70vh]"
                                title="Project website"
                                @load="handleLoad"
                            />
                        </div>
                    </div>
                </template>

                <!-- PDF: embed in card -->
                <div v-else-if="project?.pdf_path" class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                    <div class="min-h-[70vh] p-4">
                        <embed
                            :src="project.pdf_path"
                            type="application/pdf"
                            class="w-full h-[75vh] min-h-[500px] rounded-xl"
                            title="Project PDF"
                        />
                    </div>
                </div>

                <!-- No URL or PDF: empty state card -->
                <div v-else class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                    <div class="py-24 px-8 flex flex-col items-center justify-center text-center">
                        <span class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 mb-6 text-slate-400">
                            <FileText :size="28" />
                        </span>
                        <h2 class="text-lg font-bold text-indigo-950 mb-2">{{ project?.name ?? 'Project' }}</h2>
                        <p class="text-sm font-medium text-slate-500 max-w-md">
                            This project does not have an embedded website URL or PDF. Add one in project settings to show content here.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SpiningLoader from '@/Components/SpiningLoader.vue';
import { FileText } from 'lucide-vue-next';

export default defineComponent({
    components: { AppLayout, SpiningLoader, FileText },
    props: {
        project: { type: Object, default: null },
    },
    data() {
        return { loading: true };
    },
    methods: {
        handleLoad() {
            this.loading = false;
        },
    },
});
</script>
