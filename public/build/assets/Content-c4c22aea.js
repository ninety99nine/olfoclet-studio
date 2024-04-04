import"./base-c965bbf6.js";import{E as h}from"./el-popover-bcf67893.js";import m from"./SubscriptionPlanCanAutoBillBadge-3a9ea281.js";import _ from"./AutoBillingReminderStatusBadge-d14ecb6f.js";import{P as x}from"./Pagination-2e08b3d0.js";import{s as b,e as o,a as t,g as i,b as l,w as c,F as y,h as w,f,x as r,o as n,t as a,i as B}from"./app-5bb89648.js";import{h as v}from"./moment-fbc5633a.js";import{_ as S}from"./_plugin-vue_export-helper-c27b6911.js";import"./icon-0fd4f662.js";import"./constants-10dd533f.js";/* empty css            */const k=b({components:{Pagination:x,SubscriptionPlanCanAutoBillBadge:m,AutoBillingReminderStatusBadge:_},props:{projectPayload:Object,autoBillingReminderSubscriptionPlansPayload:Object},data(){return{refreshContentInterval:null,subscriptionPlan:null,moment:v}},methods:{refreshContent(){this.$inertia.reload()},getLatestSubscriptionPlanBatchJob(e){return e.latest_auto_billing_reminder_job_batch.length?e.latest_auto_billing_reminder_job_batch[0]:{}},cleanUp(){clearInterval(this.refreshContentInterval),this.refreshContentInterval=null}},created(){this.refreshContentInterval=setInterval(function(){this.refreshContent()}.bind(this),5e3)},unmounted(){this.cleanUp()}}),P={class:"bg-gray-50 border-b px-6 py-4 rounded-t text-gray-500 text-sm mb-4"},A=t("div",{class:"text-2xl font-semibold leading-6 text-gray-500 border-b pb-4 mb-4"},"Auto Billing Reminders",-1),j={class:"flex items-center"},C={key:0,class:"text-gray-400"},L=t("span",{class:"text-green-500 font-bold"},"Auto Billing is enabled",-1),J={key:1,class:"text-gray-400"},R=t("span",{class:"text-red-500 font-bold"},"Auto Billing is disabled",-1),$=t("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[t("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),I={key:0,class:"break-normal"},N=t("hr",{class:"my-4"},null,-1),V={key:1,class:"break-normal"},M={class:"bg-white shadow-xl sm:rounded-lg"},T={class:"flex flex-col overflow-y-auto"},D={class:"align-middle inline-block min-w-full"},E={class:"shadow border-b border-gray-200"},z={class:"min-w-full divide-y divide-gray-200"},F=t("thead",{class:"bg-gray-50"},[t("tr",null,[t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Name")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Auto Bill")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Status")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Sprints")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100"},[t("span",null,"Total")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100"},[t("span",null,"Pending")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100"},[t("span",null,"Processed")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100"},[t("span",null,"Progress")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Last Sprint Date")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-right text-xs font-medium text-gray-500 uppercase tracking-wider"},[t("span",null,"Actions")])])],-1),O={class:"bg-white divide-y divide-gray-200"},U={class:"px-6 py-3 whitespace-nowrap"},Y={class:"text-sm text-gray-900"},H={class:"px-6 py-3"},W={class:"px-6 py-3"},q={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center"},G={class:"text-sm text-gray-900"},K={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50"},Q={class:"text-sm text-gray-900"},X={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50"},Z={class:"text-sm text-gray-900"},tt={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50"},et={class:"text-sm text-gray-900"},st={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50"},ot={class:"text-lg text-green-600"},nt={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500"},at={class:"px-6 py-3 whitespace-nowrap text-right text-sm font-medium"},it=["onClick"],lt={key:0},rt=t("td",{colspan:10,class:"px-6 py-3 whitespace-nowrap"},[t("div",{class:"text-center text-gray-900 text-sm p-6"},"No subscription plans")],-1),ct=[rt];function pt(e,dt,gt,ut,ht,mt){const p=h,d=r("SubscriptionPlanCanAutoBillBadge"),g=r("AutoBillingReminderStatusBadge"),u=r("pagination");return n(),o("div",null,[t("div",P,[A,t("div",j,[e.projectPayload.can_auto_bill?(n(),o("span",C,[L,i(' — You can turn off "Auto Billing" in settings (This affects all subscription plans)')])):(n(),o("span",J,[R,i(' — You can turn on "Auto Billing" in settings')])),l(p,{width:400},{reference:c(()=>[$]),default:c(()=>[e.projectPayload.can_auto_bill?(n(),o("span",I,[i(` Turning off "Auto Billing" from the project settings means that every subscription plan won't be able to auto bill even if the subscription plan "Auto Billing" option is turned on. Additionally, subscribers will not be sent auto billing reminders via SMS. `),N,i(' After turning off "Auto Billing" from the project settings, any running subscription plans will complete their last sprint before completely stopping to auto bill. ')])):(n(),o("span",V,' Turning on "Auto Billing" from the project settings means that every subscription plan will be able to auto bill as long as the subscription plan "Auto Billing" option is turned on. Additionally, subscribers will be sent auto billing reminders via SMS. '))]),_:1})])]),t("div",M,[t("div",T,[t("div",D,[t("div",E,[t("table",z,[F,t("tbody",O,[(n(!0),o(y,null,w(e.autoBillingReminderSubscriptionPlansPayload.data,s=>(n(),o("tr",{key:s.id},[t("td",U,[t("div",Y,a(s.name),1)]),t("td",H,[l(d,{subscriptionPlan:s},null,8,["subscriptionPlan"])]),t("td",W,[l(g,{autoBillingReminderJobBatch:e.getLatestSubscriptionPlanBatchJob(s)},null,8,["autoBillingReminderJobBatch"])]),t("td",q,[t("div",G,a(s.auto_billing_reminder_job_batches_count),1)]),t("td",K,[t("div",Q,a(e.getLatestSubscriptionPlanBatchJob(s).total_jobs),1)]),t("td",X,[t("div",Z,a(e.getLatestSubscriptionPlanBatchJob(s).pending_jobs),1)]),t("td",tt,[t("div",et,a(e.getLatestSubscriptionPlanBatchJob(s).processed_jobs),1)]),t("td",st,[t("span",ot,a(e.getLatestSubscriptionPlanBatchJob(s).progress)+" "+a(e.getLatestSubscriptionPlanBatchJob(s).progress?"%":""),1)]),t("td",nt,a(e.getLatestSubscriptionPlanBatchJob(s).created_at==null?"...":e.moment(e.getLatestSubscriptionPlanBatchJob(s).created_at).format("lll")),1),t("td",at,[t("a",{href:"#",onClick:B(_t=>e.$inertia.get(e.route("show.auto.billing.subscription.plan.reminder.job.batches",{project:e.route().params.project,subscription_plan:s.id})),["prevent"]),class:"text-indigo-600 hover:text-indigo-900 mr-3"},"View",8,it)])]))),128)),e.autoBillingReminderSubscriptionPlansPayload.data.length==0?(n(),o("tr",lt,ct)):f("",!0)])])])])]),l(u,{class:"mt-6",paginationPayload:e.autoBillingReminderSubscriptionPlansPayload,updateData:["autoBillingReminderSubscriptionPlansPayload"]},null,8,["paginationPayload"])])])}const jt=S(k,[["render",pt]]);export{jt as default};
