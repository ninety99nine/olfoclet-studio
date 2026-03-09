<script setup>

import { ref, computed, watch, onMounted } from 'vue';
import Toast from 'primevue/toast';
import Banner from '@/Components/Banner.vue';
import NavLink from '@/Components/NavLink.vue';
import SpiningLoader from '@/Components/SpiningLoader.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import SlideUpDown from 'vue-slide-up-down';

defineProps({
    title: String,
});

// Main tabs: Subscribers, Subscriptions, Transactions, Schedules, Analytics, Reports, SMS; then "More"
const navItems = ref([
    // —— Main tabs ——
    { type: 'link', label: 'Subscriptions', routeName: 'show.subscriptions', activeRouteNames: ['show.subscriptions', 'show.subscription'], permission: 'View subscriptions' },
    { type: 'link', label: 'Transactions', routeName: 'show.transactions', activeRouteNames: ['show.transactions', 'show.transaction'], permission: 'View billing transactions' },
    { type: 'link', label: 'Subscribers', routeName: 'show.subscribers', activeRouteNames: ['show.subscribers', 'show.subscriber'], permission: 'View subscribers' },
    {
        type: 'group',
        label: 'Schedules',
        children: [
            { label: 'Auto Billing Schedules', routeName: 'show.auto.billing.schedules', activeRouteNames: ['show.auto.billing.schedules'], permission: 'View auto billing schedules' },
            { label: 'SMS Schedules', routeName: 'show.sms.campaign.schedules', activeRouteNames: ['show.sms.campaign.schedules'], permission: 'View sms campaign schedules' },
        ],
    },
    { type: 'link', label: 'Analytics', routeName: 'show.analytics', activeRouteNames: ['show.analytics'] },
    { type: 'link', label: 'Reports', routeName: 'show.billing.reports', activeRouteNames: ['show.billing.reports', 'show.billing.report'], permission: 'View billing reports' },
    { type: 'link', label: 'SMS', routeName: 'show.subscriber.messages', activeRouteNames: ['show.subscriber.messages', 'show.subscriber.message'], permission: 'View subscriber messages' },
    // —— Secondary (not center of attention) ——
    {
        type: 'group',
        label: 'More',
        secondary: true,
        children: [
            { label: 'Profile', routeName: 'profile.show', activeRouteNames: ['profile.show'] },
            { label: 'Server', routeName: 'show.server', activeRouteNames: ['show.server'], requiresSuperAdmin: true },
            { label: 'About', routeName: 'show.project.about', activeRouteNames: ['show.project.about'] },
            { label: 'Users', routeName: 'show.users', activeRouteNames: ['show.users', 'show.user'], permission: 'View users' },
            { label: 'Topics', routeName: 'show.topics', activeRouteNames: ['show.topics', 'show.topic'], permission: 'View topics' },
            { label: 'Messages', routeName: 'show.messages', activeRouteNames: ['show.messages', 'show.message'], permission: 'View messages' },
            { label: 'Pricing Plans', routeName: 'show.pricing.plans', activeRouteNames: ['show.pricing.plans', 'show.pricing.plan'], permission: 'View pricing plans' },
            { label: 'Sms Campaigns', routeName: 'show.sms.campaigns', activeRouteNames: ['show.sms.campaigns', 'show.sms.campaign.job.batches'], permission: 'View sms campaigns' },
        ],
    },
]);

const expandedGroups = ref(new Set());

const filteredNavItems = computed(() => {
    return navItems.value
        .map((item) => {
            if (item.type === 'link') {
                return canShowLink(item.permission ?? null) ? item : null;
            }
            const filteredChildren = item.children.filter((c) => {
                if (c.requiresSuperAdmin && !page.props.auth?.user?.is_super_admin) return false;
                return canShowLink(c.permission ?? null);
            });
            return filteredChildren.length ? { ...item, children: filteredChildren } : null;
        })
        .filter(Boolean);
});

