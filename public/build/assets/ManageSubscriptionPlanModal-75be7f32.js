import"./base-3ecad56a.js";import{a as V,E as P,b as $}from"./el-button-5cc72afb.js";import{s as C,q as E,e as n,a as t,t as g,b as r,w as l,f as u,x as d,o,g as i,F as p,h as I,c,a2 as D,i as y,n as w}from"./app-8047a45c.js";import{_ as B,a as A}from"./TextInput-bc6cbde4.js";import{_ as J}from"./InputLabel-22fd5c5c.js";import{J as O}from"./Textarea-93bc0975.js";import{_ as U}from"./PrimaryButton-8efc2419.js";import{J as q}from"./SelectInput-b01ea32e.js";import{a as L}from"./DialogModal-0a229599.js";import{_ as N}from"./DangerButton-569cea3a.js";import{_ as z}from"./ActionMessage-cac46d31.js";import{_ as R}from"./SecondaryButton-bd238173.js";import{_ as H}from"./_plugin-vue_export-helper-c27b6911.js";import"./isNil-737ab91a.js";import"./icon-be9b5ac7.js";import"./index-d2e11550.js";import"./use-form-item-1833bed3.js";import"./constants-7259abfc.js";const K=C({components:{JetLabel:J,JetInput:B,JetTextarea:O,JetButton:U,JetInputError:A,JetSelectInput:q,JetDialogModal:L,JetSecondaryButton:R,JetActionMessage:z,JetDangerButton:N},props:{action:{type:String,default:"update"},modelValue:{type:Boolean,default:!1},showAddbutton:{type:Boolean,default:!1},subscriptionPlan:{type:Object,default:null},show:{type:Boolean,default:!1}},data(){return{form:null,addTagInput:"",showAddTagInput:!1,showModal:this.modelValue,showSuccessMessage:!1,showErrorMessage:!1}},watch:{showModal:{handler:function(e,s){e!=this.modelValue&&this.$emit("update:modelValue",e)}},modelValue:{handler:function(e,s){e!=this.showModal&&(this.showModal=e,this.reset())}}},computed:{hasSubscriptionPlan(){return this.subscriptionPlan!=null},wantsToUpdate(){return!!(this.hasSubscriptionPlan&&this.action=="update")},wantsToDelete(){return!!(this.hasSubscriptionPlan&&this.action=="delete")},frequencyOptions(){return[{name:this.form.duration=="1"?"Second":"Seconds",value:"Seconds"},{name:this.form.duration=="1"?"Minute":"Minutes",value:"Minutes"},{name:this.form.duration=="1"?"Hour":"Hours",value:"Hours"},{name:this.form.duration=="1"?"Day":"Days",value:"Days"},{name:this.form.duration=="1"?"Week":"Weeks",value:"Weeks"},{name:this.form.duration=="1"?"Month":"Months",value:"Months"},{name:this.form.duration=="1"?"Year":"Years",value:"Years"}]}},methods:{openModal(){this.showModal=!0},closeModal(){this.showModal=!1},handleAddTag(){if(this.addTagInput){const e=this.addTagInput.trim();this.form.categories.includes(e)||this.form.categories.push(e)}this.showAddTagInput=!1,this.addTagInput=""},handleRemoveTag(e){this.form.categories.splice(this.form.categories.indexOf(e),1)},showInput(){this.showAddTagInput=!0,E(()=>{this.$refs.addTagInputRef.focus()})},create(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess()},onError:s=>{this.handleOnError()}};this.form.post(route("create.subscription.plan",{project:route().params.project}),e)},update(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess()},onError:s=>{this.handleOnError()}};this.form.put(route("update.subscription.plan",{project:route().params.project,subscription_plan:this.subscriptionPlan.id}),e)},destroy(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess()},onError:s=>{this.handleOnError()}};this.form.delete(route("delete.subscription.plan",{project:route().params.project,subscription_plan:this.subscriptionPlan.id}),e)},handleOnSuccess(){this.reset(),this.closeModal(),this.showSuccessMessage=!0,setTimeout(()=>{this.showSuccessMessage=!1},3e3)},handleOnError(){this.showErrorMessage=!0,setTimeout(()=>{this.showErrorMessage=!1},3e3)},reset(){this.form=this.$inertia.form({name:this.hasSubscriptionPlan?this.subscriptionPlan.name:null,price:this.hasSubscriptionPlan?this.subscriptionPlan.price:null,frequency:this.hasSubscriptionPlan?this.subscriptionPlan.frequency:"Days",categories:this.hasSubscriptionPlan?this.subscriptionPlan.categories??[]:[],duration:this.hasSubscriptionPlan?this.subscriptionPlan.duration.toString():"1"})}},created(){this.reset()}}),W={key:0,class:"grid grid-cols-2 gap-4"},Y={class:"bg-gray-50 pt-3 pl-6 border-b rounded-t"},F={class:"text-sm text-gray-500 my-2"},G=t("span",{class:"font-bold mr-2"},"GET:",-1),Q={class:"text-green-500 font-semibold"},X=t("div",{class:"clear-both"},null,-1),Z={class:"clear-both"},x={key:0,class:"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6",role:"alert"},ee={key:0,class:"font-bold"},se={key:1,class:"font-bold"},te={key:2,class:"font-bold"},oe=t("svg",{class:"fill-current h-6 w-6 text-green-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[t("title",null,"Close"),t("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),ae=[oe],ne={key:1,class:"bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6",role:"alert"},re={key:0,class:"font-bold"},le={key:1,class:"font-bold"},ie={key:2,class:"font-bold"},de=t("svg",{class:"fill-current h-6 w-6 text-red-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[t("title",null,"Close"),t("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),ue=[de],pe=t("span",{class:"block mt-6 mb-6"},"Are you sure you want to delete this subscription plan?",-1),ce={class:"text-sm text-gray-500"},me={class:"mb-4"},he={class:"grid gap-4 grid-cols-2"},fe={class:"mb-4"},be={class:"mb-4"},ge={class:"flex bg-slate-50 rounded-lg p-4 mb-4"},ye=t("span",{class:"font-bold mr-2"},"Category",-1),we={key:0,class:"w-20"},_e={class:"mb-4"};function ve(e,s,Se,ke,Me,Te){const h=d("jet-button"),f=d("jet-label"),b=d("jet-input"),m=d("jet-input-error"),_=d("jet-select-input"),v=V,S=P,k=$,M=d("jet-secondary-button"),T=d("jet-danger-button"),j=d("jet-dialog-modal");return o(),n("div",null,[e.$inertia.page.props.projectPermissions.includes("Manage subscription plans")&&e.showAddbutton?(o(),n("div",W,[t("div",Y,[t("div",F,[G,t("span",Q,g(e.route("api.show.subscription.plans",{project:e.route().params.project})),1)])]),t("div",null,[r(h,{onClick:s[0]||(s[0]=a=>e.openModal()),class:"float-right w-fit"},{default:l(()=>[i(" Add Subscription Plan ")]),_:1}),X])])):u("",!0),t("div",Z,[e.showSuccessMessage?(o(),n("div",x,[e.wantsToUpdate?(o(),n("strong",ee,"Subscription plan updated successfully")):e.wantsToDelete?(o(),n("strong",se,"Subscription plan deleted successfully")):(o(),n("strong",te,"Subscription plan created successfully")),t("span",{onClick:s[1]||(s[1]=a=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},ae)])):u("",!0),e.showErrorMessage?(o(),n("div",ne,[e.wantsToUpdate?(o(),n("strong",re,"Subscription plan update failed")):e.wantsToDelete?(o(),n("strong",le,"Subscription plan delete failed")):(o(),n("strong",ie,"Subscription plan creation failed")),t("span",{onClick:s[2]||(s[2]=a=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},ue)])):u("",!0),r(j,{show:e.showModal,closeable:!1},{title:l(()=>[e.wantsToUpdate?(o(),n(p,{key:0},[i("Update Subscription plan")],64)):e.wantsToDelete?(o(),n(p,{key:1},[i("Delete Subscription plan")],64)):(o(),n(p,{key:2},[i("Add Subscription plan")],64))]),content:l(()=>[e.wantsToDelete?(o(),n(p,{key:0},[pe,t("p",ce,g(e.subscriptionPlan.name),1)],64)):(o(),n(p,{key:1},[t("div",me,[r(f,{for:"name",value:"Name"}),r(b,{id:"name",type:"text",class:"w-full mt-1 block ",modelValue:e.form.name,"onUpdate:modelValue":s[3]||(s[3]=a=>e.form.name=a),placeholder:"Daily @ P95.00"},null,8,["modelValue"]),r(m,{message:e.form.errors.name,class:"mt-2"},null,8,["message"])]),t("div",he,[t("div",fe,[r(f,{for:"duration",value:"Duration"}),r(b,{id:"duration",type:"text",class:"w-full mt-1 block ",modelValue:e.form.duration,"onUpdate:modelValue":s[4]||(s[4]=a=>e.form.duration=a),modelModifiers:{string:!0},placeholder:"1"},null,8,["modelValue"]),r(m,{message:e.form.errors.duration,class:"mt-2"},null,8,["message"])]),t("div",be,[r(_,{placeholder:"Select frequency",options:e.frequencyOptions,modelValue:e.form.frequency,"onUpdate:modelValue":s[5]||(s[5]=a=>e.form.frequency=a),class:"mt-6"},null,8,["options","modelValue"]),r(m,{message:e.form.errors.frequency,class:"mt-2"},null,8,["message"])])]),t("div",ge,[ye,(o(!0),n(p,null,I(e.form.categories,a=>(o(),c(v,{key:a,class:"mx-1",closable:"","disable-transitions":!1,onClose:je=>e.handleRemoveTag(a)},{default:l(()=>[i(g(a),1)]),_:2},1032,["onClose"]))),128)),e.showAddTagInput?(o(),n("span",we,[r(S,{ref:"addTagInputRef",modelValue:e.addTagInput,"onUpdate:modelValue":s[6]||(s[6]=a=>e.addTagInput=a),size:"small",onKeyup:D(e.handleAddTag,["enter"]),onBlur:e.handleAddTag},null,8,["modelValue","onKeyup","onBlur"])])):(o(),c(k,{key:1,class:"button-new-tag ml-1",size:"small",onClick:e.showInput},{default:l(()=>[i(" + New Tag ")]),_:1},8,["onClick"]))]),t("div",_e,[r(f,{for:"price",value:"Price",class:"mb-1"}),r(b,{id:"price",type:"text",class:"w-full mt-1 block ",modelValue:e.form.price,"onUpdate:modelValue":s[7]||(s[7]=a=>e.form.price=a),placeholder:"95.00"},null,8,["modelValue"]),r(m,{message:e.form.errors.price,class:"mt-2"},null,8,["message"])])],64))]),footer:l(()=>[r(M,{onClick:s[8]||(s[8]=a=>e.closeModal()),class:"mr-2"},{default:l(()=>[i(" Cancel ")]),_:1}),e.hasSubscriptionPlan?u("",!0):(o(),c(h,{key:0,onClick:s[9]||(s[9]=y(a=>e.create(),["prevent"])),class:w({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:l(()=>[i(" Create ")]),_:1},8,["class","disabled"])),e.wantsToUpdate?(o(),c(h,{key:1,onClick:s[10]||(s[10]=y(a=>e.update(),["prevent"])),class:w({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:l(()=>[i(" Update ")]),_:1},8,["class","disabled"])):u("",!0),e.wantsToDelete?(o(),c(T,{key:2,onClick:s[11]||(s[11]=y(a=>e.destroy(),["prevent"])),class:w({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:l(()=>[i(" Delete ")]),_:1},8,["class","disabled"])):u("",!0)]),_:1},8,["show"])])])}const Ke=H(K,[["render",ve]]);export{Ke as default};
