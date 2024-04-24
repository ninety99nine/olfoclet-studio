import{o as i,c as N,w as e,a as t,r as y,v as x,d as b,e as c,b as o,f as g,g as n,u as a,F as w,h as $,t as h,n as C}from"./app-5698ab4f.js";import{_ as L}from"./ActionMessage-d8487664.js";import{_ as z}from"./ActionSection-36b62ba1.js";import{_ as T}from"./Checkbox-3e390da7.js";import{_ as U,a as S}from"./DialogModal-fbfad6a6.js";import{_ as W}from"./DangerButton-df8d437d.js";import{_ as E}from"./FormSection-ddef870b.js";import{_ as H,a as Y}from"./TextInput-67707cd0.js";import{_ as B}from"./InputLabel-46e89a14.js";import{_ as I}from"./PrimaryButton-949d2864.js";import{_ as A}from"./SecondaryButton-be1f3abd.js";import{S as q}from"./SectionBorder-7344d910.js";/* empty css            */import"./SectionTitle-f4e1702b.js";import"./_plugin-vue_export-helper-c27b6911.js";const G={class:"bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"},J={class:"sm:flex sm:items-start"},K=t("div",{class:"mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"},[t("svg",{class:"h-6 w-6 text-red-600",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"})])],-1),O={class:"mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left"},Q={class:"text-lg font-medium text-gray-900"},R={class:"mt-4 text-sm text-gray-600"},X={class:"flex flex-row justify-end px-6 py-4 bg-gray-100 text-right"},Z={__name:"ConfirmationModal",props:{show:{type:Boolean,default:!1},maxWidth:{type:String,default:"2xl"},closeable:{type:Boolean,default:!0}},emits:["close"],setup(m,{emit:P}){const r=P,d=()=>{r("close")};return(u,v)=>(i(),N(U,{show:m.show,"max-width":m.maxWidth,closeable:m.closeable,onClose:d},{default:e(()=>[t("div",G,[t("div",J,[K,t("div",O,[t("h3",Q,[y(u.$slots,"title")]),t("div",R,[y(u.$slots,"content")])])])]),t("div",X,[y(u.$slots,"footer")])]),_:3},8,["show","max-width","closeable"]))}},ee={class:"col-span-6 sm:col-span-4"},se={key:0,class:"col-span-6"},te={class:"mt-2 grid grid-cols-1 md:grid-cols-2 gap-4"},oe={class:"flex items-center"},le={class:"ml-2 text-sm text-gray-600"},ne={key:0},ae={class:"mt-10 sm:mt-0"},ie={class:"space-y-6"},re={class:"break-all"},ce={class:"flex items-center ml-2"},me={key:0,class:"text-sm text-gray-400"},de=["onClick"],ue=["onClick"],pe=t("div",null," Please copy your new API token. For your security, it won't be shown again. ",-1),fe={key:0,class:"mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500 break-all"},_e={class:"grid grid-cols-1 md:grid-cols-2 gap-4"},ve={class:"flex items-center"},ke={class:"ml-2 text-sm text-gray-600"},Ve={__name:"ApiTokenManager",props:{tokens:Array,availablePermissions:Array,defaultPermissions:Array},setup(m){const r=x({name:"",permissions:m.defaultPermissions}),d=x({permissions:[]}),u=x({}),v=b(!1),f=b(null),_=b(null),F=()=>{r.post(route("api-tokens.store"),{preserveScroll:!0,onSuccess:()=>{v.value=!0,r.reset()}})},V=p=>{d.permissions=p.abilities,f.value=p},j=()=>{d.put(route("api-tokens.update",f.value),{preserveScroll:!0,preserveState:!0,onSuccess:()=>f.value=null})},D=p=>{_.value=p},M=()=>{u.delete(route("api-tokens.destroy",_.value),{preserveScroll:!0,preserveState:!0,onSuccess:()=>_.value=null})};return(p,l)=>(i(),c("div",null,[o(E,{onSubmitted:F},{title:e(()=>[n(" Create API Token ")]),description:e(()=>[n(" API tokens allow third-party services to authenticate with our application on your behalf. ")]),form:e(()=>[t("div",ee,[o(B,{for:"name",value:"Name"}),o(H,{id:"name",modelValue:a(r).name,"onUpdate:modelValue":l[0]||(l[0]=s=>a(r).name=s),type:"text",class:"w-full mt-1 block",autofocus:""},null,8,["modelValue"]),o(Y,{message:a(r).errors.name,class:"mt-2"},null,8,["message"])]),m.availablePermissions.length>0?(i(),c("div",se,[o(B,{for:"permissions",value:"Permissions"}),t("div",te,[(i(!0),c(w,null,$(m.availablePermissions,s=>(i(),c("div",{key:s},[t("label",oe,[o(T,{checked:a(r).permissions,"onUpdate:checked":l[1]||(l[1]=k=>a(r).permissions=k),value:s},null,8,["checked","value"]),t("span",le,h(s),1)])]))),128))])])):g("",!0)]),actions:e(()=>[o(L,{on:a(r).recentlySuccessful,class:"mr-3"},{default:e(()=>[n(" Created. ")]),_:1},8,["on"]),o(I,{class:C({"opacity-25":a(r).processing}),disabled:a(r).processing},{default:e(()=>[n(" Create ")]),_:1},8,["class","disabled"])]),_:1}),m.tokens.length>0?(i(),c("div",ne,[o(q),t("div",ae,[o(z,null,{title:e(()=>[n(" Manage API Tokens ")]),description:e(()=>[n(" You may delete any of your existing tokens if they are no longer needed. ")]),content:e(()=>[t("div",ie,[(i(!0),c(w,null,$(m.tokens,s=>(i(),c("div",{key:s.id,class:"flex items-center justify-between"},[t("div",re,h(s.name),1),t("div",ce,[s.last_used_ago?(i(),c("div",me," Last used "+h(s.last_used_ago),1)):g("",!0),m.availablePermissions.length>0?(i(),c("button",{key:1,class:"cursor-pointer ml-6 text-sm text-gray-400 underline",onClick:k=>V(s)}," Permissions ",8,de)):g("",!0),t("button",{class:"cursor-pointer ml-6 text-sm text-red-500",onClick:k=>D(s)}," Delete ",8,ue)])]))),128))])]),_:1})])])):g("",!0),o(S,{show:v.value,onClose:l[3]||(l[3]=s=>v.value=!1)},{title:e(()=>[n(" API Token ")]),content:e(()=>[pe,p.$page.props.jetstream.flash.token?(i(),c("div",fe,h(p.$page.props.jetstream.flash.token),1)):g("",!0)]),footer:e(()=>[o(A,{onClick:l[2]||(l[2]=s=>v.value=!1)},{default:e(()=>[n(" Close ")]),_:1})]),_:1},8,["show"]),o(S,{show:f.value!=null,onClose:l[6]||(l[6]=s=>f.value=null)},{title:e(()=>[n(" API Token Permissions ")]),content:e(()=>[t("div",_e,[(i(!0),c(w,null,$(m.availablePermissions,s=>(i(),c("div",{key:s},[t("label",ve,[o(T,{checked:a(d).permissions,"onUpdate:checked":l[4]||(l[4]=k=>a(d).permissions=k),value:s},null,8,["checked","value"]),t("span",ke,h(s),1)])]))),128))])]),footer:e(()=>[o(A,{onClick:l[5]||(l[5]=s=>f.value=null)},{default:e(()=>[n(" Cancel ")]),_:1}),o(I,{class:C(["ml-3",{"opacity-25":a(d).processing}]),disabled:a(d).processing,onClick:j},{default:e(()=>[n(" Save ")]),_:1},8,["class","disabled"])]),_:1},8,["show"]),o(Z,{show:_.value!=null,onClose:l[8]||(l[8]=s=>_.value=null)},{title:e(()=>[n(" Delete API Token ")]),content:e(()=>[n(" Are you sure you would like to delete this API token? ")]),footer:e(()=>[o(A,{onClick:l[7]||(l[7]=s=>_.value=null)},{default:e(()=>[n(" Cancel ")]),_:1}),o(W,{class:C(["ml-3",{"opacity-25":a(u).processing}]),disabled:a(u).processing,onClick:M},{default:e(()=>[n(" Delete ")]),_:1},8,["class","disabled"])]),_:1},8,["show"])]))}};export{Ve as default};
