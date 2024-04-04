<template>

    <div>

        <create-project-modal v-model="isShowingModal" :action="modalAction" :project="project" />

        <div class="lg:w-1/2 w-full">
            <h1 role="heading" class="md:text-5xl text-3xl font-bold leading-10 mt-3 text-gray-800">My Projects</h1>
            <p role="contentinfo" class="text-base leading-5 mt-5 text-gray-600">Start managing your projects quick and easy, select your project and start creating content for automated sending to your subscribers.</p>
        </div>
        <div class="w-full mt-12 relative lg:mt-0" role="list">

            <img src="/images/flow-bg.png" class="absolute right-0 w-1/4" alt="background circle images" />

            <div class="grid grid-cols-3 gap-4 mt-12 mb-12 relative z-30">

                <div @click.self="showProject(project)" v-for="project in projectsPayload.data" :key="project.id" class="bg-white cursor-pointer shadow rounded-lg p-8 w-full aspect-video relative border">

                    <h2 @click.self="showProject(project)" class="text-2xl font-semibold leading-6 text-gray-800">{{ project.name }}</h2>
                    <p @click.self="showProject(project)" class="text-base leading-6 mt-4 text-gray-600">{{ project.description }}</p>

                    <div v-if="project.pivot.permissions.includes('Manage project settings')" class="absolute bottom-10 right-10">
                        <a href="#" @click.self.prevent="showModal(project, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <a href="#" @click.self.prevent="showModal(project, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                    </div>

                </div>

            </div>
        </div>

    </div>

</template>
<script>

    import Pagination from '../../../../Partials/Pagination.vue'
    import CreateProjectModal from './ManageProjectModal.vue'
    import { router, usePage } from '@inertiajs/vue3';
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            CreateProjectModal, Pagination
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
                moment: moment
            }
        },
        methods: {
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

                if(project.website_url != null || project.pdf_path != null) {

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

                }else if(this.canShowLink(project, 'View subscription plans')) {

                    url = route('show.subscription.plans', { project: project.id });

                }else if(this.canShowLink(project, 'View auto billing subscription plans')) {

                    url = route('show.auto.billing.subscription.plans', { project: project.id });

                }

                if(url != null) {

                    router.get(url);

                }
            }
        }
    })
</script>
