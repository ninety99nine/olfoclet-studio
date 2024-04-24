import"./base-44b135f9.js";import"./el-popper-1a2e7b76.js";import{E as a}from"./el-popover-88f24671.js";import{C as c}from"./Countdown-9650b996.js";import{h as m}from"./moment-fbc5633a.js";import{_ as f}from"./_plugin-vue_export-helper-c27b6911.js";import{s as g,o,e as s,c as p,w as n,b as w,a as r,g as l,t as S}from"./app-5698ab4f.js";import"./icon-4c2d7b76.js";import"./constants-0e9b956e.js";/* empty css            */const B={components:{Countdown:c},props:{hours:Number,autoBillingSchedule:Object},data(){return{moment:m}},methods:{canSendReminder(){var e=this.autoBillingSchedule.subscription_plan.auto_billing_reminders;for(let i=0;i<e.length;i++)if(e[i].hours==this.hours)return!0;return!1},hasSentReminder(){for(let e=0;e<6;e++){if(this.hours==1&&this.autoBillingSchedule.reminded_one_hour_before_at!=null)return!0;if(this.hours==6&&this.autoBillingSchedule.reminded_six_hours_before_at!=null)return!0;if(this.hours==12&&this.autoBillingSchedule.reminded_twelve_hours_before_at!=null)return!0;if(this.hours==24&&this.autoBillingSchedule.reminded_twenty_four_hours_before_at!=null)return!0;if(this.hours==48&&this.autoBillingSchedule.reminded_forty_eight_hours_before_at!=null)return!0;if(this.hours==72&&this.autoBillingSchedule.reminded_seventy_two_hours_before_at!=null)return!0}return!1},sentReminderAt(){for(let e=0;e<6;e++){if(this.hours==1)return this.autoBillingSchedule.reminded_one_hour_before_at;if(this.hours==6)return this.autoBillingSchedule.reminded_six_hours_before_at;if(this.hours==12)return this.autoBillingSchedule.reminded_twelve_hours_before_at;if(this.hours==24)return this.autoBillingSchedule.reminded_twenty_four_hours_before_at;if(this.hours==48)return this.autoBillingSchedule.reminded_forty_eight_hours_before_at;if(this.hours==72)return this.autoBillingSchedule.reminded_seventy_two_hours_before_at}},milliSecondsLeft(){for(let e=0;e<6;e++){if(this.hours==1)return this.autoBillingSchedule.reminded_one_hour_before_milli_seconds_left;if(this.hours==6)return this.autoBillingSchedule.reminded_six_hours_before_milli_seconds_left;if(this.hours==12)return this.autoBillingSchedule.reminded_twelve_hours_before_milli_seconds_left;if(this.hours==24)return this.autoBillingSchedule.reminded_twenty_four_hours_before_milli_seconds_left;if(this.hours==48)return this.autoBillingSchedule.reminded_forty_eight_hours_milli_seconds_left;if(this.hours==72)return this.autoBillingSchedule.reminded_seventy_two_hours_before_milli_seconds_left}}}},b={class:"flex justify-center"},x=r("svg",{class:"h-6 w-6 text-green-500 cursor-pointer",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[r("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"m4.5 12.75 6 6 9-13.5"})],-1),v=r("hr",{class:"my-4"},null,-1),k={key:1,class:"flex space-x-2 items-center whitespace-nowrap"},y=r("svg",{class:"h-4 w-4",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[r("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"})],-1),R={key:2};function C(e,i,u,j,h,t){const _=a,d=g("Countdown");return o(),s("div",b,[t.canSendReminder()&&t.hasSentReminder()?(o(),p(_,{key:0,width:400},{reference:n(()=>[x]),default:n(()=>[r("span",null,[l(" Sent Date "),v,l(" "+S(h.moment(t.sentReminderAt()).format("lll")),1)])]),_:1})):t.canSendReminder()&&!t.hasSentReminder()?(o(),s("div",k,[y,w(d,{time:t.milliSecondsLeft()},null,8,["time"])])):(o(),s("span",R,"..."))])}const Z=f(B,[["render",C]]);export{Z as default};
