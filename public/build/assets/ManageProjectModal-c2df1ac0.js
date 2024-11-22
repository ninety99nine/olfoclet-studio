import"./base-56227e5d.js";import{E as $,a as E}from"./el-switch-e3bffaf9.js";import{E as I,a as O}from"./el-tag-8b56b1c1.js";import{E as J}from"./el-divider-8ecc0c72.js";import{q as R,s as p,o as n,e as a,c as h,w as i,f as _,a as s,b as l,p as F,v as L,g as u,F as g,t as f,l as j,m as k,h as V,bJ as N,i as C,n as S}from"./app-33020c92.js";import{_ as z,a as K}from"./TextInput-1f4ada43.js";import{_ as Z}from"./InputLabel-cb7cfcd9.js";import{J as q}from"./Textarea-fb103628.js";import{_ as H}from"./PrimaryButton-6b9f4282.js";import{J as W}from"./SelectInput-a557b92c.js";import{a as X}from"./DialogModal-61701a9e.js";import{_ as Y}from"./DangerButton-d84d9b81.js";import{_ as G}from"./ActionMessage-22581b6c.js";import{_ as Q}from"./SecondaryButton-cebaf51c.js";import{_ as x}from"./_plugin-vue_export-helper-c27b6911.js";import"./index-cea313c0.js";import"./icon-02db1ed7.js";import"./constants-ee6bc465.js";/* empty css            */const ee=R({components:{JetLabel:Z,JetInput:z,JetButton:H,JetTextarea:q,JetInputError:K,JetSelectInput:W,JetDialogModal:X,JetSecondaryButton:Q,JetActionMessage:G,JetDangerButton:Y},props:{action:{type:String,default:"update"},modelValue:{type:Boolean,default:!1},showAddbutton:{type:Boolean,default:!1},project:{type:Object,default:null},show:{type:Boolean,default:!1}},data(){return{form:null,addTagInput:"",showAddTagInput:!1,showModal:this.modelValue,showAutoBillingClientSecret:!1,showAutoBillingClientID:!1,showClientCredentials:!1,showSuccessMessage:!1,showErrorMessage:!1}},watch:{showModal:{handler:function(e,t){e!=this.modelValue&&this.$emit("update:modelValue",e)}},modelValue:{handler:function(e,t){e!=this.showModal&&(this.showModal=e,this.reset())}}},computed:{hasProject(){return this.project!=null},wantsToUpdate(){return!!(this.hasProject&&this.action=="update")},wantsToDelete(){return!!(this.hasProject&&this.action=="delete")}},methods:{handleSelectedPDF(e){this.form.pdf_path=null,this.form.pdf=e.target.files[0],this.pdfPreview=URL.createObjectURL(e.target.files[0])},openModal(){this.showModal=!0},closeModal(){this.showModal=!1},addCost(){this.form.costs.push({name:"Cost #"+this.form.costs.length,percentage:1})},removeCost(e){this.form.costs.splice(e,1)},handleAddTag(){if(this.addTagInput){const e=this.addTagInput.trim();this.form.billing_report_email_addresses.includes(e)||this.form.billing_report_email_addresses.push(e)}this.showAddTagInput=!1,this.addTagInput=""},handleRemoveTag(e){this.form.billing_report_email_addresses.splice(this.form.billing_report_email_addresses.indexOf(e),1)},showInput(){this.showAddTagInput=!0,F(()=>{this.$refs.addTagInputRef.focus()})},create(){var e={replace:!0,preserveState:!0,preserveScroll:!0,onSuccess:t=>{this.handleOnSuccess()},onError:t=>{this.handleOnError()}};this.form.transform(t=>(t.settings.sms_sender_name==""&&(t.settings.sms_sender_name=null),t.settings.sms_sender_number==""&&(t.settings.sms_sender_number=null),t.settings.sms_client_credentials==""&&(t.settings.sms_client_credentials=null),t.settings.auto_billing_client_id==""&&(t.settings.auto_billing_client_id=null),t.settings.auto_billing_client_secret==""&&(t.settings.auto_billing_client_secret=null),t)).post(route("create.project"),e)},update(){var e={replace:!0,preserveState:!0,preserveScroll:!0,onSuccess:t=>{this.handleOnSuccess()},onError:t=>{this.handleOnError()}};this.form.post(route("update.project",{project:this.project.id}),e)},destroy(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:t=>{this.handleOnSuccess()},onError:t=>{this.handleOnError()}};this.form.delete(route("delete.project",{project:this.project.id}),e)},handleOnSuccess(){this.reset(),this.closeModal(),this.showSuccessMessage=!0,setTimeout(()=>{this.showSuccessMessage=!1},3e3)},handleOnError(){this.showErrorMessage=!0,setTimeout(()=>{this.showErrorMessage=!1},3e3)},reset(){var e={pdf:null,name:this.hasProject?this.project.name:null,pdf_path:this.hasProject?this.project.pdf_path:null,website_url:this.hasProject?this.project.website_url:null,description:this.hasProject?this.project.description:null,can_auto_bill:this.hasProject?this.project.can_auto_bill:!1,can_send_messages:this.hasProject?this.project.can_send_messages:!1,settings:this.hasProject?this.project.settings:{sms_sender_name:"",sms_sender_number:"",sms_client_credentials:"",auto_billing_client_id:"",auto_billing_client_secret:""},costs:this.hasProject?this.project.costs:[{name:"USAF",percentage:1},{name:"BOCRA",percentage:3},{name:"VAT (14%)",percentage:14},{name:"Dealer Commission (Airtime)",percentage:13.5}],can_create_billing_reports:this.hasProject?this.project.can_create_billing_reports:!1,billing_report_email_addresses:this.hasProject?this.project.billing_report_email_addresses:[],our_share_percentage:this.hasProject?(this.project.our_share_percentage??{}).toString():"60",their_share_percentage:this.hasProject?(this.project.their_share_percentage??{}).toString():"40"};this.hasProject&&(e._method="put"),this.form=L(e)}},created(){this.reset()}}),se={class:"clear-both"},te={key:0,class:"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6",role:"alert"},oe={key:0,class:"font-bold"},le={key:1,class:"font-bold"},ne={key:2,class:"font-bold"},ae=s("svg",{class:"fill-current h-6 w-6 text-green-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[s("title",null,"Close"),s("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),re=[ae],ie={key:1,class:"bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6",role:"alert"},de={key:0,class:"font-bold"},me={key:1,class:"font-bold"},ue={key:2,class:"font-bold"},ce=s("svg",{class:"fill-current h-6 w-6 text-red-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[s("title",null,"Close"),s("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),pe=[ce],_e=s("span",{class:"block mt-6 mb-6"},"Are you sure you want to delete this project?",-1),ge={class:"text-sm text-gray-500"},he={class:"grid grid-cols-12 gap-6"},fe={class:"col-span-12"},be={class:"col-span-12"},we={class:"col-span-12"},ve={class:"col-span-12"},ye={class:"flex justify-between items-center"},je={key:0},ke={key:0},Ve=["src"],Ce={key:1},Se=["src"],Te={class:"col-span-12"},Pe=s("span",{class:"text-sm text-gray-500"},"Send messages",-1),Ue={class:"text-sm text-gray-400"},Be={class:"mt-10 mb-10"},Me=s("span",{class:"font-semibold"},"Sms Account Settings",-1),Ae={class:"grid grid-cols-6 gap-6"},De={class:"col-span-6 sm:col-span-12"},$e={class:"col-span-6 sm:col-span-12"},Ee={class:"col-span-6 sm:col-span-12"},Ie={class:"flex items-center mt-2"},Oe=s("label",{for:"show_client_credentials",class:"ml-3 block text-sm font-medium text-gray-700"},"Show credentials",-1),Je={class:"mt-10 mb-10"},Re=s("span",{class:"font-semibold"},"Auto Billing Settings",-1),Fe={class:"grid grid-cols-12 gap-6"},Le={class:"col-span-12"},Ne=s("span",{class:"text-sm text-gray-500"},"Auto Bill",-1),ze={class:"text-sm text-gray-400"},Ke={class:"col-span-12"},Ze={class:"col-span-6 sm:col-span-12"},qe={class:"flex items-center mt-2"},He=s("label",{for:"show_auto_billing_client_secret",class:"ml-3 block text-sm font-medium text-gray-700"},"Show credentials",-1),We={class:"col-span-6 sm:col-span-12"},Xe={class:"flex items-center mt-2"},Ye=s("label",{for:"show_auto_billing_client_id",class:"ml-3 block text-sm font-medium text-gray-700"},"Show credentials",-1),Ge={class:"mt-10 mb-10"},Qe=s("span",{class:"font-semibold"},"Billing Report Settings",-1),xe={class:"mb-4"},es=s("span",{class:"text-sm text-gray-500"},"Create Billing Reports",-1),ss={class:"text-sm text-gray-400"},ts={class:"grid grid-cols-12 gap-6 bg-gray-100 p-4 mb-4"},os={class:"col-span-6 sm:col-span-12"},ls={class:"col-span-6 sm:col-span-12"},ns={class:"bg-gray-100 p-4 mb-4"},as=s("span",{class:"block text-sm font-medium text-gray-700 mt-4 mb-2"},"Add or Remove Billing Costs:",-1),rs={class:"flex space-x-2"},is={class:"w-full"},ds={class:"flex items-center space-x-4"},ms={class:"flex space-x-1 items-center"},us={class:"w-20"},cs=s("span",null,"%",-1),ps=["onClick"],_s=s("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"},null,-1),gs=[_s],hs={class:"flex justify-end mt-2"},fs=s("svg",{class:"w-4 h-4 mr-1",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[s("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"})],-1),bs=s("span",null,"Add Cost",-1),ws={class:"flex items-center bg-gray-100 p-4 mb-4"},vs=s("span",{class:"block text-sm font-medium text-gray-700 mr-2 whitespace-nowrap"},"Email Reports:",-1),ys={key:0,class:"w-20"};function js(e,t,ks,Vs,Cs,Ss){const w=p("jet-button"),d=p("jet-label"),m=p("jet-input"),r=p("jet-input-error"),U=p("jet-textarea"),T=p("jet-danger-button"),v=$,y=J,P=p("jet-secondary-button"),B=I,M=O,A=E,D=p("jet-dialog-modal");return n(),a("div",null,[e.showAddbutton?(n(),h(w,{key:0,onClick:t[0]||(t[0]=o=>e.openModal()),class:"float-right mb-6"},{default:i(()=>[u(" Add Project ")]),_:1})):_("",!0),s("div",se,[e.showSuccessMessage?(n(),a("div",te,[e.wantsToUpdate?(n(),a("strong",oe,"Project updated successfully")):e.wantsToDelete?(n(),a("strong",le,"Project deleted successfully")):(n(),a("strong",ne,"Project created successfully")),s("span",{onClick:t[1]||(t[1]=o=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},re)])):_("",!0),e.showErrorMessage?(n(),a("div",ie,[e.wantsToUpdate?(n(),a("strong",de,"Project update failed")):e.wantsToDelete?(n(),a("strong",me,"Project delete failed")):(n(),a("strong",ue,"Project creation failed")),s("span",{onClick:t[2]||(t[2]=o=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},pe)])):_("",!0),l(D,{show:e.showModal,closeable:!1},{title:i(()=>[e.wantsToUpdate?(n(),a(g,{key:0},[u("Update Project")],64)):e.wantsToDelete?(n(),a(g,{key:1},[u("Delete Project")],64)):(n(),a(g,{key:2},[u("Add Project")],64))]),content:i(()=>[e.wantsToDelete?(n(),a(g,{key:0},[_e,s("p",ge,f(e.project.name),1)],64)):(n(),a(g,{key:1},[s("div",he,[s("div",fe,[l(d,{for:"name",value:"Project Name"}),l(m,{id:"name",type:"text",class:"w-full mt-1 block",modelValue:e.form.name,"onUpdate:modelValue":t[3]||(t[3]=o=>e.form.name=o)},null,8,["modelValue"]),l(r,{message:e.form.errors.name,class:"mt-2"},null,8,["message"])]),s("div",be,[l(d,{for:"description",value:"Project Description"}),l(U,{id:"description",class:"w-full mt-1 block",modelValue:e.form.description,"onUpdate:modelValue":t[4]||(t[4]=o=>e.form.description=o)},null,8,["modelValue"]),l(r,{message:e.form.errors.description,class:"mt-2"},null,8,["message"])]),s("div",we,[l(d,{for:"website_url",value:"Website URL"}),l(m,{id:"website_url",type:"text",class:"w-full mt-1 block",modelValue:e.form.website_url,"onUpdate:modelValue":t[5]||(t[5]=o=>e.form.website_url=o)},null,8,["modelValue"]),l(r,{message:e.form.errors.website_url,class:"mt-2"},null,8,["message"])]),s("div",ve,[s("div",ye,[s("div",null,[l(d,{for:"pdf",value:e.form.pdf_path?"Change PDF":"Upload PDF"},null,8,["value"]),s("input",{type:"file",id:"pdf",class:"w-full mt-1",onChange:t[6]||(t[6]=(...o)=>e.handleSelectedPDF&&e.handleSelectedPDF(...o)),accept:".pdf"},null,32),l(r,{message:e.form.errors.pdf,class:"mt-2"},null,8,["message"])]),e.form.pdf_path!=null?(n(),a("div",je,[l(T,{onClick:t[7]||(t[7]=o=>e.form.pdf_path=null),class:"mr-2"},{default:i(()=>[u(" Remove PDF ")]),_:1})])):_("",!0)]),e.form.pdf?(n(),a("div",ke,[s("embed",{src:e.pdfPreview,type:"application/pdf",width:"100%",height:"400px",class:"mt-4 block border border-gray-500"},null,8,Ve)])):e.form.pdf_path?(n(),a("div",Ce,[s("embed",{src:e.form.pdf_path,type:"application/pdf",width:"100%",height:"400px",class:"mt-4 block border border-gray-500"},null,8,Se)])):_("",!0)]),s("div",Te,[s("span",null,[Pe,l(v,{modelValue:e.form.can_send_messages,"onUpdate:modelValue":t[8]||(t[8]=o=>e.form.can_send_messages=o),class:"mx-2"},null,8,["modelValue"]),s("span",Ue,"— "+f(e.form.can_send_messages?"Turn off to stop sending messages":"Turn on to start sending messages"),1)])])]),s("div",Be,[l(y,{"content-position":"left"},{default:i(()=>[Me]),_:1})]),s("div",Ae,[s("div",De,[l(d,{for:"sms_sender_name",value:"Sender Name"}),l(m,{id:"sms_sender_name",type:"text",placeholder:"Company XYZ",maxlength:20,class:"w-full mt-1 block",modelValue:e.form.settings.sms_sender_name,"onUpdate:modelValue":t[9]||(t[9]=o=>e.form.settings.sms_sender_name=o)},null,8,["modelValue"]),l(r,{message:e.form.errors["settings.sms_sender_name"],class:"mt-2"},null,8,["message"])]),s("div",$e,[l(d,{for:"sms_sender_number",value:"Sender Number"}),l(m,{id:"sms_sender_number",type:"text",placeholder:"26772012345",maxlength:11,class:"w-full mt-1 block",modelValue:e.form.settings.sms_sender_number,"onUpdate:modelValue":t[10]||(t[10]=o=>e.form.settings.sms_sender_number=o)},null,8,["modelValue"]),l(r,{message:e.form.errors["settings.sms_sender_number"],class:"mt-2"},null,8,["message"])]),s("div",Ee,[l(d,{for:"sms_client_credentials",value:"Client Credentials"}),l(m,{id:"sms_client_credentials",type:e.showClientCredentials?"text":"password",placeholder:"*************************",class:"w-full mt-1 block",modelValue:e.form.settings.sms_client_credentials,"onUpdate:modelValue":t[11]||(t[11]=o=>e.form.settings.sms_client_credentials=o)},null,8,["type","modelValue"]),l(r,{message:e.form.errors["settings.sms_client_credentials"],class:"mt-2"},null,8,["message"]),s("div",Ie,[j(s("input",{"onUpdate:modelValue":t[12]||(t[12]=o=>e.showClientCredentials=o),id:"show_client_credentials",name:"show_client_credentials",type:"checkbox",class:"focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"},null,512),[[k,e.showClientCredentials]]),Oe])])]),s("div",Je,[l(y,{"content-position":"left"},{default:i(()=>[Re]),_:1})]),s("div",Fe,[s("div",Le,[s("span",null,[Ne,l(v,{modelValue:e.form.can_auto_bill,"onUpdate:modelValue":t[13]||(t[13]=o=>e.form.can_auto_bill=o),class:"mx-2"},null,8,["modelValue"]),s("span",ze,"— "+f(e.form.can_auto_bill?"Turn off to stop auto billing on subscription plans":"Turn on to start auto billing on subscription plans"),1)])]),s("div",Ke,[l(d,{for:"billing_name",value:"Billing Name (onBehalfOf)"}),l(m,{id:"billing_name",type:"text",class:"w-full mt-1 block",modelValue:e.form.settings.billing_name,"onUpdate:modelValue":t[14]||(t[14]=o=>e.form.settings.billing_name=o)},null,8,["modelValue"]),l(r,{message:e.form.errors["settings.billing_name"],class:"mt-2"},null,8,["message"])]),s("div",Ze,[l(d,{for:"auto_billing_client_id",value:"Client ID"}),l(m,{id:"auto_billing_client_id",type:e.showAutoBillingClientSecret?"text":"password",placeholder:"*************************",class:"w-full mt-1 block",modelValue:e.form.settings.auto_billing_client_id,"onUpdate:modelValue":t[15]||(t[15]=o=>e.form.settings.auto_billing_client_id=o)},null,8,["type","modelValue"]),l(r,{message:e.form.errors["settings.auto_billing_client_id"],class:"mt-2"},null,8,["message"]),s("div",qe,[j(s("input",{"onUpdate:modelValue":t[16]||(t[16]=o=>e.showAutoBillingClientSecret=o),id:"show_auto_billing_client_secret",name:"show_auto_billing_client_secret",type:"checkbox",class:"focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"},null,512),[[k,e.showAutoBillingClientSecret]]),He])]),s("div",We,[l(d,{for:"auto_billing_client_secret",value:"Client Secret"}),l(m,{id:"auto_billing_client_secret",type:e.showAutoBillingClientID?"text":"password",placeholder:"*************************",class:"w-full mt-1 block",modelValue:e.form.settings.auto_billing_client_secret,"onUpdate:modelValue":t[17]||(t[17]=o=>e.form.settings.auto_billing_client_secret=o)},null,8,["type","modelValue"]),l(r,{message:e.form.errors["settings.auto_billing_client_secret"],class:"mt-2"},null,8,["message"]),s("div",Xe,[j(s("input",{"onUpdate:modelValue":t[18]||(t[18]=o=>e.showAutoBillingClientID=o),id:"show_auto_billing_client_id",name:"show_auto_billing_client_id",type:"checkbox",class:"focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"},null,512),[[k,e.showAutoBillingClientID]]),Ye])])]),s("div",Ge,[l(y,{"content-position":"left"},{default:i(()=>[Qe]),_:1}),s("p",xe,[es,l(v,{modelValue:e.form.can_create_billing_reports,"onUpdate:modelValue":t[19]||(t[19]=o=>e.form.can_create_billing_reports=o),class:"mx-2"},null,8,["modelValue"]),s("span",ss,"— "+f(e.form.can_create_billing_reports?"Turn off to stop creation of billing reports":"Turn on to start creation of billing reports"),1)]),s("div",ts,[s("div",os,[l(d,{for:"their_share_percentage",value:"Their Share (%)"}),l(m,{id:"their_share_percentage",type:"number",placeholder:"40",maxlength:100,class:"w-full mt-1 block",modelValue:e.form.their_share_percentage,"onUpdate:modelValue":t[20]||(t[20]=o=>e.form.their_share_percentage=o)},null,8,["modelValue"]),l(r,{message:e.form.errors.their_share_percentage,class:"mt-2"},null,8,["message"])]),s("div",ls,[l(d,{for:"our_share_percentage",value:"Our Share (%)"}),l(m,{id:"our_share_percentage",type:"number",placeholder:"60",maxlength:100,class:"w-full mt-1 block",modelValue:e.form.our_share_percentage,"onUpdate:modelValue":t[21]||(t[21]=o=>e.form.our_share_percentage=o)},null,8,["modelValue"]),l(r,{message:e.form.errors.our_share_percentage,class:"mt-2"},null,8,["message"])])]),s("div",ns,[as,(n(!0),a(g,null,V(e.form.costs,(o,c)=>(n(),a("div",{key:c,class:"hover:bg-gray-100 p-2 -mx-2 rounded-md"},[s("div",rs,[s("div",is,[l(m,{id:"cost_name",type:"text",class:"w-full",modelValue:o.name,"onUpdate:modelValue":b=>o.name=b},null,8,["modelValue","onUpdate:modelValue"])]),s("div",null,[s("div",ds,[s("div",ms,[s("div",us,[l(m,{id:"cost_amount",type:"number",class:"w-full",modelValue:o.percentage,"onUpdate:modelValue":b=>o.percentage=b,min:1,max:100},null,8,["modelValue","onUpdate:modelValue"])]),cs]),s("div",null,[(n(),a("svg",{onClick:b=>e.removeCost(c),class:"w-4 h-4 hover:opacity-80 cursor-pointer",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},gs,8,ps))])])])]),l(r,{message:e.form.errors["costs."+c+".name"],class:"mt-2 ml-2"},null,8,["message"]),l(r,{message:e.form.errors["costs."+c+".percentage"],class:"mt-2 ml-2"},null,8,["message"])]))),128)),l(r,{message:e.form.errors.costs,class:"mt-2 ml-2"},null,8,["message"]),s("div",hs,[l(P,{onClick:t[22]||(t[22]=o=>e.addCost())},{default:i(()=>[fs,bs]),_:1})])]),s("div",null,[s("div",ws,[vs,s("div",null,[(n(!0),a(g,null,V(e.form.billing_report_email_addresses,o=>(n(),h(B,{key:o,class:"mx-1",closable:"","disable-transitions":!1,onClose:c=>e.handleRemoveTag(o)},{default:i(()=>[u(f(o),1)]),_:2},1032,["onClose"]))),128)),e.showAddTagInput?(n(),a("span",ys,[l(M,{ref:"addTagInputRef",modelValue:e.addTagInput,"onUpdate:modelValue":t[23]||(t[23]=o=>e.addTagInput=o),size:"small",onKeyup:N(e.handleAddTag,["enter"]),onBlur:e.handleAddTag},null,8,["modelValue","onKeyup","onBlur"])])):(n(),h(A,{key:1,class:"button-new-tag ml-1",size:"small",onClick:e.showInput},{default:i(()=>[u(" + New Email ")]),_:1},8,["onClick"]))])]),(n(!0),a(g,null,V(e.form.billing_report_email_addresses,(o,c)=>(n(),a("div",{key:c},[l(r,{message:e.form.errors["billing_report_email_addresses."+c],class:"mt-2 ml-2"},null,8,["message"])]))),128))])])],64))]),footer:i(()=>[l(P,{onClick:t[24]||(t[24]=o=>e.closeModal()),class:"mr-2"},{default:i(()=>[u(" Cancel ")]),_:1}),e.hasProject?_("",!0):(n(),h(w,{key:0,onClick:t[25]||(t[25]=C(o=>e.create(),["prevent"])),class:S({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:i(()=>[u(" Create ")]),_:1},8,["class","disabled"])),e.wantsToUpdate?(n(),h(w,{key:1,onClick:t[26]||(t[26]=C(o=>e.update(),["prevent"])),class:S({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:i(()=>[u(" Update ")]),_:1},8,["class","disabled"])):_("",!0),e.wantsToDelete?(n(),h(T,{key:2,onClick:t[27]||(t[27]=C(o=>e.destroy(),["prevent"])),class:S({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:i(()=>[u(" Delete ")]),_:1},8,["class","disabled"])):_("",!0)]),_:1},8,["show"])])])}const qs=x(ee,[["render",js]]);export{qs as default};
