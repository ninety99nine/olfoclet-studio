<template>
    <app-layout title="Dashboard">

        <template v-if="project.website_url">
            <div v-show="loading" class="flex justify-center items-center h-screen">
                <SpiningLoader size="md"></SpiningLoader>
                <h1 class="font-bold ml-4">{{ project.name }}</h1>
            </div>

            <iframe v-show="!loading" :src="project.website_url" class="w-full h-screen" @load="handleLoad"></iframe>
        </template>

        <embed v-else-if="project.pdf_path" :src="project.pdf_path" type="application/pdf" class="w-full h-screen" />

        <div v-else class="p-20">

            <h1 class="text-4xl font-bold mb-8">{{ project.name }}</h1>
            <p>This project does not have any embedded Website Url or PDF.</p>

        </div>

    </app-layout>
</template>

<script>

    import AppLayout from '@/Layouts/AppLayout.vue';
    import SpiningLoader from '@/Components/SpiningLoader.vue';

    export default {
        components: { AppLayout, SpiningLoader },
        props: {
            project: Object
        },
        data() {
            return {
                loading: true,
                showErrorLoadingWebsite: false
            };
        },
        methods: {
            handleLoad() {
                this.loading = false;
            }
        },
    };

</script>
