<script setup>

import { ref, computed } from 'vue';
import Banner from '@/Components/Banner.vue';
import NavLink from '@/Components/NavLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SpiningLoader from '@/Components/SpiningLoader.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const links = ref([
    {
        label: 'About',
        routeName: 'show.project.about',
        activeRouteNames: [
            'show.project.about'
        ]
    },
    {
        label: 'Users',
        routeName: 'show.users',
        activeRouteNames: [
            'show.users',
        ],
        permission: 'View users'
    },
    {
        label: 'Topics',
        routeName: 'show.topics',
        activeRouteNames: [
            'show.topics',
            'show.topic'
        ],
        permission: 'View topics'
    },
    {
        label: 'Messages',
        routeName: 'show.messages',
        activeRouteNames: [
            'show.messages',
            'show.message',
        ],
        permission: 'View messages'
    },
    {
        label: 'Auto Billing',
        routeName: 'show.auto.billing.subscription.plans',
        activeRouteNames: [
            'show.auto.billing.subscription.plans',
            'show.auto.billing.subscription.plan.job.batches'
        ],
        permission: 'View auto billing subscription plans'
    },
    {
        label: 'Subscribers',
        routeName: 'show.subscribers',
        activeRouteNames: [
            'show.subscribers',
        ],
        permission: 'View subscribers'
    },
    {
        label: 'Subscriptions',
        routeName: 'show.subscriptions',
        activeRouteNames: [
            'show.subscriptions'
        ],
        permission: 'View subscriptions'
    },
    {
        label: 'Sms Campaigns',
        routeName: 'show.sms.campaigns',
        activeRouteNames: [
            'show.sms.campaigns',
            'show.sms.campaign.job.batches',
        ],
        permission: 'View sms campaigns'
    },
    {
        label: 'Subscription Plans',
        routeName: 'show.subscription.plans',
        activeRouteNames: [
            'show.subscription.plans',
            'show.subscription.plan'
        ],
        permission: 'View subscription plans'
    },
    {
        label: 'Billing Transactions',
        routeName: 'show.billing.transactions',
        activeRouteNames: [
            'show.billing.transactions'
        ],
        permission: 'View billing transactions'
    },
    {
        label: 'Subscriber Messages',
        routeName: 'show.subscriber.messages',
        activeRouteNames: [
            'show.subscriber.messages',
        ],
        permission: 'View subscriber messages'
    },
    {
        label: 'Auto Billing Reminders',
        routeName: 'show.auto.billing.reminder.subscription.plans',
        activeRouteNames: [
            'show.auto.billing.reminder.subscription.plans',
            'show.auto.billing.subscription.plan.reminder.job.batches'
        ],
        permission: 'View auto billing reminder subscription plans'
    },
]);

const filteredLinks = computed(() => {
    return links.value.filter(link => canShowLink(link.permission ?? null));
});

const page = usePage()
const isLoggingOut = ref(false);

const isShowingProject = computed(() => {
    return route().params.hasOwnProperty('project');
});

const canShowLink = (permission) => {
    if(permission == null) return true;
    return page.props.projectPermissions.includes('*') || page.props.projectPermissions.includes(permission);
};

const activeLinkClasses = (activeRouteNames) => {
    for (let i = 0; i < activeRouteNames.length; i++) {
        var routeName = activeRouteNames[i];
        if(route().current(routeName)) return 'bg-gray-200';
    }
    return '';
};

const navigateToNavMenu = (routeName) => {
    const url = route(routeName, { project: route().params.project });
    router.get(url);
};

const logout = () => {
    isLoggingOut.value = true;
    router.post(route('logout'));
};
</script>

<template>
    <div>

        <Head :title="title" />

        <Banner />

        <button data-drawer-target="main-sidebar" data-drawer-toggle="main-sidebar" aria-controls="main-sidebar" type="button" class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
        </button>

        <aside id="main-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">

            <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">

                <div class="flex justify-center">

                    <!-- Logo -->
                    <Link :href="route('show.projects')">
                        <ApplicationMark class="block mt-4" />
                    </Link>

                </div>

                <div class="text-center px-4 py-4 mb-2">
                    <p>{{ $page.props.auth.user.name }}</p>
                    <p class="text-sm text-gray-500">{{ $page.props.auth.user.email }}</p>
                </div>

                <ul class="space-y-1 font-medium">

                    <template v-if="isShowingProject">

                        <li><div class="border-t my-2"></div></li>

                        <li v-for="(link, index) in filteredLinks" :key="index" @click="navigateToNavMenu(link.routeName)" :class="[activeLinkClasses(link.activeRouteNames), 'w-full px-4 py-2 text-sm hover:bg-gray-200 active:bg-gray-300 cursor-pointer rounded-lg']">
                            <span>{{ link.label }}</span>
                        </li>

                        <li><div class="border-t my-2"></div></li>

                    </template>

                    <li @click="navigateToNavMenu('show.projects')" :class="[activeLinkClasses(['show.projects']), 'flex items-center space-x-2 w-full px-4 py-2 text-sm hover:bg-gray-200 active:bg-gray-300 cursor-pointer rounded-lg']">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                        </svg>

                        <span>Projects</span>
                    </li>

                    <li @click="navigateToNavMenu('profile.show')" :class="[activeLinkClasses(['profile.show']), 'flex items-center space-x-2 w-full px-4 py-2 text-sm hover:bg-gray-200 active:bg-gray-300 cursor-pointer rounded-lg']">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>

                        <span>Profile</span>
                    </li>

                    <li @click="logout" class="flex items-center space-x-2 w-full px-4 py-2 text-sm hover:bg-gray-200 active:bg-gray-300 cursor-pointer rounded-lg">

                        <SpiningLoader v-if="isLoggingOut" class="my-1"></SpiningLoader>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>

                        <span>Sign Out</span>

                    </li>

                </ul>

            </div>

        </aside>

        <div class="sm:ml-64">

            <!-- Page Content -->
            <slot />

        </div>

    </div>
</template>
