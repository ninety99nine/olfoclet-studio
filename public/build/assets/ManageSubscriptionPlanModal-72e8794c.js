import"./base-d87a8159.js";import{E as A,a as O}from"./el-tag-f7980671.js";import{a as z,E as J}from"./el-select-0f80057f.js";import"./el-scrollbar-298c55cf.js";import"./el-popper-100bb3f5.js";import{E as R,a as Y}from"./el-switch-1853c3da.js";import{E as W}from"./el-popover-8512bb4f.js";import{E as q,a as F}from"./el-breadcrumb-item-c1d2fcc3.js";import{d as K,o as l,e as i,s as G,q as Q,a as s,t as u,b as o,w as a,f as d,x as c,F as m,h as v,c as f,g as r,bw as X,i as T,n as M}from"./app-6e19013a.js";import{_ as Z,a as ee}from"./TextInput-5a17770e.js";import{_ as se}from"./InputLabel-147311c4.js";import{J as te}from"./Textarea-625b783f.js";import{_ as ne}from"./PrimaryButton-a695b726.js";import{J as oe}from"./SelectInput-1c501c6e.js";import{a as le}from"./DialogModal-94324879.js";import{_ as ie}from"./DangerButton-9bd1d3d7.js";import{_ as ae}from"./SecondaryButton-63220d0d.js";import{_ as re}from"./_plugin-vue_export-helper-c27b6911.js";import"./constants-481c1ee8.js";import"./icon-20febbf5.js";import"./index-fe677398.js";/* empty css            */const ue=["value","maxlength","placeholder"],de={__name:"NumberInput",props:{maxlength:Number,placeholder:String,modelValue:[Number,String]},emits:["update:modelValue"],setup(e){const t=K(null);return(V,w)=>(l(),i("input",{ref_key:"input",ref:t,type:"number",class:"border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm",value:e.modelValue,maxlength:e.maxlength,placeholder:e.placeholder,onInput:w[0]||(w[0]=y=>V.$emit("update:modelValue",y.target.value?parseInt(y.target.value):null))},null,40,ue))}},me=G({components:{JetLabel:se,JetInput:Z,JetNumberInput:de,JetTextarea:te,JetButton:ne,JetInputError:ee,JetSelectInput:oe,JetDialogModal:le,JetSecondaryButton:ae,JetDangerButton:ie},props:{action:String,breadcrumbs:Array,modelValue:Boolean,subscriptionPlan:Object,autoBillingReminders:Array,parentSubscriptionPlan:Object,showHeader:{type:Boolean,default:!1}},data(){return{form:null,addTagInput:"",showAddTagInput:!1,showModal:this.modelValue,showSuccessMessage:!1,showErrorMessage:!1}},watch:{showModal:{handler:function(e,t){e!=this.modelValue&&this.$emit("update:modelValue",e)}},modelValue:{handler:function(e,t){e!=this.showModal&&(this.showModal=e,this.reset())}}},computed:{hasSubscriptionPlan(){return this.subscriptionPlan!=null},wantsToUpdate(){return!!(this.hasSubscriptionPlan&&this.action=="update")},wantsToDelete(){return!!(this.hasSubscriptionPlan&&this.action=="delete")},frequencyOptions(){return[{name:this.form.duration=="1"?"Minute":"Minutes",value:"Minutes"},{name:this.form.duration=="1"?"Hour":"Hours",value:"Hours"},{name:this.form.duration=="1"?"Day":"Days",value:"Days"},{name:this.form.duration=="1"?"Week":"Weeks",value:"Weeks"},{name:this.form.duration=="1"?"Month":"Months",value:"Months"},{name:this.form.duration=="1"?"Year":"Years",value:"Years"}]},autoBillingReminderOptions(){return this.autoBillingReminders.map(function(e){return{name:e.name,value:e.id}})}},methods:{nagivateToSubscriptionPlan(e=null){e?this.$inertia.get(route("show.subscription.plan",{project:route().params.project,subscription_plan:e.id})):this.$inertia.get(route("show.subscription.plans",{project:route().params.project}))},goBackToPreviousPage(){window.history.back()},openModal(){this.showModal=!0},closeModal(){this.showModal=!1},handleAddTag(){if(this.addTagInput){const e=this.addTagInput.trim();this.form.tags.includes(e)||this.form.tags.push(e)}this.showAddTagInput=!1,this.addTagInput=""},handleRemoveTag(e){this.form.tags.splice(this.form.tags.indexOf(e),1)},showInput(){this.showAddTagInput=!0,Q(()=>{this.$refs.addTagInputRef.focus()})},create(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:t=>{this.handleOnSuccess()},onError:t=>{this.handleOnError()}};this.form.transform(t=>t.is_folder?{name:t.name,active:t.active,is_folder:t.is_folder,parent_id:t.parent_id}:(t.can_auto_bill==!1&&(delete t.next_auto_billing_reminder_sms_message,delete t.auto_billing_reminder_ids),t)).post(route("create.subscription.plan",{project:route().params.project}),e)},update(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:t=>{this.handleOnSuccess()},onError:t=>{this.handleOnError()}};this.form.transform(t=>t.is_folder?{name:t.name,active:t.active,is_folder:t.is_folder,parent_id:t.parent_id}:(t.can_auto_bill==!1&&(delete t.next_auto_billing_reminder_sms_message,delete t.auto_billing_reminder_ids),t)).put(route("update.subscription.plan",{project:route().params.project,subscription_plan:this.subscriptionPlan.id}),e)},destroy(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:t=>{this.handleOnSuccess(!0)},onError:t=>{this.handleOnError()}};this.form.delete(route("delete.subscription.plan",{project:route().params.project,subscription_plan:this.subscriptionPlan.id}),e)},handleOnSuccess(e=!1){this.reset(),this.closeModal(),e&&this.$emit("onDeleted"),this.showSuccessMessage=!0,setTimeout(()=>{this.showSuccessMessage=!1},3e3)},handleOnError(){this.showErrorMessage=!0,setTimeout(()=>{this.showErrorMessage=!1},3e3)},reset(){this.form=this.$inertia.form({name:this.hasSubscriptionPlan?this.subscriptionPlan.name:null,active:this.hasSubscriptionPlan?this.subscriptionPlan.active:!1,tags:this.hasSubscriptionPlan?this.subscriptionPlan.tags??[]:[],is_folder:this.hasSubscriptionPlan?this.subscriptionPlan.is_folder:!1,frequency:this.hasSubscriptionPlan?this.subscriptionPlan.frequency:"Days",parent_id:this.parentSubscriptionPlan?this.parentSubscriptionPlan.id:null,description:this.hasSubscriptionPlan?this.subscriptionPlan.description:null,can_auto_bill:this.hasSubscriptionPlan?this.subscriptionPlan.can_auto_bill:!1,price:this.hasSubscriptionPlan?(this.subscriptionPlan.price??{}).amount_without_currency:null,max_auto_billing_attempts:this.hasSubscriptionPlan?this.subscriptionPlan.max_auto_billing_attempts:3,duration:this.hasSubscriptionPlan?this.subscriptionPlan.is_folder?null:this.subscriptionPlan.duration.toString():"3",auto_billing_reminder_ids:this.hasSubscriptionPlan?this.subscriptionPlan.auto_billing_reminders.map(e=>e.id):[],insufficient_funds_message:this.hasSubscriptionPlan?this.subscriptionPlan.insufficient_funds_message:"You do not have enough funds to complete this transaction",next_auto_billing_reminder_sms_message:this.hasSubscriptionPlan?this.subscriptionPlan.next_auto_billing_reminder_sms_message:"You will be automatically billed {{ subscriptionPlanPrice }} on {{ nextBillableDate }} for {{ subscriptionPlanName }}. Dial *xxx# to unsubscribe.",successful_payment_sms_message:this.hasSubscriptionPlan?this.subscriptionPlan.successful_payment_sms_message:"Your payment for {{ subscriptionPlanName }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }}. Dial *xxx# to unsubscribe."})}},created(){this.reset()}}),pe={key:0,class:"grid grid-cols-12 mb-6 gap-4"},ce={class:"col-span-8 bg-gray-50 pt-4 pl-6 border-b rounded-t"},be={class:"text-2xl font-semibold leading-6 text-gray-500 border-b pb-4 mb-4"},fe={key:0,class:"border-b"},he=s("svg",{xmlns:"http://www.w3.org/2000/svg",class:"w-4",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor","stroke-width":"2"},[s("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M7 16l-4-4m0 0l4-4m-4 4h18"})],-1),_e=s("span",{class:"ml-2"},"Go Back",-1),ge=s("span",{class:"hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer"},"Subscription Plans",-1),ve={class:"hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer"},we={class:"text-sm text-gray-500 my-2"},ye=s("span",{class:"font-bold mr-2"},"GET / POST:",-1),Se={key:0,class:"text-green-500 font-semibold"},Pe={key:1,class:"text-green-500 font-semibold"},ke={key:0,class:"col-span-4 flex justify-end items-start"},Te={key:0,class:"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 mt-3",role:"alert"},Me={key:0,class:"font-bold"},Ve={key:1,class:"font-bold"},je={key:2,class:"font-bold"},xe=s("svg",{class:"fill-current h-6 w-6 text-green-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[s("title",null,"Close"),s("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),Be=[xe],De={key:1,class:"bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 mt-3",role:"alert"},Ee={key:0,class:"font-bold"},Ce={key:1,class:"font-bold"},$e={key:2,class:"font-bold"},Le=s("svg",{class:"fill-current h-6 w-6 text-red-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[s("title",null,"Close"),s("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),Ie=[Le],He=s("span",{class:"block mt-6 mb-6"},"Are you sure you want to delete this subscription plan?",-1),Ue={class:"text-sm text-gray-500"},Ne={class:"block mt-6 mb-6"},Ae={key:0},Oe={class:"rounded-lg py-1 px-2 border border-green-400 text-green-500 text-sm"},ze={key:1,class:"bg-gray-50 py-3 px-3 mt-6 mb-2"},Je=s("span",{class:"hover:text-green-600 text-green-500 font-semibold"},"Subscription Plans",-1),Re={class:"text-green-500 font-semibold"},Ye={class:"mb-4"},We={class:"mb-4"},qe=s("span",{class:"text-sm text-gray-500"},"Active",-1),Fe={class:"text-sm text-gray-400"},Ke={class:"mb-4"},Ge=s("span",{class:"text-sm text-gray-500"},"Folder",-1),Qe={class:"text-sm text-gray-400"},Xe={class:"grid gap-4 grid-cols-2"},Ze={class:"mb-4"},es={class:"mb-4"},ss={class:"mb-4"},ts={class:"mb-4"},ns={class:"flex mb-1"},os=s("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[s("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),ls=s("span",{class:"break-normal"}," When billing the subscriber and creating airtime transation records, this description will be used as the transaction description. ",-1),is={class:"flex bg-slate-50 rounded-lg p-4 mb-4"},as=s("span",{class:"font-bold mr-2"},"Category",-1),rs={key:0,class:"w-20"},us={class:"mb-4"},ds={class:"flex mb-1"},ms=s("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[s("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),ps=s("span",{class:"break-normal"}," While billing, this message will be shown to the subscriber if they have insufficient funds to complete the transaction ",-1),cs=s("div",{class:"mt-4"},[s("table",{class:"w-full divide-y divide-gray-200"},[s("thead",null,[s("tr",{class:"font-bold"},[s("td",null,"Variable"),s("td",null,"Meaning")])]),s("tbody",{class:"divide-y divide-gray-200"},[s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanName }}"}),s("td",null,"The Subscription Plan name")]),s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanPrice }}"}),s("td",null,"The Subscription Plan price")])])])],-1),bs=s("p",{class:"mt-4"},[s("strong",null,"Example:"),r(),s("span",{innerHTML:"You do not have enough funds to complete this transaction"})],-1),fs={class:"mb-4"},hs={class:"flex mb-1"},_s=s("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[s("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),gs=s("span",{class:"break-normal"}," While billing, this SMS message will be sent to the subscriber if they have sufficient funds to complete the transaction ",-1),vs=s("div",{class:"mt-4"},[s("table",{class:"w-full divide-y divide-gray-200"},[s("thead",null,[s("tr",{class:"font-bold"},[s("td",null,"Variable"),s("td",null,"Meaning")])]),s("tbody",{class:"divide-y divide-gray-200"},[s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionId }}"}),s("td",null,"The Subscription ID")]),s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ nextBillableDate }}"}),s("td",null,"The next billable date")]),s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionEndDate }}"}),s("td",null,"The Subscription end date")]),s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionStartDate }}"}),s("td",null,"The Subscription start date")]),s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanName }}"}),s("td",null,"The Subscription Plan name")]),s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanPrice }}"}),s("td",null,"The Subscription Plan price")])])])],-1),ws={class:"mt-4"},ys=s("strong",null,"Example:",-1),Ss=["innerHTML"],Ps={class:"mb-4"},ks=s("span",{class:"text-sm text-gray-500"},"Can Auto Bill",-1),Ts={class:"text-sm text-gray-400"},Ms={class:"mb-4"},Vs={class:"flex items-center mb-4"},js=s("span",{class:"font-medium text-sm text-gray-700 whitespace-nowrap mr-2"},"Next Auto Billing Reminder:",-1),xs={class:"w-full"},Bs={class:"mb-4"},Ds={class:"flex mb-1"},Es=s("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[s("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),Cs=s("span",{class:"break-normal"}," Before billing, this SMS message will be sent to the subscriber to notify them 24 hours before auto billing ",-1),$s={class:"mt-4"},Ls=s("table",{class:"w-full divide-y divide-gray-200"},[s("thead",null,[s("tr",{class:"font-bold"},[s("td",null,"Variable"),s("td",null,"Meaning")])]),s("tbody",{class:"divide-y divide-gray-200"},[s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ nextBillableDate }}"}),s("td",null,"The next billable date")]),s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanName }}"}),s("td",null,"The Subscription Plan name")]),s("tr",null,[s("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanPrice }}"}),s("td",null,"The Subscription Plan price")])])],-1),Is={class:"mt-4"},Hs=s("strong",null,"Example:",-1),Us=["innerHTML"];function Ns(e,t,V,w,y,As){const j=c("jet-secondary-button"),h=F,x=q,S=c("jet-button"),b=c("jet-label"),B=c("jet-input"),p=c("jet-input-error"),P=R,k=c("jet-number-input"),D=c("jet-select-input"),_=W,g=c("jet-textarea"),E=A,C=O,$=Y,L=z,I=J,H=c("jet-danger-button"),U=c("jet-dialog-modal");return l(),i("div",null,[e.showHeader?(l(),i("div",pe,[s("div",ce,[s("div",be,u(e.parentSubscriptionPlan?e.parentSubscriptionPlan.name:"Subscription Plans"),1),e.parentSubscriptionPlan?(l(),i("div",fe,[o(j,{onClick:t[0]||(t[0]=n=>e.goBackToPreviousPage()),class:"py-1 mb-4"},{default:a(()=>[he,_e]),_:1}),o(x,{separator:">",class:"mb-4"},{default:a(()=>[o(h,{onClick:t[1]||(t[1]=n=>e.nagivateToSubscriptionPlan())},{default:a(()=>[ge]),_:1}),(l(!0),i(m,null,v(e.breadcrumbs,n=>(l(),f(h,{key:n.id,onClick:N=>e.nagivateToSubscriptionPlan(n)},{default:a(()=>[s("span",ve,u(n.name),1)]),_:2},1032,["onClick"]))),128))]),_:1})])):d("",!0),s("div",we,[ye,e.parentSubscriptionPlan?(l(),i("span",Se,u(e.route("api.show.subscription.plan",{project:e.route().params.project,subscription_plan:e.parentSubscriptionPlan.id,type:"children"})),1)):(l(),i("span",Pe,u(e.route("api.show.subscription.plans",{project:e.route().params.project})),1))])]),e.$inertia.page.props.projectPermissions.includes("Manage subscription plans")?(l(),i("div",ke,[o(S,{onClick:t[2]||(t[2]=n=>e.openModal())},{default:a(()=>[r("Add Subscription Plan")]),_:1})])):d("",!0)])):d("",!0),s("div",null,[e.showSuccessMessage?(l(),i("div",Te,[e.wantsToUpdate?(l(),i("strong",Me,"Subscription plan updated successfully")):e.wantsToDelete?(l(),i("strong",Ve,"Subscription plan deleted successfully")):(l(),i("strong",je,"Subscription plan created successfully")),s("span",{onClick:t[3]||(t[3]=n=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},Be)])):d("",!0),e.showErrorMessage?(l(),i("div",De,[e.wantsToUpdate?(l(),i("strong",Ee,"Subscription plan update failed")):e.wantsToDelete?(l(),i("strong",Ce,"Subscription plan delete failed")):(l(),i("strong",$e,"Subscription plan creation failed")),s("span",{onClick:t[4]||(t[4]=n=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},Ie)])):d("",!0),o(U,{show:e.showModal,closeable:!1},{title:a(()=>[e.wantsToUpdate?(l(),i(m,{key:0},[r("Update Subscription Plan")],64)):e.wantsToDelete?(l(),i(m,{key:1},[r("Delete Subscription Plan")],64)):(l(),i(m,{key:2},[r("Add Subscription Plan")],64))]),content:a(()=>[e.wantsToDelete?(l(),i(m,{key:0},[He,s("p",Ue,u(e.subscriptionPlan.name),1)],64)):(l(),i(m,{key:1},[s("span",Ne,[e.parentSubscriptionPlan?(l(),i("span",Ae,[r(" You are "+u(e.wantsToUpdate?"updating":"adding")+" a subscription plan for ",1),s("span",Oe,u(e.parentSubscriptionPlan.name),1)])):d("",!0),e.parentSubscriptionPlan?(l(),i("div",ze,[o(x,{separator:">"},{default:a(()=>[o(h,null,{default:a(()=>[Je]),_:1}),(l(!0),i(m,null,v(e.breadcrumbs,n=>(l(),f(h,{key:n.id},{default:a(()=>[s("span",Re,u(n.name),1)]),_:2},1024))),128))]),_:1})])):d("",!0)]),s("div",Ye,[o(b,{for:"name",value:"Name"}),o(B,{id:"name",type:"text",class:"w-full mt-1 block",modelValue:e.form.name,"onUpdate:modelValue":t[5]||(t[5]=n=>e.form.name=n),placeholder:"Daily @ P95.00"},null,8,["modelValue"]),o(p,{message:e.form.errors.name,class:"mt-2"},null,8,["message"])]),s("div",We,[qe,o(P,{modelValue:e.form.active,"onUpdate:modelValue":t[6]||(t[6]=n=>e.form.active=n),class:"mx-2"},null,8,["modelValue"]),s("span",Fe,"— "+u(e.form.active?"Turn off to disable this subscription plan":"Turn on to enable this subscription plan"),1)]),s("div",Ke,[Ge,o(P,{modelValue:e.form.is_folder,"onUpdate:modelValue":t[7]||(t[7]=n=>e.form.is_folder=n),class:"mx-2"},null,8,["modelValue"]),s("span",Qe,"— "+u(e.form.is_folder?"Turn off to make this a subscription plan":"Turn on to make this a folder"),1)]),e.form.is_folder==!1?(l(),i(m,{key:0},[s("div",Xe,[s("div",Ze,[o(b,{for:"duration",value:"Duration"}),o(k,{id:"duration",class:"w-full mt-1 block",modelValue:e.form.duration,"onUpdate:modelValue":t[8]||(t[8]=n=>e.form.duration=n),modelModifiers:{string:!0},placeholder:"1"},null,8,["modelValue"]),o(p,{message:e.form.errors.duration,class:"mt-2"},null,8,["message"])]),s("div",es,[o(D,{placeholder:"Select frequency",options:e.frequencyOptions,modelValue:e.form.frequency,"onUpdate:modelValue":t[9]||(t[9]=n=>e.form.frequency=n),class:"mt-6"},null,8,["options","modelValue"]),o(p,{message:e.form.errors.frequency,class:"mt-2"},null,8,["message"])])]),s("div",ss,[o(b,{for:"price",value:"Price",class:"mb-1"}),o(k,{id:"price",class:"w-full mt-1 block",modelValue:e.form.price,"onUpdate:modelValue":t[10]||(t[10]=n=>e.form.price=n),placeholder:"95.00"},null,8,["modelValue"]),o(p,{message:e.form.errors.price,class:"mt-2"},null,8,["message"])]),s("div",ts,[s("div",ns,[o(b,{for:"sub-pl-description",value:"Description"}),o(_,{width:300},{reference:a(()=>[os]),default:a(()=>[ls]),_:1})]),o(g,{id:"sub-pl-description",class:"w-full mt-1 block",modelValue:e.form.description,"onUpdate:modelValue":t[11]||(t[11]=n=>e.form.description=n)},null,8,["modelValue"]),o(p,{message:e.form.errors.description,class:"mt-2"},null,8,["message"])]),s("div",is,[as,(l(!0),i(m,null,v(e.form.tags,n=>(l(),f(E,{key:n,class:"mx-1",closable:"","disable-transitions":!1,onClose:N=>e.handleRemoveTag(n)},{default:a(()=>[r(u(n),1)]),_:2},1032,["onClose"]))),128)),e.showAddTagInput?(l(),i("span",rs,[o(C,{ref:"addTagInputRef",modelValue:e.addTagInput,"onUpdate:modelValue":t[12]||(t[12]=n=>e.addTagInput=n),size:"small",onKeyup:X(e.handleAddTag,["enter"]),onBlur:e.handleAddTag},null,8,["modelValue","onKeyup","onBlur"])])):(l(),f($,{key:1,class:"button-new-tag ml-1",size:"small",onClick:e.showInput},{default:a(()=>[r(" + New Tag ")]),_:1},8,["onClick"]))]),s("div",us,[s("div",ds,[o(b,{for:"sub-pl-insufficient-funds-message",value:"Insufficient Funds Message"}),o(_,{width:400},{reference:a(()=>[ms]),default:a(()=>[ps,cs,bs]),_:1})]),o(g,{id:"sub-pl-insufficient-funds-message",class:"w-full mt-1 block",modelValue:e.form.insufficient_funds_message,"onUpdate:modelValue":t[13]||(t[13]=n=>e.form.insufficient_funds_message=n)},null,8,["modelValue"]),o(p,{message:e.form.errors.insufficient_funds_message,class:"mt-2"},null,8,["message"])]),s("div",fs,[s("div",hs,[o(b,{for:"sub-pl-successful-payment-sms-message",value:"Successful Payment SMS Message"}),o(_,{width:400},{reference:a(()=>[_s]),default:a(()=>[gs,vs,s("p",ws,[ys,r(),s("span",{innerHTML:"Your payment for {{ subscriptionPlanName }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }}. Dial *xxx# to unsubscribe."},null,8,Ss)])]),_:1})]),o(g,{id:"sub-pl-successful-payment-sms-message",class:"w-full mt-1 block",modelValue:e.form.successful_payment_sms_message,"onUpdate:modelValue":t[14]||(t[14]=n=>e.form.successful_payment_sms_message=n)},null,8,["modelValue"]),o(p,{message:e.form.errors.successful_payment_sms_message,class:"mt-2"},null,8,["message"])]),s("div",Ps,[ks,o(P,{modelValue:e.form.can_auto_bill,"onUpdate:modelValue":t[15]||(t[15]=n=>e.form.can_auto_bill=n),class:"mx-2"},null,8,["modelValue"]),s("span",Ts,"— "+u(e.form.can_auto_bill?"Turn off to disable auto billing on this subscription plan":"Turn on to enable auto billing on this subscription plan"),1)]),e.form.can_auto_bill?(l(),i(m,{key:0},[s("div",Ms,[o(b,{for:"max_auto_billing_attempts",value:"Maximum Auto Billing Attempts"}),o(k,{id:"max_auto_billing_attempts",class:"w-full mt-1",modelValue:e.form.max_auto_billing_attempts,"onUpdate:modelValue":t[16]||(t[16]=n=>e.form.max_auto_billing_attempts=n),modelModifiers:{string:!0},min:"1",max:"10",placeholder:"3"},null,8,["modelValue"]),o(p,{message:e.form.errors.max_auto_billing_attempts,class:"mt-2"},null,8,["message"])]),s("div",Vs,[js,s("div",xs,[o(I,{modelValue:e.form.auto_billing_reminder_ids,"onUpdate:modelValue":t[17]||(t[17]=n=>e.form.auto_billing_reminder_ids=n),multiple:"",placeholder:"Set Reminders",class:"w-full"},{default:a(()=>[(l(!0),i(m,null,v(e.autoBillingReminderOptions,n=>(l(),f(L,{key:n.value,value:n.value,label:n.name},null,8,["value","label"]))),128))]),_:1},8,["modelValue"]),o(p,{message:e.form.errors.auto_billing_reminder_ids,class:"mt-2"},null,8,["message"])])]),s("div",Bs,[s("div",Ds,[o(b,{for:"sub-pl-next-auto-billing-reminder-sms-message",value:"Next Auto Billing Reminder SMS Message"}),o(_,{width:400},{reference:a(()=>[Es]),default:a(()=>[Cs,s("div",$s,[Ls,s("p",Is,[Hs,r(),s("span",{innerHTML:"You will be automatically billed {{ subscriptionPlanPrice }} on {{ nextBillableDate }} for {{ subscriptionPlanName }}. Dial *xxx# to unsubscribe."},null,8,Us)])])]),_:1})]),o(g,{id:"sub-pl-next-auto-billing-reminder-sms-message",class:"w-full mt-1 block",modelValue:e.form.next_auto_billing_reminder_sms_message,"onUpdate:modelValue":t[18]||(t[18]=n=>e.form.next_auto_billing_reminder_sms_message=n)},null,8,["modelValue"]),o(p,{message:e.form.errors.next_auto_billing_reminder_sms_message,class:"mt-2"},null,8,["message"])])],64)):d("",!0)],64)):d("",!0)],64))]),footer:a(()=>[o(j,{onClick:t[19]||(t[19]=n=>e.closeModal()),class:"mr-2"},{default:a(()=>[r(" Cancel ")]),_:1}),e.hasSubscriptionPlan?d("",!0):(l(),f(S,{key:0,onClick:t[20]||(t[20]=T(n=>e.create(),["prevent"])),class:M({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:a(()=>[r(" Create ")]),_:1},8,["class","disabled"])),e.wantsToUpdate?(l(),f(S,{key:1,onClick:t[21]||(t[21]=T(n=>e.update(),["prevent"])),class:M({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:a(()=>[r(" Update ")]),_:1},8,["class","disabled"])):d("",!0),e.wantsToDelete?(l(),f(H,{key:2,onClick:t[22]||(t[22]=T(n=>e.destroy(),["prevent"])),class:M({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:a(()=>[r(" Delete ")]),_:1},8,["class","disabled"])):d("",!0)]),_:1},8,["show"])])])}const ut=re(me,[["render",Ns]]);export{ut as default};
