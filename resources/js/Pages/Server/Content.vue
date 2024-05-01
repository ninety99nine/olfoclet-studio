<template>

    <div>

        <div class="bg-gray-50 shadow rounded-lg pt-4 pl-6 mb-6">

            <p class="text-2xl font-semibold leading-6 text-gray-500 mb-4">Server</p>
            <div class="text-gray-500 text-sm pb-4">Manage server using server commands</div>

        </div>

        <!-- Success Message -->
        <div v-if="showSuccessMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">{{ successMessage }}</strong>
            <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>

        <!-- Error Message -->
        <div v-if="showErrorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">{{ errorMessage }}</strong>
            <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>

        </div>

        <div class="space-y-4">

            <div v-for="(category, index1) in categories" :key="index1" class="border shadow-md rounded-md bg-white p-4 divide-y">

                <h1 class="text-xl font-bold mb-4"><span class="text-gray-400">#</span> {{ category.header }}</h1>

                <div v-for="(option, index2) in category.options" :key="index2" class="flex justify-between items-center space-x-8 py-4">

                    <div>
                        <p class="text-md font-medium">{{ option.title }}</p>
                        <p class="text-sm text-gray-400">{{ option.description }}</p>
                    </div>

                    <div @click="runCommand(option.routeName, option.successMessage, option.errorMessage)" class="text-xs inline-flex text-left items-center space-x-1 bg-gray-800 text-white rounded-lg py-2 px-4 whitespace-nowrap cursor-pointer hover:bg-gray-700 active:bg-gray-600">
                        <span>{{ option.commands[0] }}</span>
                        <span class="text-yellow-400">{{ option.commands[1] }}</span>
                    </div>

                </div>

            </div>

        </div>

    </div>

</template>

<script>

    import { defineComponent } from 'vue';

    export default defineComponent({
        data() {
            return {
                errorMessage: null,
                successMessage: null,
                showErrorTimeout: null,
                showErrorMessage: false,
                showSuccessTimeout: null,
                showSuccessMessage: false,
                categories: [
                    {
                        header: 'Special Commands',
                        options: [
                            {
                                routeName: 'handle.server.errors',
                                title: 'Handle server errors',
                                commands: ['php artisan', 'handle:server-errors'],
                                successMessage: 'Server errors handled successfully',
                                errorMessage: 'Failed to handle server errors',
                                description: 'Run this artisan command to clear the configuration, routes, views and cache files and then immediately cache the configuration, routes, views and event files.',
                            },
                        ]
                    },
                    {
                        header: 'Clearing Commands',
                        options: [
                            {
                                routeName: 'config.clear',
                                title: 'Clear the config cache',
                                commands: ['php artisan', 'config clear'],
                                successMessage: 'Config cleared successfully',
                                errorMessage: 'Failed to clear cached configurations',
                                description: 'This should be done before other commands because other commands might rely on updated configuration settings.',
                            },
                            {
                                routeName: 'route.clear',
                                title: 'Clear the route cache',
                                commands: ['php artisan', 'route clear'],
                                successMessage: 'Route cleared successfully',
                                errorMessage: 'Failed to clear cached routes',
                                description: 'This is particularly important if you\'ve made changes to routes or middleware that need to be recognized.',
                            },
                            {
                                routeName: 'view.clear',
                                title: 'Clear the view cache',
                                commands: ['php artisan', 'view clear'],
                                successMessage: 'View cleared successfully',
                                errorMessage: 'Failed to clear cached views',
                                description: 'It\'s good to clear views after routes and config to avoid serving stale views after updates to routes or configurations.',
                            },
                            {
                                routeName: 'cache.clear',
                                title: 'Clear the cache',
                                commands: ['php artisan', 'cache clear'],
                                successMessage: 'Cache cleared successfully',
                                errorMessage: 'Failed to clear cache',
                                description: 'This removes all data stored in the application cache, ensuring no stale data affects your application\'s behavior.',
                            },
                        ]
                    },
                    {
                        header: 'Caching Commands',
                        options: [
                            {
                                routeName: 'config.cache',
                                title: 'Cache the configuration',
                                commands: ['php artisan', 'config cache'],
                                successMessage: 'Configurations cached successfully',
                                errorMessage: 'Failed to cache configurations',
                                description: 'Creates a cache file for faster configuration loading. This is useful for production as it optimizes the loading of configuration values',
                            },
                            {
                                routeName: 'route.cache',
                                title: 'Cache the routes',
                                commands: ['php artisan', 'route cache'],
                                successMessage: 'Routes cached successfully',
                                errorMessage: 'Failed to cache routes',
                                description: 'Creates a route cache file for faster route registration and resolution. This is beneficial in production where routes do not change often.',
                            },
                            {
                                routeName: 'view.cache',
                                title: 'Cache the views',
                                commands: ['php artisan', 'view cache'],
                                successMessage: 'Views cached successfully',
                                errorMessage: 'Failed to cache views',
                                description: 'Compiles all Blade templates. This reduces the load on the server since views are pre-compiled and served faster.',
                            },
                            {
                                routeName: 'event.cache',
                                title: 'Cache the events',
                                commands: ['php artisan', 'event cache'],
                                successMessage: 'Events cached successfully',
                                errorMessage: 'Failed to cache events',
                                description: 'Caches the event listeners configuration. This helps speed up event dispatching by loading cached configurations instead of dynamically building them from scratch.',
                            },
                        ]
                    }
                ]
            }
        },
        methods: {

            /**
             *  FORM METHODS
             */
             runCommand(routeName, successMessage, errorMessage) {
                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess(successMessage);

                    },

                    onError: errors => {

                        this.handleOnError(errorMessage);

                    },

                };

                this.$inertia.post(route(routeName, { project: route().params.project }), null, options);
            },
            handleOnSuccess(successMessage){

                clearTimeout(this.showSuccessTimeout);

                this.successMessage = successMessage;
                this.showSuccessMessage = true;

                this.showSuccessTimeout = setTimeout(() => {
                    this.showSuccessMessage = false;
                }, 5000);

            },
            handleOnError(errorMessage){

                clearTimeout(this.showErrorTimeout);

                this.errorMessage = errorMessage;
                this.showErrorMessage = true;

                this.showErrorTimeout = setTimeout(() => {
                    this.showErrorMessage = false;
                }, 3000);

            },
        }
    })
</script>
