import"./base-9406257f.js";import"./el-popper-7558033e.js";import{E as r}from"./el-popover-f8ab794a.js";import{_ as a}from"./_plugin-vue_export-helper-c27b6911.js";import{o as t,e as n,F as l,t as o,b as c,w as d,a as i}from"./app-c2ab2b5a.js";import"./icon-e373acc6.js";import"./constants-9a71fd89.js";/* empty css            */const m={props:{billingTransaction:Object},data(){return{}}},u={class:"flex items-center"},_={key:0,class:"bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900"},f={key:0,class:"bg-yellow-100 text-yellow-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900"},p={key:1,class:"bg-red-100 text-red-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900"},g=i("svg",{class:"w-4 h-4 ml-2",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[i("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"})],-1);function x(b,k,e,y,h,w){const s=r;return t(),n("div",u,[e.billingTransaction.is_successful?(t(),n("span",_," Successful ")):(t(),n(l,{key:1},[e.billingTransaction.failure_type=="Inactive Account"||e.billingTransaction.failure_type=="Insufficient Funds"?(t(),n("span",f,o(e.billingTransaction.failure_type),1)):(t(),n("span",p,o(e.billingTransaction.failure_type??"Unsuccessful"),1)),c(s,{placement:"bottom",title:"Reason",width:"300",trigger:"hover",content:e.billingTransaction.failure_reason},{reference:d(()=>[g]),_:1},8,["content"])],64))])}const C=a(m,[["render",x]]);export{C as default};