import{h as _}from"./moment-fbc5633a.js";import{q as f,s as c,o,e as a,b as l,a as t,F as u,h as m,f as i,t as n,i as d}from"./app-33020c92.js";import b from"./ActiveStatusBadge-0e6b9463.js";import v from"./FolderStatusBadge-d07274b6.js";import{P as k}from"./Pagination-e30dabce.js";import S from"./ManageSubscriptionPlanModal-70c49410.js";import{_ as P}from"./_plugin-vue_export-helper-c27b6911.js";/* empty css            */import"./base-56227e5d.js";import"./el-tag-8b56b1c1.js";import"./constants-ee6bc465.js";import"./icon-02db1ed7.js";import"./index-cea313c0.js";import"./el-select-97f03291.js";import"./el-popper-e325eb28.js";import"./el-scrollbar-8864ffa5.js";import"./el-switch-e3bffaf9.js";import"./el-popover-177d5098.js";import"./el-breadcrumb-item-59a8d6d5.js";import"./TextInput-1f4ada43.js";import"./InputLabel-cb7cfcd9.js";import"./Textarea-fb103628.js";import"./PrimaryButton-6b9f4282.js";import"./SelectInput-a557b92c.js";import"./DialogModal-61701a9e.js";import"./DangerButton-d84d9b81.js";import"./SecondaryButton-cebaf51c.js";const B=f({components:{ManageSubscriptionPlanModal:S,Pagination:k,ActiveStatusBadge:b,FolderStatusBadge:v},props:{breadcrumbs:Array,totalSubscriptions:Number,autoBillingReminders:Array,parentSubscriptionPlan:Object,subscriptionPlansPayload:Object},data(){return{moment:_,modalAction:null,isShowingModal:!1,subscriptionPlan:null}},methods:{onDeleted(){this.subscriptionPlan=null},getPercentageOfCoverage(e){return this.totalSubscriptions>0?Math.round(e/this.totalSubscriptions*100):0},showModal(e,r){this.subscriptionPlan=e,this.modalAction=r,this.isShowingModal=!0}}}),C={class:"bg-white shadow-xl sm:rounded-lg"},M={class:"flex flex-col overflow-y-auto"},A={class:"align-middle inline-block min-w-full"},$={class:"shadow border-b border-gray-200"},D={class:"min-w-full divide-y divide-gray-200"},V=t("thead",{class:"bg-gray-50"},[t("tr",null,[t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-left"},[t("span",null,"Name")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-left"},[t("span",null,"Description")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center"},[t("span",null,"Type")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center"},[t("span",null,"Active")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center"},[t("span",null,"Tags")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center"},[t("span",null,"Duration")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center"},[t("span",null,"Price")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center"},[t("span",null,"Subscriptions")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center"},[t("span",null,"Popularity")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Created")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-right"},[t("span",null,"Actions")])])],-1),j={class:"bg-white divide-y divide-gray-200"},N={class:"px-6 py-3 whitespace-nowrap text-left"},O={class:"text-sm text-gray-900"},F={class:"px-6 py-3 whitespace-nowrap text-left"},R={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center"},E={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center"},T={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center"},q={key:0},L={class:"flex space-x-2"},U={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center"},z={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center"},G={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center"},H={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center"},I={class:"text-lg text-green-600"},J={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500"},K={class:"px-6 py-3 whitespace-nowrap text-right text-sm font-medium"},Q=["onClick"],W=["onClick"],X=["onClick"],Y={key:0},Z=t("td",{colspan:10,class:"px-6 py-3 whitespace-nowrap"},[t("div",{class:"text-center text-gray-900 text-sm p-6"},"No subscription plans")],-1),tt=[Z];function et(e,r,st,ot,at,nt){const h=c("manage-subscription-plan-modal"),x=c("FolderStatusBadge"),g=c("ActiveStatusBadge"),w=c("pagination");return o(),a("div",null,[l(h,{modelValue:e.isShowingModal,"onUpdate:modelValue":r[0]||(r[0]=s=>e.isShowingModal=s),action:e.modalAction,subscriptionPlan:e.subscriptionPlan,parentSubscriptionPlan:e.parentSubscriptionPlan,autoBillingReminders:e.autoBillingReminders,breadcrumbs:e.breadcrumbs,onOnDeleted:e.onDeleted},null,8,["modelValue","action","subscriptionPlan","parentSubscriptionPlan","autoBillingReminders","breadcrumbs","onOnDeleted"]),t("div",C,[t("div",M,[t("div",A,[t("div",$,[t("table",D,[V,t("tbody",j,[(o(!0),a(u,null,m(e.subscriptionPlansPayload.data,s=>(o(),a("tr",{key:s.id},[t("td",N,[t("div",O,n(s.name),1)]),t("td",F,n(s.description==null?"...":s.description),1),t("td",R,[l(x,{subscriptionPlan:s},null,8,["subscriptionPlan"])]),t("td",E,[l(g,{subscriptionPlan:s},null,8,["subscriptionPlan"])]),t("td",T,[s.tags.length==0?(o(),a("div",q,"...")):i("",!0),t("div",L,[(o(!0),a(u,null,m(s.tags,(p,y)=>(o(),a("div",{key:y,class:"bg-blue-50 text-blue-500 border border-blue-300 py-1 px-2 text-xs rounded"},n(p),1))),128))])]),t("td",U,n(s.duration_in_words==null?"...":s.duration_in_words),1),t("td",z,n(s.price==null?"...":s.price.amount_with_currency),1),t("td",G,n(s.subscriptions_count==null?"...":s.subscriptions_count),1),t("td",H,[t("span",I,n(e.getPercentageOfCoverage(s.subscriptions_count))+"%",1)]),t("td",J,n(s.created_at==null?"...":e.moment(s.created_at).fromNow()),1),t("td",K,[e.$inertia.page.props.projectPermissions.includes("View subscription plans")&&s.is_folder?(o(),a("a",{key:0,href:"#",onClick:d(p=>e.$inertia.get(e.route("show.subscription.plan",{project:e.route().params.project,subscription_plan:s.id})),["prevent"]),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"View",8,Q)):i("",!0),e.$inertia.page.props.projectPermissions.includes("Manage subscription plans")?(o(),a("a",{key:1,href:"#",onClick:d(p=>e.showModal(s,"update"),["prevent"]),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"Edit",8,W)):i("",!0),e.$inertia.page.props.projectPermissions.includes("Manage subscription plans")?(o(),a("a",{key:2,href:"#",onClick:d(p=>e.showModal(s,"delete"),["prevent"]),class:"text-red-600 hover:text-red-900"},"Delete",8,X)):i("",!0)])]))),128)),e.subscriptionPlansPayload.data.length==0?(o(),a("tr",Y,tt)):i("",!0)])])])])]),l(w,{class:"mt-6",paginationPayload:e.subscriptionPlansPayload,updateData:["subscriptionPlansPayload"]},null,8,["paginationPayload"])])])}const jt=P(B,[["render",et]]);export{jt as default};