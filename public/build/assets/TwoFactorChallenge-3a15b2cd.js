import{d,v as x,e as s,b as o,u as t,w as m,F as r,o as a,X as h,a as i,g as n,i as y,n as C,q as w}from"./app-bb45fb6f.js";import{A as V}from"./AuthenticationCard-fe0206fc.js";import{_ as $}from"./AuthenticationCardLogo-efa20fa3.js";import{_ as p,a as v}from"./TextInput-431a2b44.js";import{_ as g}from"./InputLabel-361014b1.js";import{_ as I}from"./PrimaryButton-40033d3b.js";import"./_plugin-vue_export-helper-c27b6911.js";const T={class:"mb-4 text-sm text-gray-600"},U=["onSubmit"],B={key:0},F={key:1},N={class:"flex items-center justify-end mt-4"},A=["onClick"],X={__name:"TwoFactorChallenge",setup(P){const c=d(!1),e=x({code:"",recovery_code:""}),f=d(null),_=d(null),k=async()=>{c.value^=!0,await w(),c.value?(f.value.focus(),e.code=""):(_.value.focus(),e.recovery_code="")},b=()=>{e.post(route("two-factor.login"))};return(R,l)=>(a(),s(r,null,[o(t(h),{title:"Two-factor Confirmation"}),o(V,null,{logo:m(()=>[o($)]),default:m(()=>[i("div",T,[c.value?(a(),s(r,{key:1},[n(" Please confirm access to your account by entering one of your emergency recovery codes. ")],64)):(a(),s(r,{key:0},[n(" Please confirm access to your account by entering the authentication code provided by your authenticator application. ")],64))]),i("form",{onSubmit:y(b,["prevent"])},[c.value?(a(),s("div",F,[o(g,{for:"recovery_code",value:"Recovery Code"}),o(p,{id:"recovery_code",ref_key:"recoveryCodeInput",ref:f,modelValue:t(e).recovery_code,"onUpdate:modelValue":l[1]||(l[1]=u=>t(e).recovery_code=u),type:"text",class:"w-full mt-1 block",autocomplete:"one-time-code"},null,8,["modelValue"]),o(v,{class:"mt-2",message:t(e).errors.recovery_code},null,8,["message"])])):(a(),s("div",B,[o(g,{for:"code",value:"Code"}),o(p,{id:"code",ref_key:"codeInput",ref:_,modelValue:t(e).code,"onUpdate:modelValue":l[0]||(l[0]=u=>t(e).code=u),type:"text",inputmode:"numeric",class:"w-full mt-1 block",autofocus:"",autocomplete:"one-time-code"},null,8,["modelValue"]),o(v,{class:"mt-2",message:t(e).errors.code},null,8,["message"])])),i("div",N,[i("button",{type:"button",class:"text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer",onClick:y(k,["prevent"])},[c.value?(a(),s(r,{key:1},[n(" Use an authentication code ")],64)):(a(),s(r,{key:0},[n(" Use a recovery code ")],64))],8,A),o(I,{class:C(["ml-4",{"opacity-25":t(e).processing}]),disabled:t(e).processing},{default:m(()=>[n(" Log in ")]),_:1},8,["class","disabled"])])],40,U)]),_:1})],64))}};export{X as default};
