<template>
    <app-layout title="About">
        <div class="min-h-screen bg-slate-50 dark:bg-slate-900/30 pb-12">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-6 pb-12">
                <!-- Page header -->
                <header class="mb-8">
                    <h1 class="text-2xl font-black tracking-tight text-indigo-950 dark:text-slate-100">
                        About
                    </h1>
                    <p v-if="project?.name" class="mt-1 text-base font-medium text-slate-600 dark:text-slate-400">
                        {{ project.name }}
                    </p>
                    <p
                        v-if="project?.description"
                        class="mt-3 text-sm text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed"
                    >
                        {{ project.description }}
                    </p>
                    <p
                        v-else-if="project?.website_url || project?.pdf_path"
                        class="mt-2 text-sm text-slate-500 dark:text-slate-400"
                    >
                        Project details and embedded content below.
                    </p>
                </header>

                <!-- Embedded website -->
                <template v-if="project?.website_url">
                    <section class="mb-8">
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm">
                            <div class="flex items-center justify-between gap-4 px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80">
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-300 flex items-center gap-2">
                                    <Globe :size="16" class="text-indigo-500 shrink-0" />
                                    Embedded website
                                </span>
                                <a
                                    :href="project.website_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center gap-1.5 transition-colors"
                                >
                                    <ExternalLink :size="14" />
                                    Open in new tab
                                </a>
                            </div>
                            <div class="relative min-h-[70vh] bg-slate-100 dark:bg-slate-900/50">
                                <div
                                    v-show="loading"
                                    class="absolute inset-0 flex flex-col justify-center items-center py-24 px-8 z-10"
                                >
                                    <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 mb-4 shadow-sm">
                                        <SpiningLoader size="md" />
                                    </span>
                                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Loading website…</p>
                                </div>
                                <iframe
                                    :src="project.website_url"
                                    class="absolute inset-0 w-full h-full min-h-[70vh]"
                                    title="Project website"
                                    @load="handleLoad"
                                />
                            </div>
                        </div>
                    </section>
                </template>

                <!-- PDF document -->
                <template v-else-if="project?.pdf_path">
                    <section class="mb-8">
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm">
                            <div class="flex items-center justify-between gap-4 px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80">
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-300 flex items-center gap-2">
                                    <FileText :size="16" class="text-indigo-500 shrink-0" />
                                    Project document
                                </span>
                                <a
                                    :href="project.pdf_path"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center gap-1.5 transition-colors"
                                >
                                    <ExternalLink :size="14" />
                                    Open in new tab
                                </a>
                            </div>
                            <div class="min-h-[70vh] p-4 bg-slate-50 dark:bg-slate-900/30">
                                <embed
                                    :src="project.pdf_path"
                                    type="application/pdf"
                                    class="w-full h-[75vh] min-h-[500px] rounded-xl border border-slate-200 dark:border-slate-600 bg-white"
                                    title="Project PDF"
                                />
                            </div>
                        </div>
                    </section>
                </template>

                <!-- Empty state: no URL or PDF -->
                <section v-else class="mb-8">
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm">
                        <div class="py-16 sm:py-24 px-6 sm:px-12 flex flex-col items-center justify-center text-center">
                            <span class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/50 mb-6 text-indigo-500 dark:text-indigo-400">
                                <FileText :size="36" />
                            </span>
                            <h2 class="text-xl font-bold text-indigo-950 dark:text-slate-100 mb-2">
                                {{ project?.name ?? 'Project' }}
                            </h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md leading-relaxed mb-6">
                                There’s no website or PDF linked to this project yet. Add a website URL or upload a PDF in the project settings to show content here.
                            </p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                Go to <strong>My Projects</strong>, open this project’s menu, and choose <strong>Edit</strong> to add a link or file.
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SpiningLoader from '@/Components/SpiningLoader.vue';
import { FileText, Globe, ExternalLink } from 'lucide-vue-next';

export default defineComponent({
    components: { AppLayout, SpiningLoader, FileText, Globe, ExternalLink },
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
