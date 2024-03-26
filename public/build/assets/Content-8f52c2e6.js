import"./base-130e034c.js";import{E as v}from"./el-divider-dc8a65aa.js";import{E as y}from"./el-switch-88092f77.js";import{h as V}from"./moment-fbc5633a.js";import{s as C,e as d,a as s,f as c,b as o,t as h,w as u,l as p,m as _,i as j,n as S,x as r,o as m,g as k}from"./app-bb45fb6f.js";import{a as x,_ as B}from"./TextInput-431a2b44.js";import{_ as M}from"./InputLabel-361014b1.js";import{J as U}from"./Textarea-9be55ed5.js";import{_ as E}from"./PrimaryButton-40033d3b.js";import{_ as $}from"./_plugin-vue_export-helper-c27b6911.js";import"./index-58643621.js";import"./icon-45c0f559.js";import"./constants-339607f3.js";const A=C({components:{JetInputError:x,JetTextarea:U,JetButton:E,JetLabel:M,JetInput:B},props:{project:Object},data(){return{showAutoBillingClientSecret:!1,showAutoBillingClientID:!1,showClientCredentials:!1,showSuccessMessage:!1,showErrorMessage:!1,moment:V,form:null}},methods:{update(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:t=>{this.handleOnSuccess()},onError:t=>{this.handleOnError()}};this.form.put(route("update.project",{project:this.project.id}),e)},handleOnSuccess(){this.reset(),this.showSuccessMessage=!0,setTimeout(()=>{this.showSuccessMessage=!1},3e3)},handleOnError(){this.showErrorMessage=!0,setTimeout(()=>{this.showErrorMessage=!1},3e3)},reset(){this.form=this.$inertia.form({name:this.project.name,settings:this.project.settings,description:this.project.description,can_auto_bill:this.project.can_auto_bill,can_send_messages:this.project.can_send_messages,about_url:this.hasProject?this.project.about_url:null})}},created(){this.reset()}}),D={class:"mt-10 sm:mt-0"},P=s("div",{class:"grid grid-cols-8 gap-4"},[s("div",{class:"col-start-3 col-span-4"},[s("h1",{class:"text-2xl font-semibold leading-6 text-gray-500"},"Project Settings")])],-1),T={class:"grid grid-cols-8 gap-4 mt-4 mb-4"},I={class:"col-start-3 col-span-4"},N={key:0,class:"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4",role:"alert"},J=s("strong",{class:"font-bold"},"Project updated successfully",-1),L=s("svg",{class:"fill-current h-6 w-6 text-green-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[s("title",null,"Close"),s("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),O=[L],z={key:1,class:"bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4",role:"alert"},R=s("strong",{class:"font-bold"},"Project update failed",-1),X=s("svg",{class:"fill-current h-6 w-6 text-red-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[s("title",null,"Close"),s("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),Y=[X],Z={class:"grid grid-cols-8 gap-4"},q={class:"col-start-3 col-span-4"},F={class:"shadow overflow-hidden sm:rounded-md"},G={class:"px-4 py-5 bg-white sm:p-6"},H={class:"grid grid-cols-12 gap-6"},K={class:"col-span-12"},Q={class:"col-span-12"},W={class:"col-span-12"},ss={class:"col-span-12"},es=s("span",{class:"text-sm text-gray-500"},"Send messages",-1),ts={class:"text-sm text-gray-400"},os={class:"mt-10 mb-10"},ls=s("span",{class:"font-semibold"},"Sms Account Settings",-1),ns={class:"grid grid-cols-6 gap-6"},is={class:"col-span-6 sm:col-span-12"},as={class:"col-span-6 sm:col-span-12"},rs={class:"col-span-6 sm:col-span-12"},ds={class:"flex items-center mt-2"},ms=s("label",{for:"show_client_credentials",class:"ml-3 block text-sm font-medium text-gray-700"},"Show credentials",-1),cs={class:"mt-10 mb-10"},us=s("span",{class:"font-semibold"},"Auto Billing Settings",-1),ps={class:"grid grid-cols-6 gap-6"},_s={class:"col-span-12"},gs=s("span",{class:"text-sm text-gray-500"},"Auto Bill",-1),fs={class:"text-sm text-gray-400"},hs={class:"col-span-6 sm:col-span-12"},bs={class:"flex items-center mt-2"},ws=s("label",{for:"show_auto_billing_client_secret",class:"ml-3 block text-sm font-medium text-gray-700"},"Show credentials",-1),vs={class:"col-span-6 sm:col-span-12"},ys={class:"flex items-center mt-2"},Vs=s("label",{for:"show_auto_billing_client_id",class:"ml-3 block text-sm font-medium text-gray-700"},"Show credentials",-1),Cs={key:0,class:"px-4 py-3 bg-gray-50 text-right sm:px-6"};function js(e,t,Ss,ks,xs,Bs){const n=r("jet-label"),a=r("jet-input"),i=r("jet-input-error"),b=r("jet-textarea"),g=y,f=v,w=r("jet-button");return m(),d("div",D,[P,s("div",T,[s("div",I,[e.showSuccessMessage?(m(),d("div",N,[J,s("span",{onClick:t[0]||(t[0]=l=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},O)])):c("",!0),e.showErrorMessage?(m(),d("div",z,[R,s("span",{onClick:t[1]||(t[1]=l=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},Y)])):c("",!0)])]),s("div",Z,[s("div",q,[s("div",F,[s("div",G,[s("div",H,[s("div",K,[o(n,{for:"name",value:"Project Name"}),o(a,{id:"name",type:"text",class:"w-full mt-1 block",modelValue:e.form.name,"onUpdate:modelValue":t[2]||(t[2]=l=>e.form.name=l)},null,8,["modelValue"]),o(i,{message:e.form.errors.name,class:"mt-2"},null,8,["message"])]),s("div",Q,[o(n,{for:"description",value:"Project Description"}),o(b,{id:"description",class:"w-full mt-1 block",modelValue:e.form.description,"onUpdate:modelValue":t[3]||(t[3]=l=>e.form.description=l)},null,8,["modelValue"]),o(i,{message:e.form.errors.description,class:"mt-2"},null,8,["message"])]),s("div",W,[o(n,{for:"about_url",value:"About URL"}),o(a,{id:"about_url",type:"text",class:"w-full mt-1 block",modelValue:e.form.about_url,"onUpdate:modelValue":t[4]||(t[4]=l=>e.form.about_url=l)},null,8,["modelValue"]),o(i,{message:e.form.errors.about_url,class:"mt-2"},null,8,["message"])]),s("div",ss,[s("span",null,[es,o(g,{modelValue:e.form.can_send_messages,"onUpdate:modelValue":t[5]||(t[5]=l=>e.form.can_send_messages=l),class:"mx-2"},null,8,["modelValue"]),s("span",ts,"— "+h(e.form.can_send_messages?"Turn off to stop sending messages":"Turn on to start sending messages"),1)])])]),s("div",os,[o(f,{"content-position":"left"},{default:u(()=>[ls]),_:1})]),s("div",ns,[s("div",is,[o(n,{for:"sms_sender_name",value:"Sender Name"}),o(a,{id:"sms_sender_name",type:"text",placeholder:"Company XYZ",maxlength:20,class:"w-full mt-1 block",modelValue:e.form.settings.sms_sender_name,"onUpdate:modelValue":t[6]||(t[6]=l=>e.form.settings.sms_sender_name=l)},null,8,["modelValue"]),o(i,{message:e.form.errors["settings.sms_sender_name"],class:"mt-2"},null,8,["message"])]),s("div",as,[o(n,{for:"sms_sender_number",value:"Sender Number"}),o(a,{id:"sms_sender_number",type:"text",placeholder:"26772012345",maxlength:11,class:"w-full mt-1 block",modelValue:e.form.settings.sms_sender_number,"onUpdate:modelValue":t[7]||(t[7]=l=>e.form.settings.sms_sender_number=l)},null,8,["modelValue"]),o(i,{message:e.form.errors["settings.sms_sender_number"],class:"mt-2"},null,8,["message"])]),s("div",rs,[o(n,{for:"sms_client_credentials",value:"Client Credentials"}),o(a,{id:"sms_client_credentials",type:e.showClientCredentials?"text":"password",placeholder:"*************************",class:"w-full mt-1 block",modelValue:e.form.settings.sms_client_credentials,"onUpdate:modelValue":t[8]||(t[8]=l=>e.form.settings.sms_client_credentials=l)},null,8,["type","modelValue"]),o(i,{message:e.form.errors["settings.sms_client_credentials"],class:"mt-2"},null,8,["message"]),s("div",ds,[p(s("input",{"onUpdate:modelValue":t[9]||(t[9]=l=>e.showClientCredentials=l),id:"show_client_credentials",name:"show_client_credentials",type:"checkbox",class:"focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"},null,512),[[_,e.showClientCredentials]]),ms])])]),s("div",cs,[o(f,{"content-position":"left"},{default:u(()=>[us]),_:1})]),s("div",ps,[s("div",_s,[s("span",null,[gs,o(g,{modelValue:e.form.can_auto_bill,"onUpdate:modelValue":t[10]||(t[10]=l=>e.form.can_auto_bill=l),class:"mx-2"},null,8,["modelValue"]),s("span",fs,"— "+h(e.form.can_auto_bill?"Turn off to stop auto billing on subscription plans":"Turn on to start auto billing on subscription plans"),1)])]),s("div",hs,[o(n,{for:"auto_billing_client_id",value:"Client ID"}),o(a,{id:"auto_billing_client_id",type:e.showAutoBillingClientSecret?"text":"password",placeholder:"*************************",class:"w-full mt-1 block",modelValue:e.form.settings.auto_billing_client_id,"onUpdate:modelValue":t[11]||(t[11]=l=>e.form.settings.auto_billing_client_id=l)},null,8,["type","modelValue"]),o(i,{message:e.form.errors["settings.auto_billing_client_id"],class:"mt-2"},null,8,["message"]),s("div",bs,[p(s("input",{"onUpdate:modelValue":t[12]||(t[12]=l=>e.showAutoBillingClientSecret=l),id:"show_auto_billing_client_secret",name:"show_auto_billing_client_secret",type:"checkbox",class:"focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"},null,512),[[_,e.showAutoBillingClientSecret]]),ws])]),s("div",vs,[o(n,{for:"auto_billing_client_secret",value:"Client Secret"}),o(a,{id:"auto_billing_client_secret",type:e.showAutoBillingClientID?"text":"password",placeholder:"*************************",class:"w-full mt-1 block",modelValue:e.form.settings.auto_billing_client_secret,"onUpdate:modelValue":t[13]||(t[13]=l=>e.form.settings.auto_billing_client_secret=l)},null,8,["type","modelValue"]),o(i,{message:e.form.errors["settings.auto_billing_client_secret"],class:"mt-2"},null,8,["message"]),s("div",ys,[p(s("input",{"onUpdate:modelValue":t[14]||(t[14]=l=>e.showAutoBillingClientID=l),id:"show_auto_billing_client_id",name:"show_auto_billing_client_id",type:"checkbox",class:"focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"},null,512),[[_,e.showAutoBillingClientID]]),Vs])])])]),e.$inertia.page.props.projectPermissions.includes("Manage project settings")?(m(),d("div",Cs,[o(w,{onClick:t[15]||(t[15]=j(l=>e.update(),["prevent"])),class:S({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:u(()=>[k(" Save Changes ")]),_:1},8,["class","disabled"])])):c("",!0)])])])])}const zs=$(A,[["render",js]]);export{zs as default};
