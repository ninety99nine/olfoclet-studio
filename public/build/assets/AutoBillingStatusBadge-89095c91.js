import{_ as r}from"./_plugin-vue_export-helper-c27b6911.js";import{o as n,e as o,g as s,t as i,a as e}from"./app-cc174bfa.js";/* empty css            */const a={props:{autoBillingSubscriptionPlanJobBatch:Object},data(){return{}}},l={key:0,class:"bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900"},d=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-3 w-3 mr-1",viewBox:"0 0 20 20",fill:"currentColor"},[e("path",{"fill-rule":"evenodd",d:"M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z","clip-rule":"evenodd"})],-1),c={key:1,class:"bg-red-100 text-red-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900"},u=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-3 w-3 mr-1 animate-bounce",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor","stroke-width":"2"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"})],-1),g={key:2,class:"bg-blue-100 text-blue-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-900"},m=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-3 w-3 mr-1 animate-spin",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"})],-1),p={key:3,class:"bg-gray-100 text-gray-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-2 dark:bg-gray-200 dark:text-gray-900"},h=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-3 w-3 mr-1 animate-pulse",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor","stroke-width":"2"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"})],-1);function x(b,_,t,w,k,f){return n(),o("div",null,[t.autoBillingSubscriptionPlanJobBatch.progress==100&&t.autoBillingSubscriptionPlanJobBatch.failed_jobs==0?(n(),o("span",l,[d,s(" Successful ")])):t.autoBillingSubscriptionPlanJobBatch.failed_jobs>0?(n(),o("span",c,[u,s(" "+i(t.autoBillingSubscriptionPlanJobBatch.failed_jobs)+" Failed ",1)])):t.autoBillingSubscriptionPlanJobBatch.progress>0&&t.autoBillingSubscriptionPlanJobBatch.progress<100?(n(),o("span",g,[m,s(" Running ")])):(n(),o("span",p,[h,s(" Waiting ")]))])}const S=r(a,[["render",x]]);export{S as default};