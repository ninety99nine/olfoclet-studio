import{_ as p}from"./AppLayout-eeb18a87.js";import c from"./Content-fc8e3778.js";import m from"./ManageSubscriberModal-d21fdc34.js";import{_ as n}from"./_plugin-vue_export-helper-c27b6911.js";import{s as l,c as b,w as _,x as o,o as d,a as t,b as r}from"./app-a9929219.js";import"./SubscriptionStatusBadge-87ce8c3a.js";import"./Pagination-c426c16e.js";import"./BillingStatusBadge-be8e375b.js";import"./base-210fcbe9.js";import"./el-popover-69565d82.js";import"./icon-6945ac30.js";import"./constants-a140f488.js";import"./moment-fbc5633a.js";import"./TextInput-854b9238.js";import"./InputLabel-831d05d4.js";import"./PrimaryButton-151237c0.js";import"./SelectInput-0f0594b1.js";import"./DialogModal-654df02b.js";import"./DangerButton-19641c8b.js";import"./ActionMessage-55ec1a2c.js";import"./SecondaryButton-c49f7243.js";/* empty css            */const u=l({components:{AppLayout:p,SubscribersContent:c,CreateSubscriberModal:m},props:{totalMessages:Number,subscribersPayload:Object},created(){console.log(this.subscribersPayload)}}),f={class:"py-12"},y={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"};function x(s,h,g,M,C,P){const e=o("create-subscriber-modal"),a=o("subscribers-content"),i=o("app-layout");return d(),b(i,{title:"Dashboard"},{default:_(()=>[t("div",f,[t("div",y,[r(e,{showAddbutton:!0}),r(a,{subscribersPayload:s.subscribersPayload,totalMessages:s.totalMessages},null,8,["subscribersPayload","totalMessages"])])])]),_:1})}const Q=n(u,[["render",x]]);export{Q as default};