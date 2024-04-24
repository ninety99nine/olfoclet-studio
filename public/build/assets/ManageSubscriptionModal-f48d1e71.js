import"./base-44b135f9.js";import{a as E}from"./el-radio-c6244bc3.js";import"./el-tag-078cb2d7.js";import"./el-popper-1a2e7b76.js";import"./el-scrollbar-ea5d3c5e.js";import{q as T,s as d,o,e as r,a as t,t as $,b as p,w as i,f as u,g as l,F as m,c as b,i as h,n as f}from"./app-5698ab4f.js";import{_ as V,a as O}from"./TextInput-67707cd0.js";import{_ as U}from"./InputLabel-46e89a14.js";import{J as B}from"./Textarea-039076bd.js";import{_ as D}from"./PrimaryButton-949d2864.js";import{J}from"./SelectInput-000b2c67.js";import{a as A}from"./DialogModal-fbfad6a6.js";import{_ as F}from"./DangerButton-df8d437d.js";import{_ as L}from"./ActionMessage-d8487664.js";import{_ as z}from"./SecondaryButton-be1f3abd.js";import{_ as N}from"./_plugin-vue_export-helper-c27b6911.js";import"./constants-0e9b956e.js";import"./icon-4c2d7b76.js";import"./index-66fd432c.js";/* empty css            */const I=T({components:{JetLabel:U,JetInput:V,JetTextarea:B,JetButton:D,JetInputError:O,JetSelectInput:J,JetDialogModal:A,JetSecondaryButton:z,JetActionMessage:L,JetDangerButton:F},props:{action:{type:String,default:"update"},modelValue:{type:Boolean,default:!1},showAddbutton:{type:Boolean,default:!1},subscription:{type:Object,default:null},subscriptionPlans:Array,show:{type:Boolean,default:!1}},data(){return{form:null,showModal:this.modelValue,showSuccessMessage:!1,showErrorMessage:!1}},watch:{showModal:{handler:function(e,s){e!=this.modelValue&&this.$emit("update:modelValue",e)}},modelValue:{handler:function(e,s){e!=this.showModal&&(this.showModal=e,this.reset())}}},computed:{hasSubscription(){return this.subscription!=null},hasSubscriber(){return this.hasSubscription&&this.subscription.subscriber!=null},wantsToUpdate(){return!!(this.hasSubscription&&this.action=="update")},wantsToDelete(){return!!(this.hasSubscription&&this.action=="delete")},subscriptionPlanOptions(){return this.subscriptionPlans.map(function(e){return{name:e.name,value:e.id}})}},methods:{getPropsForSubscriptionPlans(){return{lazy:!0,multiple:!1,checkStrictly:!1,lazyLoad:function(e,s){const{level:k}=e;if(k===0)var v=route("api.show.subscription.plans",{project:route().params.project});else var v=route("api.show.subscription.plan",{project:route().params.project,subscription_plan:e.data.value,type:"children"});axios.get(v).then(j=>{var M=j.data.data.map(n=>{var g=n.active,_=n.isFolder,c=n.childrenCount>0,S=!c,w=n.id,y=n.name.length<40?String(n.name):String(n.name).substring(0,40);return{leaf:S,value:w,label:y,disabled:_&&!c||!g}});s(M)}).catch(()=>s([]))}}},openModal(){this.showModal=!0},closeModal(){this.showModal=!1},create(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess()},onError:s=>{this.handleOnError()}};this.form.transform(s=>(s.subscription_plan_id.length>0&&(s.subscription_plan_id=s.subscription_plan_id[s.subscription_plan_id.length-1]),s)).post(route("create.subscription",{project:route().params.project}),e)},update(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess()},onError:s=>{this.handleOnError()}};this.form.put(route("update.subscription",{project:route().params.project,subscription:this.subscription.id}),e)},cancel(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess()},onError:s=>{this.handleOnError()}};this.form.post(route("cancel.subscription",{project:route().params.project,subscription:this.subscription.id}),e)},uncancel(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess()},onError:s=>{this.handleOnError()}};this.form.post(route("uncancel.subscription",{project:route().params.project,subscription:this.subscription.id}),e)},destroy(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess(!0)},onError:s=>{this.handleOnError()}};this.form.delete(route("delete.subscription",{project:route().params.project,subscription:this.subscription.id}),e)},handleOnSuccess(e=!1){this.reset(),this.closeModal(),e&&this.$emit("onDeleted"),this.showSuccessMessage=!0,setTimeout(()=>{this.showSuccessMessage=!1},3e3)},handleOnError(){this.showErrorMessage=!0,setTimeout(()=>{this.showErrorMessage=!1},3e3)},reset(){this.form=this.$inertia.form({cancelled_at:this.hasSubscriber?this.subscription.cancelled_at:null,msisdn:this.hasSubscriber?this.subscription.subscriber.msisdn??null:null,subscription_plan_id:this.hasSubscription?[this.subscription.subscription_plan_id]:[]})}},created(){this.reset()}}),P={key:0,class:"grid grid-cols-2 mb-6 gap-4"},q={class:"bg-gray-50 pt-4 pl-6 border-b rounded-t"},G=t("div",{class:"text-2xl font-semibold leading-6 text-gray-500 border-b pb-4 mb-4"},"Subscriptions",-1),H={class:"text-sm text-gray-500 my-2"},K=t("span",{class:"font-bold mr-2"},"GET / POST:",-1),Q={class:"text-green-500 font-semibold"},R=t("div",{class:"clear-both"},null,-1),W={key:0,class:"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6",role:"alert"},X={key:0,class:"font-bold"},Y={key:1,class:"font-bold"},Z={key:2,class:"font-bold"},x=t("svg",{class:"fill-current h-6 w-6 text-green-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[t("title",null,"Close"),t("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),ee=[x],se={key:1,class:"bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6",role:"alert"},te={key:0,class:"font-bold"},oe={key:1,class:"font-bold"},re={key:2,class:"font-bold"},ne=t("svg",{class:"fill-current h-6 w-6 text-red-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[t("title",null,"Close"),t("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),ae=[ne],ie=t("span",{class:"block mt-6 mb-6"},"Are you sure you want to delete this subscription?",-1),le={class:"text-sm text-gray-500"},pe={class:"mb-4"},ue={class:"mb-4"};function de(e,s,k,v,j,M){const n=d("jet-button"),g=d("jet-label"),_=d("jet-input"),c=d("jet-input-error"),S=E,w=d("jet-secondary-button"),y=d("jet-danger-button"),C=d("jet-dialog-modal");return o(),r("div",null,[e.$inertia.page.props.projectPermissions.includes("Manage subscriptions")&&e.showAddbutton?(o(),r("div",P,[t("div",q,[G,t("div",H,[K,t("span",Q,$(e.route("api.create.subscription",{project:e.route().params.project})),1)])]),t("div",null,[p(n,{onClick:s[0]||(s[0]=a=>e.openModal()),class:"float-right w-fit"},{default:i(()=>[l(" Add Subscription ")]),_:1}),R])])):u("",!0),t("div",null,[e.showSuccessMessage?(o(),r("div",W,[e.wantsToUpdate?(o(),r("strong",X,"Subscription updated successfully")):e.wantsToDelete?(o(),r("strong",Y,"Subscription deleted successfully")):(o(),r("strong",Z,"Subscription created successfully")),t("span",{onClick:s[1]||(s[1]=a=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},ee)])):u("",!0),e.showErrorMessage?(o(),r("div",se,[e.wantsToUpdate?(o(),r("strong",te,"Subscription update failed")):e.wantsToDelete?(o(),r("strong",oe,"Subscription delete failed")):(o(),r("strong",re,"Subscription creation failed")),t("span",{onClick:s[2]||(s[2]=a=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},ae)])):u("",!0),p(C,{show:e.showModal,closeable:!1},{title:i(()=>[e.wantsToUpdate?(o(),r(m,{key:0},[l("Update Subscription")],64)):e.wantsToDelete?(o(),r(m,{key:1},[l("Delete Subscription")],64)):(o(),r(m,{key:2},[l("Add Subscription")],64))]),content:i(()=>[e.wantsToDelete?(o(),r(m,{key:0},[ie,t("p",le,$(e.hasSubscriber?e.subscription.subscriber.msisdn:"Unknown"),1)],64)):(o(),r(m,{key:1},[t("div",pe,[p(g,{for:"msisdn",value:"Mobile"}),p(_,{id:"msisdn",type:"text",class:"w-full mt-1 block",modelValue:e.form.msisdn,"onUpdate:modelValue":s[3]||(s[3]=a=>e.form.msisdn=a),placeholder:"26772000001"},null,8,["modelValue"]),p(c,{message:e.form.errors.msisdn,class:"mt-2"},null,8,["message"])]),t("div",null,[t("div",ue,[p(g,{for:"subscription-plan",value:"Subscription Plan",class:"mb-1"}),p(S,{id:"subscription-plan",modelValue:e.form.subscription_plan_id,"onUpdate:modelValue":s[4]||(s[4]=a=>e.form.subscription_plan_id=a),props:e.getPropsForSubscriptionPlans(),"collapse-tags":"","collapse-tags-tooltip":"",clearable:"",class:"w-full"},null,8,["modelValue","props"]),p(c,{message:e.form.errors.subscription_plan_id,class:"mt-2"},null,8,["message"])])])],64))]),footer:i(()=>[p(w,{onClick:s[5]||(s[5]=a=>e.closeModal()),class:"mr-2"},{default:i(()=>[l(" Close ")]),_:1}),e.hasSubscription?u("",!0):(o(),b(n,{key:0,onClick:s[6]||(s[6]=h(a=>e.create(),["prevent"])),class:f({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:i(()=>[l(" Create ")]),_:1},8,["class","disabled"])),e.wantsToUpdate&&e.form.cancelled_at==null?(o(),b(n,{key:1,onClick:s[7]||(s[7]=h(a=>e.cancel(),["prevent"])),class:f([{"opacity-25":e.form.processing},"mr-2"]),disabled:e.form.processing},{default:i(()=>[l(" Cancel ")]),_:1},8,["class","disabled"])):u("",!0),e.wantsToUpdate&&e.form.cancelled_at!=null?(o(),b(n,{key:2,onClick:s[8]||(s[8]=h(a=>e.uncancel(),["prevent"])),class:f([{"opacity-25":e.form.processing},"mr-2"]),disabled:e.form.processing},{default:i(()=>[l(" Uncancel ")]),_:1},8,["class","disabled"])):u("",!0),e.wantsToUpdate?(o(),b(n,{key:3,onClick:s[9]||(s[9]=h(a=>e.update(),["prevent"])),class:f({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:i(()=>[l(" Update ")]),_:1},8,["class","disabled"])):u("",!0),e.wantsToDelete?(o(),b(y,{key:4,onClick:s[10]||(s[10]=h(a=>e.destroy(),["prevent"])),class:f({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:i(()=>[l(" Delete ")]),_:1},8,["class","disabled"])):u("",!0)]),_:1},8,["show"])])])}const Ue=N(I,[["render",de]]);export{Ue as default};
