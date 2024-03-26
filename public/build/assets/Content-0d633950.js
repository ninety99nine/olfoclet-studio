import{P as g}from"./Pagination-12b2532c.js";import u from"./ManageTopicModal-7a885865.js";import{s as y,e as s,b as p,a as t,F as _,h as w,f as n,x as l,o as i,t as c,i as r}from"./app-bb45fb6f.js";import{_ as f}from"./_plugin-vue_export-helper-c27b6911.js";import"./base-130e034c.js";import"./el-breadcrumb-item-ff51b5c3.js";import"./icon-45c0f559.js";import"./index-58643621.js";import"./InputLabel-361014b1.js";import"./TextInput-431a2b44.js";import"./Textarea-9be55ed5.js";import"./PrimaryButton-40033d3b.js";import"./SelectInput-d87dd92a.js";import"./DialogModal-29eb6350.js";import"./DangerButton-883cba17.js";import"./SecondaryButton-5989360e.js";const x=y({components:{ManageTopicModal:u,Pagination:g},props:{parentTopic:Object,topicsPayload:Object,breadcrumbs:Array},data(){return{isShowingModal:!1,modalAction:null,topic:null}},methods:{showModal(e,a){this.topic=e,this.modalAction=a,this.isShowingModal=!0}}}),v={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg"},b={class:"flex flex-col"},k={class:"-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8"},M={class:"py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"},P={class:"shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"},C={class:"min-w-full divide-y divide-gray-200"},$=t("thead",{class:"bg-gray-50"},[t("tr",null,[t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Title")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Content")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-right text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Actions")])])],-1),V={class:"bg-white divide-y divide-gray-200"},j={class:"px-6 py-3"},T={class:"text-sm text-gray-900"},A={class:"px-6 py-3"},S={class:"text-sm text-gray-900"},N={class:"px-6 py-3 whitespace-nowrap text-right text-sm font-medium"},B=["onClick"],D=["onClick"],E=["onClick"],F={key:0},O=t("td",{colspan:3,class:"px-6 py-3 whitespace-nowrap"},[t("div",{class:"text-center text-gray-900 text-sm p-6"},"No topics")],-1),L=[O];function U(e,a,q,z,G,H){const m=l("manage-topic-modal"),h=l("pagination");return i(),s("div",null,[p(m,{modelValue:e.isShowingModal,"onUpdate:modelValue":a[0]||(a[0]=o=>e.isShowingModal=o),action:e.modalAction,topic:e.topic,parentTopic:e.parentTopic,breadcrumbs:e.breadcrumbs},null,8,["modelValue","action","topic","parentTopic","breadcrumbs"]),t("div",v,[t("div",b,[t("div",k,[t("div",M,[t("div",P,[t("table",C,[$,t("tbody",V,[(i(!0),s(_,null,w(e.topicsPayload.data,o=>(i(),s("tr",{key:o.id},[t("td",j,[t("div",T,c(o.title),1)]),t("td",A,[t("div",S,c(o.content),1)]),t("td",N,[e.$inertia.page.props.projectPermissions.includes("View topics")?(i(),s("a",{key:0,href:"#",onClick:r(d=>e.$inertia.get(e.route("show.topic",{project:e.route().params.project,topic:o.id})),["prevent"]),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"View",8,B)):n("",!0),e.$inertia.page.props.projectPermissions.includes("Manage topics")?(i(),s("a",{key:1,href:"#",onClick:r(d=>e.showModal(o,"update"),["prevent"]),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"Edit",8,D)):n("",!0),e.$inertia.page.props.projectPermissions.includes("Manage topics")?(i(),s("a",{key:2,href:"#",onClick:r(d=>e.showModal(o,"delete"),["prevent"]),class:"text-red-600 hover:text-red-900"},"Delete",8,E)):n("",!0)])]))),128)),e.topicsPayload.data.length==0?(i(),s("tr",F,L)):n("",!0)])])])])])]),p(h,{class:"mt-6",paginationPayload:e.topicsPayload,updateData:["topicsPayload"]},null,8,["paginationPayload"])])])}const rt=f(x,[["render",U]]);export{rt as default};
