import y from"./BillingTransactionStatusBadge-6f9d2d58.js";import h from"./CreatedUsingAutoBillingBadge-fec176cd.js";import w from"./ActiveStatusBadge-0e6b9463.js";import b from"./RatingTypeBadge-3f86b9c8.js";import f from"./AutoBillingReminderStatusBadge-7a8ff362.js";import v from"./AutoBillingEnabledStatusBadge-d9a49b09.js";import{P as k}from"./Pagination-e30dabce.js";import{C as A}from"./Countdown-474eafbc.js";import{h as T}from"./moment-fbc5633a.js";import{_ as B}from"./_plugin-vue_export-helper-c27b6911.js";import{q as L,s as p,o as n,e as o,a as t,F as C,h as P,f as I,b as r,t as a,c as i}from"./app-33020c92.js";import"./base-56227e5d.js";import"./el-popper-e325eb28.js";import"./icon-02db1ed7.js";import"./constants-ee6bc465.js";import"./el-popover-177d5098.js";/* empty css            */const N=L({components:{Countdown:A,SubscriptionPlanActiveStatusBadge:w,AutoBillingReminderStatusBadge:f,AutoBillingEnabledStatusBadge:v,Pagination:k,BillingTransactionStatusBadge:y,RatingTypeBadge:b,CreatedUsingAutoBillingBadge:h},props:{autoBillingSchedulesPayload:Object},data(){return{refreshContentInterval:null,moment:T}},methods:{getLatestAutoBillingTransaction(s){return s.subscriber.latest_auto_billing_transaction??{}},refreshContent(){this.$inertia.reload()},cleanUp(){clearInterval(this.refreshContentInterval),this.refreshContentInterval=null}},created(){this.refreshContentInterval=setInterval(function(){this.refreshContent()}.bind(this),5e3)},unmounted(){this.cleanUp()}}),D=t("div",{class:"grid grid-cols-12 gap-4"},[t("div",{class:"col-span-6"},[t("div",{class:"bg-gray-50 border-b px-6 py-4 rounded-t text-gray-500 text-sm mb-4"},[t("div",{class:"text-2xl font-semibold leading-6 text-gray-500"},"Auto Billing Schedules")])])],-1),R={class:"bg-white shadow-xl sm:rounded-lg"},U={class:"flex flex-col overflow-y-auto"},E={class:"align-middle inline-block min-w-full"},H={class:"shadow border-b border-gray-200"},O={class:"min-w-full divide-y divide-gray-200"},F=t("thead",{class:"bg-gray-50"},[t("tr",null,[t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300"}),t("th",{scope:"col",colspan:"7",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300"},[t("div",{class:"font-bold text-teal-500"},"AUTO BILLING SCHEDULE")]),t("th",{scope:"col",colspan:"8",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50 border-r border-dotted border-r-violet-300"},[t("div",{class:"font-bold text-fuchsia-500"},"LAST AUTO BILLING TRANSACTION")]),t("th",{scope:"col",colspan:"6",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50 border-r border-dotted border-r-teal-300"},[t("div",{class:"font-bold text-violet-500"},"AUTO BILLING REMINDER")]),t("th",{scope:"col",colspan:"6",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("div",{class:"font-bold text-teal-500"},"SUBSCRIPTION PLAN")])]),t("tr",null,[t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300"},[t("span",null,"Mobile")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Auto Billing Enabled")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Next Attempt Date")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Countdown")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Total Attempts Before Disabling")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Total Successful Attempts")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Total Failed Attempts")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300"},[t("span",null,"Created Date")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50"},[t("span",null,"Status")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50"},[t("span",null,"Amount")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50"},[t("span",null,"Funds Before Deduction")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50"},[t("span",null,"Funds After Deduction")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50"},[t("span",null,"Rating Type")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50"},[t("span",null,"Auto Billing")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50"},[t("span",null,"Description")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50 border-r border-dotted border-r-violet-300"},[t("span",null,"Created")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50"},[t("span",null,"72 Hours Before")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50"},[t("span",null,"48 Hours Before")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50"},[t("span",null,"24 Hours Before")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50"},[t("span",null,"12 Hours Before")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50"},[t("span",null,"6 Hours Before")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50 border-r border-dotted border-r-teal-300"},[t("span",null,"1 Hour Before")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Name")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Description")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Active")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Duration")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Price")]),t("th",{scope:"col",class:"px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50"},[t("span",null,"Created")])])],-1),$={class:"bg-white divide-y divide-gray-200"},G={class:"px-6 py-3 whitespace-nowrap border-r border-dotted border-r-teal-300"},V={class:"text-sm text-gray-900"},M={class:"px-6 py-3 text-sm text-gray-500 text-center bg-teal-50"},j={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50"},q={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50"},z={key:0},J={class:"px-6 py-3 text-sm text-gray-500 text-center bg-teal-50"},K={class:"px-6 py-3 text-sm text-gray-500 text-center bg-teal-50"},Q={class:"px-6 py-3 text-sm text-gray-500 text-center bg-teal-50"},W={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50 border-r border-dotted border-r-fuchsia-300"},X={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-fuchsia-50"},Y={key:1},Z={class:"px-6 py-3 whitespace-nowrap text-md text-gray-500 text-center font-bold bg-fuchsia-50"},S={key:0},tt={key:1},et={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50"},st={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50"},at={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50"},nt={key:1},ot={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50"},rt={key:1},pt={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50"},ct={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 bg-fuchsia-50 border-r border-dotted border-r-violet-300"},it={class:"px-6 py-3 text-xs text-gray-500 bg-violet-50"},lt={class:"px-6 py-3 text-xs text-gray-500 bg-violet-50"},dt={class:"px-6 py-3 text-xs text-gray-500 bg-violet-50"},xt={class:"px-6 py-3 text-xs text-gray-500 bg-violet-50"},gt={class:"px-6 py-3 text-xs text-gray-500 bg-violet-50"},ut={class:"px-6 py-3 text-xs text-gray-500 bg-violet-50 border-r border-dotted border-r-teal-300"},mt={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50"},_t={class:"text-sm text-gray-900"},yt={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50"},ht={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50"},wt={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50"},bt={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50"},ft={class:"px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50"},vt={key:0},kt=t("td",{colspan:10,class:"px-6 py-3 whitespace-nowrap"},[t("div",{class:"text-center text-gray-900 text-sm p-6"},"No auto billing schedules")],-1),At=t("td",{colspan:10,class:"px-6 py-3 whitespace-nowrap"},[t("div",{class:"text-center text-gray-900 text-sm p-6"},"No auto billing schedules")],-1),Tt=t("td",{colspan:7,class:"px-6 py-3 whitespace-nowrap"},[t("div",{class:"text-center text-gray-900 text-sm p-6"},"No auto billing schedules")],-1),Bt=[kt,At,Tt];function Lt(s,Ct,Pt,It,Nt,Dt){const l=p("AutoBillingEnabledStatusBadge"),d=p("Countdown"),x=p("BillingTransactionStatusBadge"),g=p("RatingTypeBadge"),u=p("CreatedUsingAutoBillingBadge"),c=p("AutoBillingReminderStatusBadge"),m=p("SubscriptionPlanActiveStatusBadge"),_=p("pagination");return n(),o("div",null,[D,t("div",R,[t("div",U,[t("div",E,[t("div",H,[t("table",O,[F,t("tbody",$,[(n(!0),o(C,null,P(s.autoBillingSchedulesPayload.data,e=>(n(),o("tr",{key:e.id},[t("td",G,[t("div",V,a(e.subscriber.msisdn),1)]),t("td",M,[r(l,{autoBillingSchedule:e},null,8,["autoBillingSchedule"])]),t("td",j,a(e.next_attempt_date==null?"...":s.moment(e.next_attempt_date).format("lll")),1),t("td",q,[e.next_attempt_date_milli_seconds_left==null?(n(),o("span",z,"...")):(n(),i(d,{key:1,time:e.next_attempt_date_milli_seconds_left},null,8,["time"]))]),t("td",J,a(e.attempts==null?"...":e.attempts)+" / "+a(e.subscription_plan==null?"...":e.subscription_plan.max_auto_billing_attempts),1),t("td",K,a(e.total_successful_attempts==null?"...":e.total_successful_attempts),1),t("td",Q,a(e.total_failed_attempts==null?"...":e.total_failed_attempts),1),t("td",W,a(e.created_at==null?"...":s.moment(e.created_at).format("lll")),1),t("td",X,[s.getLatestAutoBillingTransaction(e).is_successful?(n(),i(x,{key:0,billingTransaction:s.getLatestAutoBillingTransaction(e)},null,8,["billingTransaction"])):(n(),o("span",Y,"..."))]),t("td",Z,[s.getLatestAutoBillingTransaction(e).amount?(n(),o("span",S,a(s.getLatestAutoBillingTransaction(e).amount.amount_with_currency),1)):(n(),o("span",tt,"..."))]),t("td",et,a(s.getLatestAutoBillingTransaction(e).funds_before_deduction==null?"...":s.getLatestAutoBillingTransaction(e).funds_before_deduction.amount_with_currency),1),t("td",st,a(s.getLatestAutoBillingTransaction(e).funds_after_deduction==null?"...":s.getLatestAutoBillingTransaction(e).funds_after_deduction.amount_with_currency),1),t("td",at,[s.getLatestAutoBillingTransaction(e).rating_type?(n(),i(g,{key:0,billingTransaction:s.getLatestAutoBillingTransaction(e)},null,8,["billingTransaction"])):(n(),o("span",nt,"..."))]),t("td",ot,[s.getLatestAutoBillingTransaction(e).created_using_auto_billing?(n(),i(u,{key:0,billingTransaction:s.getLatestAutoBillingTransaction(e)},null,8,["billingTransaction"])):(n(),o("span",rt,"..."))]),t("td",pt,[t("span",null,a(s.getLatestAutoBillingTransaction(e).description??"..."),1)]),t("td",ct,a(s.getLatestAutoBillingTransaction(e).created_at==null?"...":s.moment(s.getLatestAutoBillingTransaction(e).created_at).format("lll")),1),t("td",it,[r(c,{hours:72,autoBillingSchedule:e},null,8,["autoBillingSchedule"])]),t("td",lt,[r(c,{hours:48,autoBillingSchedule:e},null,8,["autoBillingSchedule"])]),t("td",dt,[r(c,{hours:24,autoBillingSchedule:e},null,8,["autoBillingSchedule"])]),t("td",xt,[r(c,{hours:12,autoBillingSchedule:e},null,8,["autoBillingSchedule"])]),t("td",gt,[r(c,{hours:6,autoBillingSchedule:e},null,8,["autoBillingSchedule"])]),t("td",ut,[r(c,{hours:1,autoBillingSchedule:e},null,8,["autoBillingSchedule"])]),t("td",mt,[t("div",_t,a(e.subscription_plan.name),1)]),t("td",yt,a(e.subscription_plan.description==null?"...":e.subscription_plan.description),1),t("td",ht,[r(m,{subscriptionPlan:e.subscription_plan},null,8,["subscriptionPlan"])]),t("td",wt,a(e.subscription_plan.duration_in_words==null?"...":e.subscription_plan.duration_in_words),1),t("td",bt,a(e.subscription_plan.price==null?"...":e.subscription_plan.price.amount_with_currency),1),t("td",ft,a(e.subscription_plan.created_at==null?"...":s.moment(e.subscription_plan.created_at).format("lll")),1)]))),128)),s.autoBillingSchedulesPayload.data.length==0?(n(),o("tr",vt,Bt)):I("",!0)])])])])]),r(_,{class:"mt-6",paginationPayload:s.autoBillingSchedulesPayload,updateData:["autoBillingSchedulesPayload"]},null,8,["paginationPayload"])])])}const Xt=B(N,[["render",Lt]]);export{Xt as default};