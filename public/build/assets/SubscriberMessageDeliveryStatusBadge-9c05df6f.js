import"./base-44b135f9.js";import"./el-popper-1a2e7b76.js";import{E as o}from"./el-popover-88f24671.js";import{_ as n}from"./_plugin-vue_export-helper-c27b6911.js";import{o as t,e as s,t as r,F as l,b as c,w as d,a}from"./app-5698ab4f.js";import"./icon-4c2d7b76.js";import"./constants-0e9b956e.js";/* empty css            */const _={props:{subscriberMessage:Object},data(){return{}}},u={class:"flex items-center"},m={key:0,class:"w-full block text-center"},p={key:1,class:"bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900"},g={key:0,class:"bg-yellow-100 text-yellow-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900"},b={key:1,class:"bg-red-100 text-red-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900"},f=a("svg",{class:"w-4 h-4 ml-2",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[a("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"})],-1);function y(x,k,e,v,h,w){const i=o;return t(),s("div",u,[e.subscriberMessage.delivery_status_update_is_successful==null?(t(),s("div",m," ... ")):e.subscriberMessage.delivery_status_update_is_successful?(t(),s("span",p,r(e.subscriberMessage.delivery_status),1)):(t(),s(l,{key:2},[e.subscriberMessage.delivery_status_update_failure_type=="Message Sending Failed"?(t(),s("span",g,r(e.subscriberMessage.delivery_status_update_failure_type),1)):(t(),s("span",b,r(e.subscriberMessage.delivery_status_update_failure_type??"Unsuccessful"),1)),c(i,{placement:"bottom",title:"Reason",width:"300",trigger:"hover",content:e.subscriberMessage.delivery_status_update_failure_reason},{reference:d(()=>[f]),_:1},8,["content"])],64))])}const D=n(_,[["render",y]]);export{D as default};
