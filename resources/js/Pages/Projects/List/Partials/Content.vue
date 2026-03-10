<template>

    <div>

        <create-project-modal v-model="isShowingModal" :action="modalAction" :project="project" />

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="max-w-2xl">
                    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                        My Projects
                    </h1>
                    <p class="mt-2 text-sm sm:text-base leading-6 text-gray-600 dark:text-gray-300">
                        Choose a project to manage subscribers and automated content. You can also add a new project anytime.
                    </p>
                </div>

                <div class="shrink-0">
                    <jet-button type="button" @click="openCreateModal()">
                        <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Project
                    </jet-button>
                </div>
            </div>

            <div v-if="projectsPayload?.data?.length" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5" role="list">
                <div
                    v-for="project in projectsPayload.data"
                    :key="project.id"
                    role="listitem"
                    class="group relative rounded-xl border border-gray-200/70 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600 transition cursor-pointer focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 focus-within:ring-offset-slate-50 dark:focus-within:ring-offset-slate-900/30"
                    @click="showProject(project)"
                >
                    <div class="p-5 sm:p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
                                    {{ project.name }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                    {{ project.description || 'No description provided.' }}
                                </p>
                            </div>

                            <svg class="w-5 h-5 shrink-0 text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-400 transition" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>

                        <div class="mt-5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <span v-if="project.website_url" class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-700/50 px-2 py-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                                    Website
                                </span>
                                <span v-if="project.pdf_path" class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-700/50 px-2 py-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500" />
                                    PDF
                                </span>
                            </div>

                            <div v-if="project.pivot?.permissions?.includes('Manage project settings')" class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="text-xs font-medium text-blue-700 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 focus:outline-none"
                                    @click.stop.prevent="showModal(project, 'update')"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    class="text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 focus:outline-none"
                                    @click.stop.prevent="showModal(project, 'delete')"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 bg-white/60 dark:bg-gray-800/40 p-10 sm:p-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/30">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75a2.25 2.25 0 0 1 2.25-2.25H6a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 6 20.25h-.75a2.25 2.25 0 0 1-2.25-2.25v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                </div>
                <h2 class="mt-4 text-base font-semibold text-gray-900 dark:text-gray-100">No projects yet</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    Create your first project to start managing subscribers and automated sends.
                </p>
                <div class="mt-6 flex justify-center">
                    <jet-button type="button" @click="openCreateModal()">
                        Add Project
                    </jet-button>
                </div>
            </div>
        </div>

    </div>

</template>
<script>

    import CreateProjectModal from './ManageProjectModal.vue'
    import { router, usePage } from '@inertiajs/vue3';
    import { defineComponent } from 'vue'
    import JetButton from '@/Components/PrimaryButton.vue';

    export default defineComponent({
        components: {
            CreateProjectModal,
            JetButton,
        },
        props: {
            projectsPayload: Object
        },
        data() {
            return {
                isShowingModal: false,
                modalAction: null,
                page: usePage(),
                project: null,
            }
        },
        methods: {
            openCreateModal() {
                this.project = null;
                this.modalAction = 'create';
                this.isShowingModal = true;
            },
            showModal(project, action){
                this.project = project;
                this.modalAction = action;
                this.isShowingModal = true;
            },
            canShowLink(project, permission){
                return project.pivot.permissions.includes('*') || project.pivot.permissions.includes(permission);
            },
            showProject(project) {

                var url = null;

                if(this.canShowLink(project, 'View subscriptions')) {

                    url = route('show.subscriptions', { project: project.id });

                }else if(project.website_url != null || project.pdf_path != null) {

                    url = route('show.project.about', { project: project.id });

                }else if(this.canShowLink(project, 'View users')) {

                    url = route('show.users', { project: project.id });

                }else if(this.canShowLink(project, 'View topics')) {

                    url = route('show.topics', { project: project.id });

                }else if(this.canShowLink(project, 'View messages')) {

                    url = route('show.messages', { project: project.id });

                }else if(this.canShowLink(project, 'View subscribers')) {

                    url = route('show.subscribers', { project: project.id });

                }else if(this.canShowLink(project, 'View sms campaigns')) {

                    url = route('show.sms.campaigns', { project: project.id });

                }else if(this.canShowLink(project, 'View pricing plans')) {

                    url = route('show.pricing.plans', { project: project.id });

                }else if(this.canShowLink(project, 'View auto billing pricing plans')) {

                    url = route('show.auto.billing.pricing.plans', { project: project.id });

                }

                if(url != null) {

                    router.get(url);

                }
            }
        }
    })
</script>
