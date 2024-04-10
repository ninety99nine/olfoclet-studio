import{u as N,a as l,E as L}from"./el-popper-ade42412.js";import{_ as S,b as y,d as c,u as M,g as R,w as U}from"./base-4015d282.js";import{i as V,d as j,b as D}from"./icon-bb3cd363.js";import{q as h,r as g,d as I,L as $,K,B as H,u as a,aV as q,k as b,o as T,c as z,w as B,e as F,n as J,t as _,f as P,g as Y,aR as G}from"./app-fda283e1.js";const W=h({inheritAttrs:!1});function Q(t,r,n,o,d,p){return g(t.$slots,"default")}var X=S(W,[["render",Q],["__file","/home/runner/work/element-plus/element-plus/packages/components/collection/src/collection.vue"]]);const Z=h({name:"ElCollectionItem",inheritAttrs:!1});function x(t,r,n,o,d,p){return g(t.$slots,"default")}var ee=S(Z,[["render",x],["__file","/home/runner/work/element-plus/element-plus/packages/components/collection/src/collection-item.vue"]]);const te="data-el-collection-item",oe=t=>{const r=`El${t}Collection`,n=`${r}Item`,o=Symbol(r),d=Symbol(n),p={...X,name:r,setup(){const v=I(null),f=new Map;$(o,{itemMap:f,getItems:()=>{const i=a(v);if(!i)return[];const s=Array.from(i.querySelectorAll(`[${te}]`));return[...f.values()].sort((E,C)=>s.indexOf(E.ref)-s.indexOf(C.ref))},collectionRef:v})}},m={...ee,name:n,setup(v,{attrs:f}){const u=I(null),i=K(o,void 0);$(d,{collectionItemRef:u}),H(()=>{const s=a(u);s&&i.itemMap.set(s,{ref:s,...f})}),q(()=>{const s=a(u);i.itemMap.delete(s)})}};return{COLLECTION_INJECTION_KEY:o,COLLECTION_ITEM_INJECTION_KEY:d,ElCollection:p,ElCollectionItem:m}},w=y({trigger:N.trigger,effect:{...l.effect,default:"light"},type:{type:c(String)},placement:{type:c(String),default:"bottom"},popperOptions:{type:c(Object),default:()=>({})},id:String,size:{type:String,default:""},splitButton:Boolean,hideOnClick:{type:Boolean,default:!0},loop:{type:Boolean,default:!0},showTimeout:{type:Number,default:150},hideTimeout:{type:Number,default:150},tabindex:{type:c([Number,String]),default:0},maxHeight:{type:c([Number,String]),default:""},popperClass:{type:String,default:""},disabled:{type:Boolean,default:!1},role:{type:String,default:"menu"},buttonProps:{type:c(Object)},teleported:l.teleported});y({command:{type:[Object,String,Number],default:()=>({})},disabled:Boolean,divided:Boolean,textValue:String,icon:{type:V}});y({onKeydown:{type:c(Function)}});oe("Dropdown");const re=y({trigger:N.trigger,placement:w.placement,disabled:N.disabled,visible:l.visible,transition:l.transition,popperOptions:w.popperOptions,tabindex:w.tabindex,content:l.content,popperStyle:l.popperStyle,popperClass:l.popperClass,enterable:{...l.enterable,default:!0},effect:{...l.effect,default:"light"},teleported:l.teleported,title:String,width:{type:[String,Number],default:150},offset:{type:Number,default:void 0},showAfter:{type:Number,default:0},hideAfter:{type:Number,default:200},autoClose:{type:Number,default:0},showArrow:{type:Boolean,default:!0},persistent:{type:Boolean,default:!0},"onUpdate:visible":{type:Function}}),ne={"update:visible":t=>j(t),"before-enter":()=>!0,"before-leave":()=>!0,"after-enter":()=>!0,"after-leave":()=>!0},se="onUpdate:visible",le=h({name:"ElPopover"}),ae=h({...le,props:re,emits:ne,setup(t,{expose:r,emit:n}){const o=t,d=b(()=>o[se]),p=M("popover"),m=I(),v=b(()=>{var e;return(e=a(m))==null?void 0:e.popperRef}),f=b(()=>[{width:D(o.width)},o.popperStyle]),u=b(()=>[p.b(),o.popperClass,{[p.m("plain")]:!!o.content}]),i=b(()=>o.transition===`${p.namespace.value}-fade-in-linear`),s=()=>{var e;(e=m.value)==null||e.hide()},O=()=>{n("before-enter")},E=()=>{n("before-leave")},C=()=>{n("after-enter")},A=()=>{n("update:visible",!1),n("after-leave")};return r({popperRef:v,hide:s}),(e,fe)=>(T(),z(a(L),G({ref_key:"tooltipRef",ref:m},e.$attrs,{trigger:e.trigger,placement:e.placement,disabled:e.disabled,visible:e.visible,transition:e.transition,"popper-options":e.popperOptions,tabindex:e.tabindex,content:e.content,offset:e.offset,"show-after":e.showAfter,"hide-after":e.hideAfter,"auto-close":e.autoClose,"show-arrow":e.showArrow,"aria-label":e.title,effect:e.effect,enterable:e.enterable,"popper-class":a(u),"popper-style":a(f),teleported:e.teleported,persistent:e.persistent,"gpu-acceleration":a(i),"onUpdate:visible":a(d),onBeforeShow:O,onBeforeHide:E,onShow:C,onHide:A}),{content:B(()=>[e.title?(T(),F("div",{key:0,class:J(a(p).e("title")),role:"title"},_(e.title),3)):P("v-if",!0),g(e.$slots,"default",{},()=>[Y(_(e.content),1)])]),default:B(()=>[e.$slots.reference?g(e.$slots,"reference",{key:0}):P("v-if",!0)]),_:3},16,["trigger","placement","disabled","visible","transition","popper-options","tabindex","content","offset","show-after","hide-after","auto-close","show-arrow","aria-label","effect","enterable","popper-class","popper-style","teleported","persistent","gpu-acceleration","onUpdate:visible"]))}});var pe=S(ae,[["__file","/home/runner/work/element-plus/element-plus/packages/components/popover/src/popover.vue"]]);const k=(t,r)=>{const n=r.arg||r.value,o=n==null?void 0:n.popperRef;o&&(o.triggerRef=t)};var ie={mounted(t,r){k(t,r)},updated(t,r){k(t,r)}};const ce="popover",de=R(ie,ce),ge=U(pe,{directive:de});export{ge as E};
