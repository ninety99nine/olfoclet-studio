<template>

    <div class="flex justify-center">

        <svg v-if="canSendReminder() && hasSentReminder()" class="h-4 w-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>

        <svg v-else-if="canSendReminder() && !hasSentReminder()" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>

        <span v-else>...</span>

    </div>

</template>
<script>

    export default {
        props: {
            hours: Number,
            autoBillingSchedule: Object
        },
        data() {
            return {
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

                    if(this.hours == 1 && this.autoBillingSchedule.reminded_one_hour_before) {

                        return true;

                    }else if(this.hours == 6 && this.autoBillingSchedule.reminded_six_hours_before) {

                        return true;

                    }else if(this.hours == 12 && this.autoBillingSchedule.reminded_twelve_hours_before) {

                        return true;

                    }else if(this.hours == 24 && this.autoBillingSchedule.reminded_twenty_four_hours_before) {

                        return true;

                    }else if(this.hours == 48 && this.autoBillingSchedule.reminded_forty_eight_hours_before) {

                        return true;

                    }else if(this.hours == 72 && this.autoBillingSchedule.reminded_seventy_two_hours_before) {

                        return true;

                    }

                }

                return false;

            }
        }
    }
</script>
