<template>

    <div class="flex justify-center">

        <el-popover v-if="canSendReminder() && hasSentReminder()" :width="400">

            <template #reference>
                <svg class="h-6 w-6 text-green-500 cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </template>

            <template #default>
                <span>
                    Sent Date
                    <hr class="my-4">
                    {{ moment(sentReminderAt()).format('lll') }}
                </span>
            </template>

        </el-popover>

        <div class="flex space-x-2 items-center whitespace-nowrap" v-else-if="canSendReminder() && !hasSentReminder()">

            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            <Countdown :time="milliSecondsLeft()"></Countdown>

        </div>

        <span v-else>...</span>

    </div>

</template>
<script>

    import Countdown from './../../../../Partials/Countdown.vue';
    import moment from "moment";

    export default {
        components: {
            Countdown
        },
        props: {
            hours: Number,
            autoBillingSchedule: Object
        },
        data() {
            return {
                moment: moment
            }
        },
        methods: {
            canSendReminder() {

                var reminders = this.autoBillingSchedule.subscription_plan.auto_billing_reminders;

                for (let index = 0; index < reminders.length; index++) {

                    const reminder = reminders[index];

                    if(reminder.hours == this.hours) {

                        return true;

                    }

                }

                return false;

            },
            hasSentReminder() {

                for (let index = 0; index < 6; index++) {

                    if(this.hours == 1 && this.autoBillingSchedule.reminded_one_hour_before_at != null) {

                        return true;

                    }else if(this.hours == 6 && this.autoBillingSchedule.reminded_six_hours_before_at != null) {

                        return true;

                    }else if(this.hours == 12 && this.autoBillingSchedule.reminded_twelve_hours_before_at != null) {

                        return true;

                    }else if(this.hours == 24 && this.autoBillingSchedule.reminded_twenty_four_hours_before_at != null) {

                        return true;

                    }else if(this.hours == 48 && this.autoBillingSchedule.reminded_forty_eight_hours_before_at != null) {

                        return true;

                    }else if(this.hours == 72 && this.autoBillingSchedule.reminded_seventy_two_hours_before_at != null) {

                        return true;

                    }

                }

                return false;

            },
            sentReminderAt() {

                for (let index = 0; index < 6; index++) {

                    if(this.hours == 1) {

                        return this.autoBillingSchedule.reminded_one_hour_before_at;

                    }else if(this.hours == 6) {

                        return this.autoBillingSchedule.reminded_six_hours_before_at;

                    }else if(this.hours == 12) {

                        return this.autoBillingSchedule.reminded_twelve_hours_before_at;

                    }else if(this.hours == 24) {

                        return this.autoBillingSchedule.reminded_twenty_four_hours_before_at;

                    }else if(this.hours == 48) {

                        return this.autoBillingSchedule.reminded_forty_eight_hours_before_at;

                    }else if(this.hours == 72) {

                        return this.autoBillingSchedule.reminded_seventy_two_hours_before_at;

                    }

                }
            },
            milliSecondsLeft() {

                for (let index = 0; index < 6; index++) {

                    if(this.hours == 1) {

                        return this.autoBillingSchedule.reminded_one_hour_before_milli_seconds_left;

                    }else if(this.hours == 6) {

                        return this.autoBillingSchedule.reminded_six_hours_before_milli_seconds_left;

                    }else if(this.hours == 12) {

                        return this.autoBillingSchedule.reminded_twelve_hours_before_milli_seconds_left;

                    }else if(this.hours == 24) {

                        return this.autoBillingSchedule.reminded_twenty_four_hours_before_milli_seconds_left;

                    }else if(this.hours == 48) {

                        return this.autoBillingSchedule.reminded_forty_eight_hours_milli_seconds_left;

                    }else if(this.hours == 72) {

                        return this.autoBillingSchedule.reminded_seventy_two_hours_before_milli_seconds_left;

                    }

                }

            }
        }
    }
</script>
