import{_ as p}from"./AppLayout-feb9eb21.js";import e from"./Content-58c18750.js";import l from"./ManageSubscriptionPlanModal-a79a5337.js";import{_ as m}from"./_plugin-vue_export-helper-c27b6911.js";import{q as c,s as t,o as u,c as b,w as d,a as i,b as r}from"./app-1a0996f9.js";import"./moment-fbc5633a.js";import"./ActiveStatusBadge-33235c88.js";/* empty css            */import"./FolderStatusBadge-755a0c00.js";import"./Pagination-9a08c685.js";import"./base-d4a8cc75.js";import"./el-tag-6d0cf1ba.js";import"./constants-e299eaa8.js";import"./icon-f616c2e7.js";import"./index-73c69c87.js";import"./el-select-6667f448.js";import"./el-popper-d20d1716.js";import"./el-scrollbar-e18a62f1.js";import"./el-switch-be0da6f3.js";import"./el-popover-87bffa09.js";import"./el-breadcrumb-item-19df37fe.js";import"./TextInput-24000092.js";import"./InputLabel-64902c75.js";import"./Textarea-26d89e75.js";import"./PrimaryButton-2d93dae9.js";import"./SelectInput-f6f94617.js";import"./DialogModal-4e9f6323.js";import"./DangerButton-4295d93d.js";import"./SecondaryButton-5bb69dfa.js";const P=c({components:{AppLayout:p,SubscriptionPlansContent:e,ManageSubscriptionPlanModal:l},props:{breadcrumbs:Array,totalSubscriptions:Number,autoBillingReminders:Array,parentSubscriptionPlan:Object,subscriptionPlansPayload:Object}}),_={class:"py-12"},f={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"};function y(o,S,g,B,R,h){const n=t("manage-subscription-plan-modal"),a=t("subscription-plans-content"),s=t("app-layout");return u(),b(s,{title:"Dashboard"},{default:d(()=>[i("div",_,[i("div",f,[r(n,{parentSubscriptionPlan:o.parentSubscriptionPlan,subscriptionPlansPayload:o.subscriptionPlansPayload,autoBillingReminders:o.autoBillingReminders,breadcrumbs:o.breadcrumbs,showHeader:!0},null,8,["parentSubscriptionPlan","subscriptionPlansPayload","autoBillingReminders","breadcrumbs"]),r(a,{parentSubscriptionPlan:o.parentSubscriptionPlan,subscriptionPlansPayload:o.subscriptionPlansPayload,totalSubscriptions:o.totalSubscriptions,autoBillingReminders:o.autoBillingReminders,breadcrumbs:o.breadcrumbs},null,8,["parentSubscriptionPlan","subscriptionPlansPayload","totalSubscriptions","autoBillingReminders","breadcrumbs"])])])]),_:1})}const Z=m(P,[["render",y]]);export{Z as default};