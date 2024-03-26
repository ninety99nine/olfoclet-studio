<template>
    <app-layout title="Dashboard">

        <div v-show="loading" class="flex justify-center items-center h-screen">
            <SpiningLoader size="md"></SpiningLoader>
            <h1 class="font-bold ml-4">{{ project.name }}</h1>
        </div>

        <iframe v-show="!loading" :src="project.about_url"  class="w-full h-screen" @load="handleLoad"></iframe>

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
                loading: true
            };
        },
        methods: {
            handleLoad() {
                console.log('loading');
                this.loading = false;
            },
        },
    };

</script>
