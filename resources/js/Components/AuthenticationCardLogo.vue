<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
        <div class="flex items-center">
            <img src="/images/logo-bold.png" alt="Logo" class="w-40">
            <span style="color:#1b64f1; font-weight: 400;">Studio</span>
        </div>
    </Link>
</template>
