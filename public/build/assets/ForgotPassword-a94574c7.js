import{v as d,e as r,b as e,u as t,w as o,F as c,o as m,X as f,t as _,f as p,a,n as w,g,i as b}from"./app-a9929219.js";import{A as y}from"./AuthenticationCard-ad0472b5.js";import{_ as x}from"./AuthenticationCardLogo-d3c870a7.js";import{_ as h,a as k}from"./TextInput-854b9238.js";import{_ as v}from"./InputLabel-831d05d4.js";import{_ as V}from"./PrimaryButton-151237c0.js";/* empty css            */import"./_plugin-vue_export-helper-c27b6911.js";const F=a("div",{class:"mb-4 text-sm text-gray-600"}," Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one. ",-1),N={key:0,class:"mb-4 font-medium text-sm text-green-600"},$=["onSubmit"],C={class:"flex items-center justify-end mt-4"},J={__name:"ForgotPassword",props:{status:String},setup(i){const s=d({email:""}),n=()=>{s.post(route("password.email"))};return(S,l)=>(m(),r(c,null,[e(t(f),{title:"Forgot Password"}),e(y,null,{logo:o(()=>[e(x)]),default:o(()=>[F,i.status?(m(),r("div",N,_(i.status),1)):p("",!0),a("form",{onSubmit:b(n,["prevent"])},[a("div",null,[e(v,{for:"email",value:"Email"}),e(h,{id:"email",modelValue:t(s).email,"onUpdate:modelValue":l[0]||(l[0]=u=>t(s).email=u),type:"email",class:"w-full mt-1 block",required:"",autofocus:"",autocomplete:"username"},null,8,["modelValue"]),e(k,{class:"mt-2",message:t(s).errors.email},null,8,["message"])]),a("div",C,[e(V,{class:w({"opacity-25":t(s).processing}),disabled:t(s).processing},{default:o(()=>[g(" Email Password Reset Link ")]),_:1},8,["class","disabled"])])],40,$)]),_:1})],64))}};export{J as default};