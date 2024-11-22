import"./base-56227e5d.js";import{E as $}from"./el-divider-8ecc0c72.js";import{q as C,s as d,o,e as a,c,w as n,f as u,a as t,b as l,g as i,F as m,t as g,h as V,l as T,m as E,i as y,n as w}from"./app-33020c92.js";import{_ as D,a as B}from"./TextInput-1f4ada43.js";import{_ as J}from"./InputLabel-cb7cfcd9.js";import{_ as O}from"./PrimaryButton-6b9f4282.js";import{J as A}from"./SelectInput-a557b92c.js";import{a as L}from"./DialogModal-61701a9e.js";import{_ as N}from"./DangerButton-d84d9b81.js";import{_ as z}from"./ActionMessage-22581b6c.js";import{_ as I}from"./SecondaryButton-cebaf51c.js";import{_ as F}from"./_plugin-vue_export-helper-c27b6911.js";/* empty css            */const q=C({components:{JetLabel:J,JetInput:D,JetButton:O,JetInputError:B,JetSelectInput:A,JetDialogModal:L,JetSecondaryButton:I,JetActionMessage:z,JetDangerButton:N},props:{action:{type:String,default:"update"},availablePermissions:{type:Array},modelValue:{type:Boolean,default:!1},showAddbutton:{type:Boolean,default:!1},user:{type:Object,default:null},show:{type:Boolean,default:!1}},data(){return{form:null,accountTypes:[{name:"Management",value:"Management"},{name:"Customer Care",value:"Customer Care"}],showModal:this.modelValue,showSuccessMessage:!1,showErrorMessage:!1}},watch:{showModal:{handler:function(e,s){e!=this.modelValue&&this.$emit("update:modelValue",e)}},modelValue:{handler:function(e,s){e!=this.showModal&&(this.showModal=e,this.reset())}}},computed:{hasUser(){return this.user!=null},wantsToUpdate(){return!!(this.hasUser&&this.action=="update")},wantsToDelete(){return!!(this.hasUser&&this.action=="delete")}},methods:{openModal(){this.showModal=!0},closeModal(){this.showModal=!1},create(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess()},onError:s=>{this.handleOnError()}};this.form.post(route("create.user",{project:route().params.project}),e)},update(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess()},onError:s=>{this.handleOnError()}};this.form.put(route("update.user",{project:route().params.project,user:this.user.id}),e)},destroy(){var e={preserveState:!0,preserveScroll:!0,replace:!0,onSuccess:s=>{this.handleOnSuccess(!0)},onError:s=>{this.handleOnError()}};this.form.delete(route("delete.user",{project:route().params.project,user:this.user.id}),e)},handleOnSuccess(e=!1){this.reset(),this.closeModal(),e&&this.$emit("onDeleted"),this.showSuccessMessage=!0,setTimeout(()=>{this.showSuccessMessage=!1},3e3)},handleOnError(){this.showErrorMessage=!0,setTimeout(()=>{this.showErrorMessage=!1},3e3)},reset(){this.form=this.$inertia.form({name:this.hasUser?this.user.name:null,email:this.hasUser?this.user.email:null,permissions:this.hasUser?this.user.pivot.permissions:[],account_type:this.hasUser?this.user.account_type:"Management"})}},created(){this.reset()}}),W={class:"clear-both"},G={key:0,class:"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6",role:"alert"},H={key:0,class:"font-bold"},K={key:1,class:"font-bold"},P={key:2,class:"font-bold"},Q=t("svg",{class:"fill-current h-6 w-6 text-green-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[t("title",null,"Close"),t("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),R=[Q],X={key:1,class:"bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6",role:"alert"},Y={key:0,class:"font-bold"},Z={key:1,class:"font-bold"},x={key:2,class:"font-bold"},ee=t("svg",{class:"fill-current h-6 w-6 text-red-500",role:"button",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20"},[t("title",null,"Close"),t("path",{d:"M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"})],-1),se=[ee],te=t("span",{class:"block mt-6 mb-6"},"Are you sure you want to delete this user?",-1),oe={class:"text-sm text-gray-500"},re={class:"mb-4"},ae={class:"mb-4"},le={class:"mb-4"},ne={class:"mt-10 mb-10"},ie=t("span",{class:"font-semibold"},"Permissions",-1),de={class:"grid grid-cols-2 gap-6"},ue={class:"flex items-center"},me=["value","name","checked"],pe=["for"],ce=t("div",null,[t("span",{class:"font-bold mr-2"},"Note:"),t("span",null,"When the user account does not exist, a new account is created. The user can login in using their email and the word"),t("span",{class:"font-bold text-green-600 font-italic mx-2"},"password"),t("span",null,"as their default password. Otherwise if the user already has an existing account, they may use their usual password.")],-1);function he(e,s,fe,ge,ye,we){const h=d("jet-button"),f=d("jet-label"),b=d("jet-input"),p=d("jet-input-error"),k=d("jet-select-input"),_=$,M=d("jet-secondary-button"),U=d("jet-danger-button"),j=d("jet-dialog-modal");return o(),a("div",null,[e.$inertia.page.props.projectPermissions.includes("Manage users")&&e.showAddbutton?(o(),c(h,{key:0,onClick:s[0]||(s[0]=r=>e.openModal()),class:"float-right mb-6"},{default:n(()=>[i(" Add User ")]),_:1})):u("",!0),t("div",W,[e.showSuccessMessage?(o(),a("div",G,[e.wantsToUpdate?(o(),a("strong",H,"User updated successfully")):e.wantsToDelete?(o(),a("strong",K,"User deleted successfully")):(o(),a("strong",P,"User created successfully")),t("span",{onClick:s[1]||(s[1]=r=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},R)])):u("",!0),e.showErrorMessage?(o(),a("div",X,[e.wantsToUpdate?(o(),a("strong",Y,"User update failed")):e.wantsToDelete?(o(),a("strong",Z,"User delete failed")):(o(),a("strong",x,"User creation failed")),t("span",{onClick:s[2]||(s[2]=r=>e.showSuccessMessage=!1),class:"absolute top-0 bottom-0 right-0 px-4 py-3"},se)])):u("",!0),l(j,{show:e.showModal,closeable:!1},{title:n(()=>[e.wantsToUpdate?(o(),a(m,{key:0},[i("Update User")],64)):e.wantsToDelete?(o(),a(m,{key:1},[i("Delete User")],64)):(o(),a(m,{key:2},[i("Add User")],64))]),content:n(()=>[e.wantsToDelete?(o(),a(m,{key:0},[te,t("p",oe,g(e.user.name),1)],64)):(o(),a(m,{key:1},[t("div",re,[l(f,{for:"name",value:"Name"}),l(b,{id:"name",type:"text",class:"w-full mt-1 block",modelValue:e.form.name,"onUpdate:modelValue":s[3]||(s[3]=r=>e.form.name=r),placeholder:"John Doe"},null,8,["modelValue"]),l(p,{message:e.form.errors.name,class:"mt-2"},null,8,["message"])]),t("div",ae,[l(f,{for:"email",value:"Email"}),l(b,{id:"email",type:"email",class:"w-full mt-1 block",modelValue:e.form.email,"onUpdate:modelValue":s[4]||(s[4]=r=>e.form.email=r),placeholder:"example@gmail.com"},null,8,["modelValue"]),l(p,{message:e.form.errors.email,class:"mt-2"},null,8,["message"])]),t("div",le,[l(f,{for:"account-type",value:"Account Type",class:"mb-1"}),l(k,{id:"account-type",placeholder:"Select account type",options:e.accountTypes,modelValue:e.form.account_type,"onUpdate:modelValue":s[5]||(s[5]=r=>e.form.account_type=r)},null,8,["options","modelValue"]),l(p,{message:e.form.errors.account_type,class:"mt-2"},null,8,["message"])]),t("div",ne,[l(_,{"content-position":"left"},{default:n(()=>[ie]),_:1}),l(p,{message:e.form.errors.permissions,class:"mt-2"},null,8,["message"])]),t("div",de,[(o(!0),a(m,null,V(e.availablePermissions,(r,v)=>(o(),a("div",{key:v},[t("div",ue,[T(t("input",{"onUpdate:modelValue":s[6]||(s[6]=S=>e.form.permissions=S),value:r,name:r,type:"checkbox",checked:e.form.permissions.includes(r),class:"focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"},null,8,me),[[E,e.form.permissions]]),t("label",{for:r,class:"ml-3 block text-sm font-medium text-gray-700"},g(r),9,pe)]),l(p,{message:e.form.errors["permissions."+v],class:"mt-2"},null,8,["message"])]))),128))]),i(" "+g(e.form.errors)+" ",1),l(_,{"content-position":"left",class:"mt-10 mb-10"}),ce],64))]),footer:n(()=>[l(M,{onClick:s[7]||(s[7]=r=>e.closeModal()),class:"mr-2"},{default:n(()=>[i(" Cancel ")]),_:1}),e.hasUser?u("",!0):(o(),c(h,{key:0,onClick:s[8]||(s[8]=y(r=>e.create(),["prevent"])),class:w({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:n(()=>[i(" Create ")]),_:1},8,["class","disabled"])),e.wantsToUpdate?(o(),c(h,{key:1,onClick:s[9]||(s[9]=y(r=>e.update(),["prevent"])),class:w({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:n(()=>[i(" Update ")]),_:1},8,["class","disabled"])):u("",!0),e.wantsToDelete?(o(),c(U,{key:2,onClick:s[10]||(s[10]=y(r=>e.destroy(),["prevent"])),class:w({"opacity-25":e.form.processing}),disabled:e.form.processing},{default:n(()=>[i(" Delete ")]),_:1},8,["class","disabled"])):u("",!0)]),_:1},8,["show"])])])}const De=F(q,[["render",he]]);export{De as default};