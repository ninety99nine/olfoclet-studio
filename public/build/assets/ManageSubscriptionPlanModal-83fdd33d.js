import"./base-ddf78397.js";import{E as N,a as z}from"./el-tag-d42029af.js";import{a as Y,E as O}from"./el-select-39ec4542.js";import"./el-scrollbar-6c79e798.js";import"./el-popper-a31ea41e.js";import{E as W,a as R}from"./el-switch-a6c847bb.js";import{E as J}from"./el-popover-9738fa6d.js";import{E as q,a as F}from"./el-breadcrumb-item-22f8fc3d.js";import{d as G,o,e as a,q as K,s as b,a as e,t as c,b as l,w as i,f as m,p as Q,F as p,h as v,c as f,g as r,bJ as X,i as M,n as V}from"./app-765f6ff9.js";import{_ as Z,a as ee}from"./TextInput-15a0fb84.js";import{_ as se}from"./InputLabel-2905b6e3.js";import{J as te}from"./Textarea-e26bb084.js";import{_ as le}from"./PrimaryButton-0309b98d.js";import{J as ne}from"./SelectInput-ff8cfe78.js";import{a as oe}from"./DialogModal-c4e45d61.js";import{_ as ie}from"./DangerButton-b25f3d66.js";import{_ as ae}from"./SecondaryButton-663f0e86.js";import{_ as re}from"./_plugin-vue_export-helper-c27b6911.js";import"./constants-c4dfd060.js";import"./icon-48f2c17f.js";import"./index-b32ae762.js";/* empty css            */const ue=["value","maxlength","placeholder"],de={__name:"NumberInput",props:{maxlength:Number,placeholder:String,modelValue:[Number,String]},emits:["update:modelValue"],setup(s){const t=G(null);return(x,w)=>(o(),a("input",{ref_key:"input",ref:t,type:"number",class:"border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm",value:s.modelValue,maxlength:s.maxlength,placeholder:s.placeholder,onInput:w[0]||(w[0]=y=>x.$emit("update:modelValue",y.target.value?parseInt(y.target.value):null))},null,40,ue))}},ce=K({components:{JetLabel:se,JetInput:Z,JetNumberInput:de,JetTextarea:te,JetButton:le,JetInputError:ee,JetSelectInput:ne,JetDialogModal:oe,JetSecondaryButton:ae,JetDangerButton:ie},props:{action:String,breadcrumbs:Array,modelValue:Boolean,subscriptionPlan:Object,autoBillingReminders:Array,parentSubscriptionPlan:Object,showHeader:{type:Boolean,default:!1}},data(){return{form:null,addTagInput:"",showAddTagInput:!1,showModal:this.modelValue,showSuccessMessage:!1,showErrorMessage:!1}},watch:{showModal:{handler:function(s,t){s!=this.modelValue&&this.$emit("update:modelValue",s)}},modelValue:{handler:function(s,t){s!=this.showModal&&(this.showModal=s,this.reset())}}},computed:{hasSubscriptionPlan(){return this.subscriptionPlan!=null},wantsToUpdate(){return!!(this.hasSubscriptionPlan&&this.action=="update")},wantsToDelete(){return!!(this.hasSubscriptionPlan&&this.action=="delete")},frequencyOptions(){return[{name:this.form.duration=="1"?"Minute":"Minutes",value:"Minutes"},{name:this.form.duration=="1"?"Hour":"Hours",value:"Hours"},{name:this.form.duration=="1"?"Day":"Days",value:"Days"},{name:this.form.duration=="1"?"Week":"Weeks",value:"Weeks"},{name:this.form.duration=="1"?"Month":"Months",value:"Months"},{name:this.form.duration=="1"?"Year":"Years",value:"Years"}]},autoBillingReminderOptions(){return this.autoBillingReminders.map(function(s){return{name:s.name,value:s.id}})}},methods:{nagivateToSubscriptionPlan(s=null){s?this.$inertia.get(route("show.subscription.plan",{project:route().params.project,subscription_plan:s.id})):this.$inertia.get(route("show.subscription.plans",{project:route().params.project}))},goBackToPreviousPage(){window.history.back()},openModal(){this.showModal=!0},closeModal(){this.showModal=!1},handleAddTag(){if(this.addTagInput){const s=this.addTagInput.trim();this.form.tags.includes(s)||this.form.tags.push(s)}this.showAddTagInput=!1,this.addTagInput=""},handleRemoveTag(s){this.form.tags.splice(this.form.tags.indexOf(s),1)},showInput(){this.showAddTagInput=!0,Q(()=>{this.$refs.addTagInputRef.focus()})},create(){var s={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:t=>{this.handleOnSuccess()},onError:t=>{this.handleOnError()}};this.form.transform(t=>t.is_folder?{name:t.name,active:t.active,is_folder:t.is_folder,parent_id:t.parent_id}:t.can_auto_bill==!1?(delete t.next_auto_billing_reminder_sms_message,delete t.auto_billing_reminder_ids,t):((t.billing_purchase_category_code==null||t.billing_purchase_category_code.trim()=="")&&delete t.billing_purchase_category_code,(t.billing_product_id==null||t.billing_product_id.trim()=="")&&delete t.billing_product_id,t)).post(route("create.subscription.plan",{project:route().params.project}),s)},update(){var s={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:t=>{this.handleOnSuccess()},onError:t=>{this.handleOnError()}};this.form.transform(t=>t.is_folder?{name:t.name,active:t.active,is_folder:t.is_folder,parent_id:t.parent_id}:(t.can_auto_bill==!1&&(delete t.next_auto_billing_reminder_sms_message,delete t.auto_billing_reminder_ids),t)).put(route("update.subscription.plan",{project:route().params.project,subscription_plan:this.subscriptionPlan.id}),s)},destroy(){var s={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:t=>{this.handleOnSuccess(!0)},onError:t=>{this.handleOnError()}};this.form.delete(route("delete.subscription.plan",{project:route().params.project,subscription_plan:this.subscriptionPlan.id}),s)},handleOnSuccess(s=!1){this.reset(),this.closeModal(),s&&this.$emit("onDeleted"),this.showSuccessMessage=!0,setTimeout(()=>{this.showSuccessMessage=!1},3e3)},handleOnError(){this.showErrorMessage=!0,setTimeout(()=>{this.showErrorMessage=!1},3e3)},reset(){this.form=this.$inertia.form({name:this.hasSubscriptionPlan?this.subscriptionPlan.name:null,active:this.hasSubscriptionPlan?this.subscriptionPlan.active:!1,tags:this.hasSubscriptionPlan?this.subscriptionPlan.tags??[]:[],is_folder:this.hasSubscriptionPlan?this.subscriptionPlan.is_folder:!1,frequency:this.hasSubscriptionPlan?this.subscriptionPlan.frequency:"Days",parent_id:this.parentSubscriptionPlan?this.parentSubscriptionPlan.id:null,description:this.hasSubscriptionPlan?this.subscriptionPlan.description:null,can_auto_bill:this.hasSubscriptionPlan?this.subscriptionPlan.can_auto_bill:!1,price:this.hasSubscriptionPlan?(this.subscriptionPlan.price??{}).amount_without_currency:null,max_auto_billing_attempts:this.hasSubscriptionPlan?this.subscriptionPlan.max_auto_billing_attempts:3,billing_product_id:this.hasSubscriptionPlan?this.subscriptionPlan.billing_product_id:"Product 123",billing_purchase_category_code:this.hasSubscriptionPlan?this.subscriptionPlan.billing_purchase_category_code:"",duration:this.hasSubscriptionPlan?this.subscriptionPlan.is_folder?null:this.subscriptionPlan.duration.toString():"3",auto_billing_reminder_ids:this.hasSubscriptionPlan?this.subscriptionPlan.auto_billing_reminders.map(s=>s.id):[],insufficient_funds_message:this.hasSubscriptionPlan?this.subscriptionPlan.insufficient_funds_message:"You do not have enough funds to complete this transaction",auto_billing_disabled_sms_message:this.hasSubscriptionPlan?this.subscriptionPlan.auto_billing_disabled_sms_message:"You have been successfully unsubscribed from {{ subscriptionPlanName }}. Dial *xxx# to subscribe.",next_auto_billing_reminder_sms_message:this.hasSubscriptionPlan?this.subscriptionPlan.next_auto_billing_reminder_sms_message:"You will be automatically billed {{ subscriptionPlanPrice }} on {{ nextBillableDate }} for {{ subscriptionPlanName }}. Dial *xxx# to unsubscribe.",successful_payment_sms_message:this.hasSubscriptionPlan?this.subscriptionPlan.successful_payment_sms_message:"Your payment for {{ subscriptionPlanName }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }}. Dial *xxx# to unsubscribe.",successful_auto_billing_payment_sms_message:this.hasSubscriptionPlan?this.subscriptionPlan.successful_auto_billing_payment_sms_message:"Your payment for {{ subscriptionPlanName }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }}. Dial *xxx# to unsubscribe."})}},created(){this.reset()}}),me={key:0,class:"grid grid-cols-12 mb-6 gap-4"},pe={class:"col-span-8 bg-gray-50 pt-4 pl-6 border-b rounded-t"},be={class:"text-2xl font-semibold leading-6 text-gray-500 border-b pb-4 mb-4"},he={key:0,class:"border-b"},fe=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"w-4",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor","stroke-width":"2"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M7 16l-4-4m0 0l4-4m-4 4h18"})],-1),_e=e("span",{class:"ml-2"},"Go Back",-1),ge=e("span",{class:"hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer"},"Subscription Plans",-1),ve={class:"hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer"},we={class:"text-sm text-gray-500 my-2"},ye=e("span",{class:"font-bold mr-2"},"GET / POST:",-1),Pe={key:0,class:"text-green-500 font-semibold"},Se={key:1,class:"text-green-500 font-semibold"},ke={key:0,class:"col-span-4 flex justify-end items-start"},Te={key:0,class:"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 mt-3",role:"alert"},Me={key:0,class:"font-bold"},Ve={key:1,class:"font-bold"},xe={key:2,class:"font-bold"},Le=e("svg",{class:"fill-current h-6 w-6 text-green-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[e("title",null,"Close"),e("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),Be=[Le],De={key:1,class:"bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 mt-3",role:"alert"},je={key:0,class:"font-bold"},He={key:1,class:"font-bold"},Ee={key:2,class:"font-bold"},Ce=e("svg",{class:"fill-current h-6 w-6 text-red-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[e("title",null,"Close"),e("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),Ie=[Ce],$e=e("span",{class:"block mt-6 mb-6"},"Are you sure you want to delete this subscription plan?",-1),Ae={class:"text-sm text-gray-500"},Ue={class:"block mt-6 mb-6"},Ne={key:0},ze={class:"rounded-lg py-1 px-2 border border-green-400 text-green-500 text-sm"},Ye={key:1,class:"bg-gray-50 py-3 px-3 mt-6 mb-2"},Oe=e("span",{class:"hover:text-green-600 text-green-500 font-semibold"},"Subscription Plans",-1),We={class:"text-green-500 font-semibold"},Re={class:"mb-4"},Je={class:"mb-4"},qe=e("span",{class:"text-sm text-gray-500"},"Active",-1),Fe={class:"text-sm text-gray-400"},Ge={class:"mb-4"},Ke=e("span",{class:"text-sm text-gray-500"},"Folder",-1),Qe={class:"text-sm text-gray-400"},Xe={class:"grid gap-4 grid-cols-2"},Ze={class:"mb-4"},es={class:"mb-4"},ss={class:"mb-4"},ts={class:"mb-4"},ls={class:"flex mb-1"},ns=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[e("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),os=e("span",{class:"break-normal"}," When billing the subscriber and creating airtime transation records, this description will be used as the transaction description. ",-1),is={class:"flex bg-slate-50 rounded-lg p-4 mb-4"},as=e("span",{class:"font-bold mr-2"},"Category",-1),rs={key:0,class:"w-20"},us={class:"mb-4"},ds={class:"flex mb-1"},cs=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[e("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),ms=e("span",{class:"break-normal"}," While billing, this message will be used as the billing transaction description to know why the billing transaction failed. This message can be shown to the user via USSD if required. This is possible since making a subscription via the API endpoint returns the billing transaction resource which can be used to determine the billing transaction success status as well as this message to show the subscriber in the case that the transaction has failed due to insufficient funds. ",-1),ps=e("div",{class:"mt-4"},[e("table",{class:"w-full divide-y divide-gray-200"},[e("thead",null,[e("tr",{class:"font-bold"},[e("td",null,"Variable"),e("td",null,"Meaning")])]),e("tbody",{class:"divide-y divide-gray-200"},[e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanName }}"}),e("td",null,"The Subscription Plan name")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanPrice }}"}),e("td",null,"The Subscription Plan price")])])])],-1),bs=e("p",{class:"mt-4"},[e("strong",null,"Example:"),r(),e("span",{innerHTML:"You do not have enough funds to complete this transaction"})],-1),hs={class:"mb-4"},fs={class:"flex mb-1"},_s=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[e("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),gs=e("span",{class:"break-normal"}," While billing, this message will be sent as an SMS to the subscriber if they have sufficient funds to complete the transaction ",-1),vs=e("div",{class:"mt-4"},[e("table",{class:"w-full divide-y divide-gray-200"},[e("thead",null,[e("tr",{class:"font-bold"},[e("td",null,"Variable"),e("td",null,"Meaning")])]),e("tbody",{class:"divide-y divide-gray-200"},[e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionId }}"}),e("td",null,"The Subscription ID")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ nextBillableDate }}"}),e("td",null,"The next billable date")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionEndDate }}"}),e("td",null,"The Subscription end date")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionStartDate }}"}),e("td",null,"The Subscription start date")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanName }}"}),e("td",null,"The Subscription Plan name")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanPrice }}"}),e("td",null,"The Subscription Plan price")])])])],-1),ws={class:"mt-4"},ys=e("strong",null,"Example:",-1),Ps=["innerHTML"],Ss={class:"mb-4"},ks={class:"flex mb-1"},Ts=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[e("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),Ms=e("span",{class:"break-normal"}," This is the subscription plan identifier used by the Mobile Network to distiguish between transactions based on the product being purchased. Give a unique name that will distinguish this subscription plan as a product ",-1),Vs={class:"mb-4"},xs={class:"flex mb-1"},Ls=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[e("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),Bs=e("span",{class:"break-normal"}," This is the subscription plan category used by the Mobile Network to distiguish between transactions. This parameter MUST be filled with values validated by AAS integration team ",-1),Ds={class:"mb-4"},js=e("span",{class:"text-sm text-gray-500"},"Can Auto Bill",-1),Hs={class:"text-sm text-gray-400"},Es={class:"mb-4"},Cs={class:"flex mb-1"},Is=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[e("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),$s=e("span",{class:"break-normal"}," While auto billing, this message will be sent as an SMS to the subscriber if they have sufficient funds to complete the transaction ",-1),As=e("div",{class:"mt-4"},[e("table",{class:"w-full divide-y divide-gray-200"},[e("thead",null,[e("tr",{class:"font-bold"},[e("td",null,"Variable"),e("td",null,"Meaning")])]),e("tbody",{class:"divide-y divide-gray-200"},[e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionId }}"}),e("td",null,"The Subscription ID")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ nextBillableDate }}"}),e("td",null,"The next billable date")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionEndDate }}"}),e("td",null,"The Subscription end date")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionStartDate }}"}),e("td",null,"The Subscription start date")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanName }}"}),e("td",null,"The Subscription Plan name")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanPrice }}"}),e("td",null,"The Subscription Plan price")])])])],-1),Us={class:"mt-4"},Ns=e("strong",null,"Example:",-1),zs=["innerHTML"],Ys={class:"mb-4"},Os={class:"flex items-center mb-4"},Ws=e("span",{class:"font-medium text-sm text-gray-700 whitespace-nowrap mr-2"},"Next Auto Billing Reminder:",-1),Rs={class:"w-full"},Js={class:"mb-4"},qs={class:"flex mb-1"},Fs=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[e("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),Gs=e("span",{class:"break-normal"}," Before billing, this message will be sent as an SMS to the subscriber to notify them several hours before auto billing is attempted. This grants the subscriber the opportunity to unsubscribe if necessary ",-1),Ks={class:"mt-4"},Qs=e("table",{class:"w-full divide-y divide-gray-200"},[e("thead",null,[e("tr",{class:"font-bold"},[e("td",null,"Variable"),e("td",null,"Meaning")])]),e("tbody",{class:"divide-y divide-gray-200"},[e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ nextBillableDate }}"}),e("td",null,"The next billable date")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanName }}"}),e("td",null,"The Subscription Plan name")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanPrice }}"}),e("td",null,"The Subscription Plan price")])])],-1),Xs={class:"mt-4"},Zs=e("strong",null,"Example:",-1),et=["innerHTML"],st={class:"mb-4"},tt={class:"flex mb-1"},lt=e("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-5 w-5 ml-2",fill:"none",viewBox:"0 0 24 24",strokeWidth:"{1.5}",stroke:"currentColor"},[e("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"})],-1),nt=e("span",{class:"break-normal"}," After attempting auto billing for the last time with no avail, this message will be sent to the subscriber to notify them that auto billing has been disabled for this subscription plan. This means that auto billing on this subscription plan will no longer be attempted moving forward, therefore the subscriber must internally subscribe for this subscription plan ",-1),ot={class:"mt-4"},it=e("table",{class:"w-full divide-y divide-gray-200"},[e("thead",null,[e("tr",{class:"font-bold"},[e("td",null,"Variable"),e("td",null,"Meaning")])]),e("tbody",{class:"divide-y divide-gray-200"},[e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanName }}"}),e("td",null,"The Subscription Plan name")]),e("tr",null,[e("td",{class:"font-semibold text-green-500",innerHTML:"{{ subscriptionPlanPrice }}"}),e("td",null,"The Subscription Plan price")])])],-1),at={class:"mt-4"},rt=e("strong",null,"Example:",-1),ut=["innerHTML"];function dt(s,t,x,w,y,ct){const L=b("jet-secondary-button"),g=F,B=q,P=b("jet-button"),d=b("jet-label"),S=b("jet-input"),u=b("jet-input-error"),k=W,T=b("jet-number-input"),D=b("jet-select-input"),h=J,_=b("jet-textarea"),j=N,H=z,E=R,C=Y,I=O,$=b("jet-danger-button"),A=b("jet-dialog-modal");return o(),a("div",null,[s.showHeader?(o(),a("div",me,[e("div",pe,[e("div",be,c(s.parentSubscriptionPlan?s.parentSubscriptionPlan.name:"Subscription Plans"),1),s.parentSubscriptionPlan?(o(),a("div",he,[l(L,{onClick:t[0]||(t[0]=n=>s.goBackToPreviousPage()),class:"py-1 mb-4"},{default:i(()=>[fe,_e]),_:1}),l(B,{separator:">",class:"mb-4"},{default:i(()=>[l(g,{onClick:t[1]||(t[1]=n=>s.nagivateToSubscriptionPlan())},{default:i(()=>[ge]),_:1}),(o(!0),a(p,null,v(s.breadcrumbs,n=>(o(),f(g,{key:n.id,onClick:U=>s.nagivateToSubscriptionPlan(n)},{default:i(()=>[e("span",ve,c(n.name),1)]),_:2},1032,["onClick"]))),128))]),_:1})])):m("",!0),e("div",we,[ye,s.parentSubscriptionPlan?(o(),a("span",Pe,c(s.route("api.show.subscription.plan",{project:s.route().params.project,subscription_plan:s.parentSubscriptionPlan.id,type:"children"})),1)):(o(),a("span",Se,c(s.route("api.show.subscription.plans",{project:s.route().params.project})),1))])]),s.$inertia.page.props.projectPermissions.includes("Manage subscription plans")?(o(),a("div",ke,[l(P,{onClick:t[2]||(t[2]=n=>s.openModal())},{default:i(()=>[r("Add Subscription Plan")]),_:1})])):m("",!0)])):m("",!0),e("div",null,[s.showSuccessMessage?(o(),a("div",Te,[s.wantsToUpdate?(o(),a("strong",Me,"Subscription plan updated successfully")):s.wantsToDelete?(o(),a("strong",Ve,"Subscription plan deleted successfully")):(o(),a("strong",xe,"Subscription plan created successfully")),e("span",{onClick:t[3]||(t[3]=n=>s.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},Be)])):m("",!0),s.showErrorMessage?(o(),a("div",De,[s.wantsToUpdate?(o(),a("strong",je,"Subscription plan update failed")):s.wantsToDelete?(o(),a("strong",He,"Subscription plan delete failed")):(o(),a("strong",Ee,"Subscription plan creation failed")),e("span",{onClick:t[4]||(t[4]=n=>s.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},Ie)])):m("",!0),l(A,{show:s.showModal,closeable:!1},{title:i(()=>[s.wantsToUpdate?(o(),a(p,{key:0},[r("Update Subscription Plan")],64)):s.wantsToDelete?(o(),a(p,{key:1},[r("Delete Subscription Plan")],64)):(o(),a(p,{key:2},[r("Add Subscription Plan")],64))]),content:i(()=>[s.wantsToDelete?(o(),a(p,{key:0},[$e,e("p",Ae,c(s.subscriptionPlan.name),1)],64)):(o(),a(p,{key:1},[e("span",Ue,[s.parentSubscriptionPlan?(o(),a("span",Ne,[r(" You are "+c(s.wantsToUpdate?"updating":"adding")+" a subscription plan for ",1),e("span",ze,c(s.parentSubscriptionPlan.name),1)])):m("",!0),s.parentSubscriptionPlan?(o(),a("div",Ye,[l(B,{separator:">"},{default:i(()=>[l(g,null,{default:i(()=>[Oe]),_:1}),(o(!0),a(p,null,v(s.breadcrumbs,n=>(o(),f(g,{key:n.id},{default:i(()=>[e("span",We,c(n.name),1)]),_:2},1024))),128))]),_:1})])):m("",!0)]),e("div",Re,[l(d,{for:"name",value:"Name"}),l(S,{id:"name",type:"text",class:"w-full mt-1 block",modelValue:s.form.name,"onUpdate:modelValue":t[5]||(t[5]=n=>s.form.name=n),placeholder:"Daily @ P95.00"},null,8,["modelValue"]),l(u,{message:s.form.errors.name,class:"mt-2"},null,8,["message"])]),e("div",Je,[qe,l(k,{modelValue:s.form.active,"onUpdate:modelValue":t[6]||(t[6]=n=>s.form.active=n),class:"mx-2"},null,8,["modelValue"]),e("span",Fe,"— "+c(s.form.active?"Turn off to disable this subscription plan":"Turn on to enable this subscription plan"),1)]),e("div",Ge,[Ke,l(k,{modelValue:s.form.is_folder,"onUpdate:modelValue":t[7]||(t[7]=n=>s.form.is_folder=n),class:"mx-2"},null,8,["modelValue"]),e("span",Qe,"— "+c(s.form.is_folder?"Turn off to make this a subscription plan":"Turn on to make this a folder"),1)]),s.form.is_folder==!1?(o(),a(p,{key:0},[e("div",Xe,[e("div",Ze,[l(d,{for:"duration",value:"Duration"}),l(T,{id:"duration",class:"w-full mt-1 block",modelValue:s.form.duration,"onUpdate:modelValue":t[8]||(t[8]=n=>s.form.duration=n),modelModifiers:{string:!0},placeholder:"1"},null,8,["modelValue"]),l(u,{message:s.form.errors.duration,class:"mt-2"},null,8,["message"])]),e("div",es,[l(D,{placeholder:"Select frequency",options:s.frequencyOptions,modelValue:s.form.frequency,"onUpdate:modelValue":t[9]||(t[9]=n=>s.form.frequency=n),class:"mt-6"},null,8,["options","modelValue"]),l(u,{message:s.form.errors.frequency,class:"mt-2"},null,8,["message"])])]),e("div",ss,[l(d,{for:"price",value:"Price",class:"mb-1"}),l(T,{id:"price",class:"w-full mt-1 block",modelValue:s.form.price,"onUpdate:modelValue":t[10]||(t[10]=n=>s.form.price=n),placeholder:"95.00"},null,8,["modelValue"]),l(u,{message:s.form.errors.price,class:"mt-2"},null,8,["message"])]),e("div",ts,[e("div",ls,[l(d,{for:"sub-pl-description",value:"Description"}),l(h,{width:300},{reference:i(()=>[ns]),default:i(()=>[os]),_:1})]),l(_,{id:"sub-pl-description",class:"w-full mt-1 block",modelValue:s.form.description,"onUpdate:modelValue":t[11]||(t[11]=n=>s.form.description=n)},null,8,["modelValue"]),l(u,{message:s.form.errors.description,class:"mt-2"},null,8,["message"])]),e("div",is,[as,(o(!0),a(p,null,v(s.form.tags,n=>(o(),f(j,{key:n,class:"mx-1",closable:"","disable-transitions":!1,onClose:U=>s.handleRemoveTag(n)},{default:i(()=>[r(c(n),1)]),_:2},1032,["onClose"]))),128)),s.showAddTagInput?(o(),a("span",rs,[l(H,{ref:"addTagInputRef",modelValue:s.addTagInput,"onUpdate:modelValue":t[12]||(t[12]=n=>s.addTagInput=n),size:"small",onKeyup:X(s.handleAddTag,["enter"]),onBlur:s.handleAddTag},null,8,["modelValue","onKeyup","onBlur"])])):(o(),f(E,{key:1,class:"button-new-tag ml-1",size:"small",onClick:s.showInput},{default:i(()=>[r(" + New Tag ")]),_:1},8,["onClick"]))]),e("div",us,[e("div",ds,[l(d,{for:"sub-pl-c-message",value:"Insufficient Funds Message"}),l(h,{width:400},{reference:i(()=>[cs]),default:i(()=>[ms,ps,bs]),_:1})]),l(_,{id:"sub-pl-insufficient-funds-message",class:"w-full mt-1 block",modelValue:s.form.insufficient_funds_message,"onUpdate:modelValue":t[13]||(t[13]=n=>s.form.insufficient_funds_message=n)},null,8,["modelValue"]),l(u,{message:s.form.errors.insufficient_funds_message,class:"mt-2"},null,8,["message"])]),e("div",hs,[e("div",fs,[l(d,{for:"sub-pl-successful-payment-sms-message",value:"Successful Payment SMS Message"}),l(h,{width:400},{reference:i(()=>[_s]),default:i(()=>[gs,vs,e("p",ws,[ys,r(),e("span",{innerHTML:"Your payment for {{ subscriptionPlanName }} priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *xxx# to unsubscribe."},null,8,Ps)])]),_:1})]),l(_,{id:"sub-pl-successful-payment-sms-message",class:"w-full mt-1 block",modelValue:s.form.successful_payment_sms_message,"onUpdate:modelValue":t[14]||(t[14]=n=>s.form.successful_payment_sms_message=n)},null,8,["modelValue"]),l(u,{message:s.form.errors.successful_payment_sms_message,class:"mt-2"},null,8,["message"])]),e("div",Ss,[e("div",ks,[l(d,{for:"billing_product_id",value:"Billing Product ID (AAS)"}),l(h,{width:400},{reference:i(()=>[Ts]),default:i(()=>[Ms]),_:1})]),l(S,{id:"billing_product_id",type:"text",class:"w-full mt-1 block",modelValue:s.form.billing_product_id,"onUpdate:modelValue":t[15]||(t[15]=n=>s.form.billing_product_id=n),placeholder:"Product 123"},null,8,["modelValue"]),l(u,{message:s.form.errors.billing_product_id,class:"mt-2"},null,8,["message"])]),e("div",Vs,[e("div",xs,[l(d,{for:"billing_purchase_category_code",value:"Billing Purchase Category Code (AAS)"}),l(h,{width:400},{reference:i(()=>[Ls]),default:i(()=>[Bs]),_:1})]),l(S,{id:"billing_purchase_category_code",type:"text",class:"w-full mt-1 block",modelValue:s.form.billing_purchase_category_code,"onUpdate:modelValue":t[16]||(t[16]=n=>s.form.billing_purchase_category_code=n),placeholder:"Product 123"},null,8,["modelValue"]),l(u,{message:s.form.errors.billing_purchase_category_code,class:"mt-2"},null,8,["message"])]),e("div",Ds,[js,l(k,{modelValue:s.form.can_auto_bill,"onUpdate:modelValue":t[17]||(t[17]=n=>s.form.can_auto_bill=n),class:"mx-2"},null,8,["modelValue"]),e("span",Hs,"— "+c(s.form.can_auto_bill?"Turn off to disable auto billing on this subscription plan":"Turn on to enable auto billing on this subscription plan"),1)]),s.form.can_auto_bill?(o(),a(p,{key:0},[e("div",Es,[e("div",Cs,[l(d,{for:"sub-pl-successful-auto-payment-sms-message",value:"Successful Auto Billing Payment SMS Message"}),l(h,{width:400},{reference:i(()=>[Is]),default:i(()=>[$s,As,e("p",Us,[Ns,r(),e("span",{innerHTML:"Your auto payment for {{ subscriptionPlanName }} priced {{ subscriptionPlanPrice }} was completed successfully. Valid till {{ subscriptionEndDate }}. You will be automatically billed on {{ nextBillableDate }}. Ref: #{{ subscriptionId }} . Dial *xxx# to unsubscribe."},null,8,zs)])]),_:1})]),l(_,{id:"sub-pl-successful-auto-payment-sms-message",class:"w-full mt-1 block",modelValue:s.form.successful_auto_billing_payment_sms_message,"onUpdate:modelValue":t[18]||(t[18]=n=>s.form.successful_auto_billing_payment_sms_message=n)},null,8,["modelValue"]),l(u,{message:s.form.errors.successful_auto_billing_payment_sms_message,class:"mt-2"},null,8,["message"])]),e("div",Ys,[l(d,{for:"max_auto_billing_attempts",value:"Maximum Auto Billing Attempts"}),l(T,{id:"max_auto_billing_attempts",class:"w-full mt-1",modelValue:s.form.max_auto_billing_attempts,"onUpdate:modelValue":t[19]||(t[19]=n=>s.form.max_auto_billing_attempts=n),modelModifiers:{string:!0},min:"1",max:"10",placeholder:"3"},null,8,["modelValue"]),l(u,{message:s.form.errors.max_auto_billing_attempts,class:"mt-2"},null,8,["message"])]),e("div",Os,[Ws,e("div",Rs,[l(I,{modelValue:s.form.auto_billing_reminder_ids,"onUpdate:modelValue":t[20]||(t[20]=n=>s.form.auto_billing_reminder_ids=n),multiple:"",placeholder:"Set Reminders",class:"w-full"},{default:i(()=>[(o(!0),a(p,null,v(s.autoBillingReminderOptions,n=>(o(),f(C,{key:n.value,value:n.value,label:n.name},null,8,["value","label"]))),128))]),_:1},8,["modelValue"]),l(u,{message:s.form.errors.auto_billing_reminder_ids,class:"mt-2"},null,8,["message"])])]),e("div",Js,[e("div",qs,[l(d,{for:"sub-pl-next-auto-billing-reminder-sms-message",value:"Next Auto Billing Reminder SMS Message"}),l(h,{width:400},{reference:i(()=>[Fs]),default:i(()=>[Gs,e("div",Ks,[Qs,e("p",Xs,[Zs,r(),e("span",{innerHTML:"You will be automatically billed for {{ subscriptionPlanName }} priced {{ subscriptionPlanPrice }} on {{ nextBillableDate }}. Dial *xxx# to unsubscribe."},null,8,et)])])]),_:1})]),l(_,{id:"sub-pl-next-auto-billing-reminder-sms-message",class:"w-full mt-1 block",modelValue:s.form.next_auto_billing_reminder_sms_message,"onUpdate:modelValue":t[21]||(t[21]=n=>s.form.next_auto_billing_reminder_sms_message=n)},null,8,["modelValue"]),l(u,{message:s.form.errors.next_auto_billing_reminder_sms_message,class:"mt-2"},null,8,["message"])]),e("div",st,[e("div",tt,[l(d,{for:"sub-pl-next-auto-billing-reminder-sms-message",value:"Auto Billing Disabled SMS Message"}),l(h,{width:400},{reference:i(()=>[lt]),default:i(()=>[nt,e("div",ot,[it,e("p",at,[rt,r(),e("span",{innerHTML:"You have been successfully unsubscribed from {{ subscriptionPlanName }} priced {{ subscriptionPlanPrice }}. Dial *217# to subscribe."},null,8,ut)])])]),_:1})]),l(_,{id:"sub-pl-next-auto-billing-reminder-sms-message",class:"w-full mt-1 block",modelValue:s.form.auto_billing_disabled_sms_message,"onUpdate:modelValue":t[22]||(t[22]=n=>s.form.auto_billing_disabled_sms_message=n)},null,8,["modelValue"]),l(u,{message:s.form.errors.auto_billing_disabled_sms_message,class:"mt-2"},null,8,["message"])])],64)):m("",!0)],64)):m("",!0)],64))]),footer:i(()=>[l(L,{onClick:t[23]||(t[23]=n=>s.closeModal()),class:"mr-2"},{default:i(()=>[r(" Cancel ")]),_:1}),s.hasSubscriptionPlan?m("",!0):(o(),f(P,{key:0,onClick:t[24]||(t[24]=M(n=>s.create(),["prevent"])),class:V({"opacity-25":s.form.processing}),disabled:s.form.processing},{default:i(()=>[r(" Create ")]),_:1},8,["class","disabled"])),s.wantsToUpdate?(o(),f(P,{key:1,onClick:t[25]||(t[25]=M(n=>s.update(),["prevent"])),class:V({"opacity-25":s.form.processing}),disabled:s.form.processing},{default:i(()=>[r(" Update ")]),_:1},8,["class","disabled"])):m("",!0),s.wantsToDelete?(o(),f($,{key:2,onClick:t[26]||(t[26]=M(n=>s.destroy(),["prevent"])),class:V({"opacity-25":s.form.processing}),disabled:s.form.processing},{default:i(()=>[r(" Delete ")]),_:1},8,["class","disabled"])):m("",!0)]),_:1},8,["show"])])])}const Et=re(ce,[["render",dt]]);export{Et as default};
