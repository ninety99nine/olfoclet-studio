import{y as x,A as v,B as p,k as b,o as f,c as h,b as r,w as o,l as m,a as s,T as u,z as y,n as g,r as l,f as _,C as k}from"./app-5698ab4f.js";const B={class:"fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50","scroll-region":""},$=s("div",{class:"absolute inset-0 bg-gray-500 opacity-75"},null,-1),C=[$],W={__name:"Modal",props:{show:{type:Boolean,default:!1},maxWidth:{type:String,default:"2xl"},closeable:{type:Boolean,default:!0}},emits:["close"],setup(e,{emit:n}){const t=e,c=n;x(()=>t.show,()=>{t.show?document.body.style.overflow="hidden":document.body.style.overflow=null});const a=()=>{t.closeable&&c("close")},i=d=>{d.key==="Escape"&&t.show&&a()};v(()=>document.addEventListener("keydown",i)),p(()=>{document.removeEventListener("keydown",i),document.body.style.overflow=null});const w=b(()=>({sm:"sm:max-w-sm",md:"sm:max-w-md",lg:"sm:max-w-lg",xl:"sm:max-w-xl","2xl":"sm:max-w-2xl"})[t.maxWidth]);return(d,N)=>(f(),h(k,{to:"body"},[r(u,{"leave-active-class":"duration-200"},{default:o(()=>[m(s("div",B,[r(u,{"enter-active-class":"ease-out duration-300","enter-from-class":"opacity-0","enter-to-class":"opacity-100","leave-active-class":"ease-in duration-200","leave-from-class":"opacity-100","leave-to-class":"opacity-0"},{default:o(()=>[m(s("div",{class:"fixed inset-0 transform transition-all",onClick:a},C,512),[[y,e.show]])]),_:1}),r(u,{"enter-active-class":"ease-out duration-300","enter-from-class":"opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95","enter-to-class":"opacity-100 translate-y-0 sm:scale-100","leave-active-class":"ease-in duration-200","leave-from-class":"opacity-100 translate-y-0 sm:scale-100","leave-to-class":"opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"},{default:o(()=>[m(s("div",{class:g(["mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto",w.value])},[e.show?l(d.$slots,"default",{key:0}):_("",!0)],2),[[y,e.show]])]),_:3})],512),[[y,e.show]])]),_:3})]))}},E={class:"px-6 py-4"},S={class:"text-lg font-medium text-gray-900"},z={class:"mt-4 text-gray-600"},M={class:"flex flex-row justify-end px-6 py-4 bg-gray-100 text-right"},V={__name:"DialogModal",props:{show:{type:Boolean,default:!1},maxWidth:{type:String,default:"2xl"},closeable:{type:Boolean,default:!0}},emits:["close"],setup(e,{emit:n}){const t=n,c=()=>{t("close")};return(a,i)=>(f(),h(W,{show:e.show,"max-width":e.maxWidth,closeable:e.closeable,onClose:c},{default:o(()=>[s("div",E,[s("div",S,[l(a.$slots,"title")]),s("div",z,[l(a.$slots,"content")])]),s("div",M,[l(a.$slots,"footer")])]),_:3},8,["show","max-width","closeable"]))}};export{W as _,V as a};