function isGroupActive(group) {
    return group.children.some((c) => c.activeRouteNames.some((name) => route().current(name)));
}

function toggleGroup(label) {
    const isCurrentlyOpen = expandedGroups.value.has(label);
    if (isCurrentlyOpen) {
        expandedGroups.value = new Set();
    } else {
        expandedGroups.value = new Set([label]);
    }
}

function isGroupExpanded(label) {
    return expandedGroups.value.has(label);
}

function expandActiveGroup() {
    const current = route().current();
    if (!current) return;
    for (const item of filteredNavItems.value) {
        if (item.type === 'group' && item.children.some((c) => c.activeRouteNames.includes(current))) {
            expandedGroups.value = new Set([item.label]);
            break;
        }
    }
}

const page = usePage();
const isLoggingOut = ref(false);

onMounted(expandActiveGroup);
watch(() => page.url, expandActiveGroup);

const isShowingProject = computed(() => {
    return route().params.hasOwnProperty('project');
});

const isProjectsListPage = computed(() => route().current('show.projects'));

const canShowLink = (permission) => {
    if(permission == null) return true;
    return page.props.projectPermissions.includes('*') || page.props.projectPermissions.includes(permission);
};

const activeLinkClasses = (activeRouteNames) => {
    for (let i = 0; i < activeRouteNames.length; i++) {
        var routeName = activeRouteNames[i];
        if(route().current(routeName)) return 'bg-gray-100 dark:bg-gray-700/50 border-l-2 border-gray-300 dark:border-gray-500';
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

// Watch for project changes to update localStorage and axios headers
watch(
  () => page.props.project,
  (newProject) => {
    const token = newProject?.secret_token || null;
    if (token) {
      localStorage.setItem('projectToken', token);
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    } else {
      localStorage.removeItem('projectToken');
      const accessToken = localStorage.getItem('accessToken');
      if (accessToken) {
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`;
      } else {
        delete window.axios.defaults.headers.common['Authorization'];
      }
    }
  },
  { immediate: true }
);

const currentProjectName = computed(() => page.props.project?.name ?? null);
</script>

<template>
    <div>

        <Head :title="title" />

        <Toast position="top-right" />

        <Banner />

        <button data-drawer-target="main-sidebar" data-drawer-toggle="main-sidebar" aria-controls="main-sidebar" type="button" class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
        </button>

        <aside id="main-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0" :class="{ '!translate-x-0': isProjectsListPage }" aria-label="Sidebar">

            <div class="h-full flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">

                <!-- Profile at top -->
                <div class="shrink-0 px-4 pt-4 pb-1.5 border-b border-gray-100 dark:border-gray-600">
                    <p class="font-semibold text-gray-900 dark:text-gray-100 text-sm truncate">{{ $page.props.auth.user.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5" :title="$page.props.auth.user.email">{{ $page.props.auth.user.email }}</p>
                </div>

                <!-- Nav: scrollable -->
                <nav class="flex-1 overflow-y-auto min-h-0 px-3 py-1.5 sidebar-nav-scroll">
                <ul class="space-y-0.5">
                    <template v-if="isShowingProject">
                        <li class="mt-1 pt-1.5" aria-hidden="true" />
                        <template v-for="(item, index) in filteredNavItems" :key="index">
                            <!-- Single link (main tabs) -->
                            <li v-if="item.type === 'link'" @click="navigateToNavMenu(item.routeName)" :class="['w-full pl-4 pr-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 cursor-pointer rounded-r-lg border-l-2 border-transparent', activeLinkClasses(item.activeRouteNames)]">
                                <span>{{ item.label }}</span>
                            </li>
                            <!-- Collapsible group: Billing, Schedule -->
                            <li v-else-if="item.type === 'group' && !item.secondary" class="space-y-0.5">
                                <div
                                    @click="toggleGroup(item.label)"
                                    :class="[isGroupActive(item) ? 'bg-gray-100 dark:bg-gray-700/50 border-l-2 border-gray-300 dark:border-gray-500' : 'border-l-2 border-transparent', 'w-full pl-4 pr-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 cursor-pointer rounded-r-lg flex items-center justify-between']"
                                >
                                    <span>{{ item.label }}</span>
                                    <svg class="w-4 h-4 transition-transform shrink-0 text-gray-500" :class="{ 'rotate-180': isGroupExpanded(item.label) }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                <SlideUpDown :active="isGroupExpanded(item.label)" :duration="200" tag="ul" class="ml-3 pl-3 space-y-0.5 border-l border-gray-200 dark:border-gray-600">
                                    <li v-for="(child, childIndex) in item.children" :key="childIndex" @click.stop="navigateToNavMenu(child.routeName)" :class="['w-full pl-3 pr-2 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer rounded-r-lg border-l-2 border-transparent', activeLinkClasses(child.activeRouteNames)]">
                                        <span>{{ child.label }}</span>
                                    </li>
                                </SlideUpDown>
                            </li>
                            <!-- Secondary "More" (About, Users, Topics, etc.) – divider + muted -->
                            <li v-else-if="item.type === 'group' && item.secondary" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 space-y-0.5">
                                <div
                                    @click="toggleGroup(item.label)"
                                    :class="[isGroupActive(item) ? 'bg-gray-50 dark:bg-gray-700/30' : '', 'w-full pl-4 pr-3 py-2 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer rounded-r-lg flex items-center justify-between']"
                                >
                                    <span>{{ item.label }}</span>
                                    <svg class="w-3.5 h-3.5 transition-transform shrink-0" :class="{ 'rotate-180': isGroupExpanded(item.label) }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                <SlideUpDown :active="isGroupExpanded(item.label)" :duration="200" tag="ul" class="ml-3 pl-3 mt-0.5 space-y-0.5 border-l border-gray-200 dark:border-gray-600">
                                    <li v-for="(child, childIndex) in item.children" :key="childIndex" @click.stop="navigateToNavMenu(child.routeName)" :class="['w-full pl-3 pr-2 py-1.5 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer rounded-r-lg border-l-2 border-transparent', activeLinkClasses(child.activeRouteNames)]">
                                        <span>{{ child.label }}</span>
                                    </li>
                                </SlideUpDown>
                            </li>
                        </template>
                    </template>

                </ul>
                </nav>

                <!-- Sidebar footer: current project, My Projects, Sign Out -->
                <div class="shrink-0 px-4 pt-3 pb-10 border-t border-gray-200 dark:border-gray-600 space-y-2">
                    <!-- Current project indicator -->
                    <div v-if="currentProjectName" class="py-1.5">
                        <p class="text-[10px] font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Current project</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" :title="currentProjectName">{{ currentProjectName }}</p>
                    </div>
                    <!-- My Projects link -->
                    <div @click="navigateToNavMenu('show.projects')" :class="[activeLinkClasses(['show.projects']), 'flex items-center gap-2 w-full py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-100 cursor-pointer rounded-r-lg border-l-2 border-transparent -ml-4 pl-4']">
                        <svg class="w-4 h-4 shrink-0 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75a2.25 2.25 0 0 1 2.25-2.25H6a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 6 20.25h-.75a2.25 2.25 0 0 1-2.25-2.25v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        <span>My Projects</span>
                    </div>
                    <!-- Sign Out -->
                    <div @click="logout" class="flex items-center gap-3 w-full py-2 -ml-4 pl-4 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer rounded-r-lg border-l-2 border-transparent">
                        <SpiningLoader v-if="isLoggingOut" class="my-1 shrink-0" />
                        <svg v-else class="w-5 h-5 shrink-0 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span>Sign Out</span>
                    </div>
                </div>

            </div>

        </aside>

        <div class="min-h-screen bg-slate-50 dark:bg-slate-900/30" :class="{ 'sm:ml-64': !isProjectsListPage, 'ml-64': isProjectsListPage }">
            <slot />

        </div>

    </div>
</template>
