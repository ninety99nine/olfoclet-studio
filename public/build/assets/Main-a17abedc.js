import{_ as p}from"./AppLayout-c04c7d8b.js";import e from"./Content-7e2e994f.js";import l from"./ManageSubscriptionPlanModal-9079273e.js";import{_ as m}from"./_plugin-vue_export-helper-c27b6911.js";import{q as c,s as t,o as u,c as b,w as d,a as i,b as r}from"./app-5df439d4.js";import"./moment-fbc5633a.js";import"./ActiveStatusBadge-98275469.js";/* empty css            */import"./FolderStatusBadge-df832b82.js";import"./Pagination-f9b3d63f.js";import"./base-a1fdb940.js";import"./el-tag-55128fdf.js";import"./constants-f0843242.js";import"./icon-a633b9f2.js";import"./index-409ab19b.js";import"./el-select-8a46c5af.js";import"./el-popper-f6308475.js";import"./el-scrollbar-1f3fdccf.js";import"./el-switch-b01f701c.js";import"./el-popover-eb9df5a5.js";import"./el-breadcrumb-item-6978d521.js";import"./TextInput-eda73175.js";import"./InputLabel-c6291185.js";import"./Textarea-44f4092e.js";import"./PrimaryButton-b0ed6599.js";import"./SelectInput-76450c60.js";import"./DialogModal-10e5bc3d.js";import"./DangerButton-47cd5dbc.js";import"./SecondaryButton-ec912512.js";const P=c({components:{AppLayout:p,SubscriptionPlansContent:e,ManageSubscriptionPlanModal:l},props:{breadcrumbs:Array,totalSubscriptions:Number,autoBillingReminders:Array,parentSubscriptionPlan:Object,subscriptionPlansPayload:Object}}),_={class:"py-12"},f={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"};function y(o,S,g,B,R,h){const n=t("manage-subscription-plan-modal"),a=t("subscription-plans-content"),s=t("app-layout");return u(),b(s,{title:"Dashboard"},{default:d(()=>[i("div",_,[i("div",f,[r(n,{parentSubscriptionPlan:o.parentSubscriptionPlan,subscriptionPlansPayload:o.subscriptionPlansPayload,autoBillingReminders:o.autoBillingReminders,breadcrumbs:o.breadcrumbs,showHeader:!0},null,8,["parentSubscriptionPlan","subscriptionPlansPayload","autoBillingReminders","breadcrumbs"]),r(a,{parentSubscriptionPlan:o.parentSubscriptionPlan,subscriptionPlansPayload:o.subscriptionPlansPayload,totalSubscriptions:o.totalSubscriptions,autoBillingReminders:o.autoBillingReminders,breadcrumbs:o.breadcrumbs},null,8,["parentSubscriptionPlan","subscriptionPlansPayload","totalSubscriptions","autoBillingReminders","breadcrumbs"])])])]),_:1})}const Z=m(P,[["render",y]]);export{Z as default};