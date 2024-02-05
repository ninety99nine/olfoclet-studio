import{P as g}from"./Pagination-54faa811.js";import h from"./ManageMessageModal-62dda441.js";import{s as u,e as o,b as l,a as s,F as y,h as _,f as n,x as p,o as a,t as f,i as r}from"./app-8047a45c.js";import{_ as w}from"./_plugin-vue_export-helper-c27b6911.js";import"./base-3ecad56a.js";import"./el-breadcrumb-item-b4cdcb66.js";import"./icon-be9b5ac7.js";import"./index-d2e11550.js";import"./TextInput-bc6cbde4.js";import"./InputLabel-22fd5c5c.js";import"./Textarea-93bc0975.js";import"./PrimaryButton-8efc2419.js";import"./SelectInput-b01ea32e.js";import"./DialogModal-0a229599.js";import"./DangerButton-569cea3a.js";import"./SecondaryButton-bd238173.js";const v=u({components:{ManageMessageModal:h,Pagination:g},props:{parentMessage:Object,messagesPayload:Object,breadcrumbs:Array},data(){return{isShowingModal:!1,modalAction:null,message:null}},methods:{showModal(e,i){this.message=e,this.modalAction=i,this.isShowingModal=!0}}}),x={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg"},b={class:"flex flex-col"},M={class:"-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8"},k={class:"py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"},P={class:"shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"},C={class:"min-w-full divide-y divide-gray-200"},$=s("thead",{class:"bg-gray-50"},[s("tr",null,[s("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[s("span",null,"Content")]),s("th",{scope:"col",class:"px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"},[s("span",null,"Actions")])])],-1),V={class:"bg-white divide-y divide-gray-200"},j={class:"px-6 py-4"},A={class:"text-sm text-gray-900"},S={class:"px-6 py-4 whitespace-nowrap text-right text-sm font-medium"},N=["onClick"],B=["onClick"],D=["onClick"],E={key:0},F=s("td",{colspan:7,class:"px-6 py-4 whitespace-nowrap"},[s("div",{class:"text-center text-gray-900 text-sm p-6"},"No messages")],-1),O=[F];function L(e,i,U,q,z,G){const m=p("manage-message-modal"),c=p("pagination");return a(),o("div",null,[l(m,{modelValue:e.isShowingModal,"onUpdate:modelValue":i[0]||(i[0]=t=>e.isShowingModal=t),action:e.modalAction,message:e.message,parentMessage:e.parentMessage,breadcrumbs:e.breadcrumbs},null,8,["modelValue","action","message","parentMessage","breadcrumbs"]),s("div",x,[s("div",b,[s("div",M,[s("div",k,[s("div",P,[s("table",C,[$,s("tbody",V,[(a(!0),o(y,null,_(e.messagesPayload.data,t=>(a(),o("tr",{key:t.id},[s("td",j,[s("div",A,f(t.content),1)]),s("td",S,[e.$inertia.page.props.projectPermissions.includes("View messages")?(a(),o("a",{key:0,href:"#",onClick:r(d=>e.$inertia.get(e.route("show.message",{project:e.route().params.project,message:t.id})),["prevent"]),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"View",8,N)):n("",!0),e.$inertia.page.props.projectPermissions.includes("Manage messages")?(a(),o("a",{key:1,href:"#",onClick:r(d=>e.showModal(t,"update"),["prevent"]),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"Edit",8,B)):n("",!0),e.$inertia.page.props.projectPermissions.includes("Manage messages")?(a(),o("a",{key:2,href:"#",onClick:r(d=>e.showModal(t,"delete"),["prevent"]),class:"text-red-600 hover:text-red-900"},"Delete",8,D)):n("",!0)])]))),128)),e.messagesPayload.data.length==0?(a(),o("tr",E,O)):n("",!0)])])])])])]),l(c,{class:"mt-6",paginationPayload:e.messagesPayload,updateData:["messagesPayload"]},null,8,["paginationPayload"])])])}const ie=w(v,[["render",L]]);export{ie as default};