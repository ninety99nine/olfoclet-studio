import u from"./ManageSubscriptionModal-4b20bf72.js";import{P as x}from"./Pagination-c426c16e.js";import{s as g,e as o,b as l,a as t,F as w,h as y,f as i,x as p,o as n,t as a,n as f,i as c}from"./app-a9929219.js";import{h as _}from"./moment-fbc5633a.js";import{_ as b}from"./_plugin-vue_export-helper-c27b6911.js";import"./TextInput-854b9238.js";import"./InputLabel-831d05d4.js";import"./Textarea-ba09b2c9.js";import"./PrimaryButton-151237c0.js";import"./SelectInput-0f0594b1.js";import"./DialogModal-654df02b.js";import"./DangerButton-19641c8b.js";import"./ActionMessage-55ec1a2c.js";import"./SecondaryButton-c49f7243.js";/* empty css            */const v=g({components:{CreateSubscriptionModal:u,Pagination:x},props:{subscriptionPlans:Array,totalSubscribers:Number,subscriptionsPayload:Object},data(){return{refreshContentInterval:null,isShowingModal:!1,modalAction:null,subscription:null,moment:_}},methods:{refreshContent(){this.$inertia.reload()},showModal(e,r){this.subscription=e,this.modalAction=r,this.isShowingModal=!0},getPercentageOfCoverage(e){return this.totalSubscribers>0?Math.round(e/this.totalSubscribers*100):0},cleanUp(){clearInterval(this.refreshContentInterval),this.refreshContentInterval=null}},created(){this.refreshContentInterval=setInterval(function(){this.refreshContent()}.bind(this),5e3)},unmounted(){this.cleanUp()}}),k={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg"},C={class:"flex flex-col"},P={class:"-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8"},M={class:"py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"},S={class:"shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"},$={class:"min-w-full divide-y divide-gray-200"},A=t("thead",{class:"bg-gray-50"},[t("tr",null,[t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Mobile")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Start")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"End")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Cancelled")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Plan")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Status")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Created")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-right"},[t("span",null,"Actions")])])],-1),I={class:"bg-white divide-y divide-gray-200"},N={class:"px-6 py-3 whitespace-nowrap"},V={class:"text-sm text-gray-900"},j={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500"},B={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500"},D={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500"},E={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500"},U={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500"},F={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500"},O={class:"px-6 py-3 whitespace-nowrap text-right text-sm font-medium"},z=["onClick"],L=["onClick"],q={key:0},G=t("td",{colspan:8,class:"px-6 py-3 whitespace-nowrap"},[t("div",{class:"text-center text-gray-900 text-sm p-6"},"No subscriptions")],-1),H=[G];function J(e,r,K,Q,R,T){const d=p("create-subscription-modal"),m=p("pagination");return n(),o("div",null,[l(d,{modelValue:e.isShowingModal,"onUpdate:modelValue":r[0]||(r[0]=s=>e.isShowingModal=s),action:e.modalAction,subscription:e.subscription,subscriptionPlans:e.subscriptionPlans},null,8,["modelValue","action","subscription","subscriptionPlans"]),t("div",k,[t("div",C,[t("div",P,[t("div",M,[t("div",S,[t("table",$,[A,t("tbody",I,[(n(!0),o(w,null,y(e.subscriptionsPayload.data,s=>(n(),o("tr",{key:s.id},[t("td",N,[t("div",V,a(s.subscriber?s.subscriber.msisdn:"..."),1)]),t("td",j,a(s.created_at==null?"...":e.moment(s.start_at).format("lll")),1),t("td",B,a(s.created_at==null?"...":e.moment(s.end_at).format("lll")),1),t("td",D,a(s.cancelled_at==null?"...":e.moment(s.cancelled_at).format("lll")),1),t("td",E,a(s.subscription_plan==null?"...":s.subscription_plan.name),1),t("td",U,[t("span",{class:f(s.status=="Active"?"text-green-600":"text-gray-600")},a(s.status),3)]),t("td",F,a(s.created_at==null?"...":e.moment(s.created_at).fromNow()),1),t("td",O,[e.$inertia.page.props.projectPermissions.includes("Manage subscriptions")?(n(),o("a",{key:0,href:"#",onClick:c(h=>e.showModal(s,"update"),["prevent"]),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"Edit",8,z)):i("",!0),e.$inertia.page.props.projectPermissions.includes("Manage subscriptions")?(n(),o("a",{key:1,href:"#",onClick:c(h=>e.showModal(s,"delete"),["prevent"]),class:"text-red-600 hover:text-red-900"},"Delete",8,L)):i("",!0)])]))),128)),e.subscriptionsPayload.data.length==0?(n(),o("tr",q,H)):i("",!0)])])])])])]),l(m,{class:"mt-6",paginationPayload:e.subscriptionsPayload,updateData:["subscriptionsPayload"]},null,8,["paginationPayload"])])])}const dt=b(v,[["render",J]]);export{dt as default};