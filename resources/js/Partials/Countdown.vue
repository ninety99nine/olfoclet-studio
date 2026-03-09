<template>

    <vue-countdown :time="time" v-slot="{ days, hours, minutes, seconds }">
        <template v-if="days > 0">
            <span>{{ days }} {{ days == 1 ? 'day ' : 'days '}}</span>
            <template v-if="hours > 0">
                <span>{{ hours }} {{ hours == 1 ? 'hr ' : 'hrs '}}</span>
            </template>
        </template>
        <template v-else-if="hours > 0">
            <span>{{ hours }} {{ hours == 1 ? 'hr ' : 'hrs '}}</span>
            <template v-if="minutes > 0">
                <span>{{ minutes }} {{ minutes == 1 ? 'min ' : 'mins '}}</span>
            </template>
        </template>
        <template v-else-if="minutes > 0">
            <span>{{ minutes }} {{ minutes == 1 ? 'min ' : 'mins '}}</span>
            <template v-if="seconds > 0">
                <span>{{ seconds }} {{ seconds == 1 ? 'sec ' : 'secs '}}</span>
            </template>
        </template>
        <template v-else-if="seconds > 0">
            <span>{{ seconds }} {{ seconds == 1 ? 'sec ' : 'secs '}}</span>
        </template>
        <template v-else>
            <span class="waiting-badge">
                <slot>Pending</slot>
            </span>
        </template>
    </vue-countdown>

</template>

<script>

    import VueCountdown from '@chenfengyuan/vue-countdown';

    export default {
        components: {
            VueCountdown
        },
        props: {
            time: Number
        }
    }
</script>

<style scoped>
.waiting-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 10px;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
    background-color: #f1f5f9;
    color: #1e293b;
}
</style>
