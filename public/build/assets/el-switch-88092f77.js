import{C as w,u as a,k as v,U as R,P as I,d as g,G as J,bf as ie,H as oe,bA as N,s as W,o as r,e as y,a as z,n as p,A as ne,c as b,w as V,S as B,f as m,t as _,b as se,y as q,i as le,q as re,bF as H}from"./app-bb45fb6f.js";import{E as C}from"./index-58643621.js";import{h as P,j as U,i as L,b as ce,l as ue}from"./icon-45c0f559.js";import{c as de,b as ve,d as pe,u as fe,_ as me,w as he}from"./base-130e034c.js";import{b as O,f as Y,a as ye}from"./constants-339607f3.js";class be extends Error{constructor(o){super(o),this.name="ElementPlusError"}}function ge(i,o){throw new be(`[${i}] ${o}`)}function He(i,o){}const A="update:modelValue",D="change",F="input",Z=["","default","small","large"],Le={large:40,default:32,small:24},Ie=i=>["",...Z].includes(i),Se=({from:i,replacement:o,scope:s,version:t,ref:c,type:u="API"},h)=>{w(()=>a(h),n=>{},{immediate:!0})},G=i=>{const o=R();return v(()=>{var s,t;return(t=((s=o.proxy)==null?void 0:s.$props)[i])!=null?t:void 0})},Re=de({type:String,values:Z,required:!1}),we=Symbol("size"),ke=()=>{const i=I(we,{});return v(()=>a(i.size)||"")},Ee=(i,o={})=>{const s=g(void 0),t=o.prop?s:G("size"),c=o.global?s:ke(),u=o.form?{size:void 0}:I(O,void 0),h=o.formItem?{size:void 0}:I(Y,void 0);return v(()=>t.value||a(i)||(h==null?void 0:h.size)||(u==null?void 0:u.size)||c.value||"")},Ve=i=>{const o=G("disabled"),s=I(O,void 0);return v(()=>o.value||a(i)||(s==null?void 0:s.disabled)||!1)},Ce=()=>{const i=I(O,void 0),o=I(Y,void 0);return{form:i,formItem:o}},Pe=(i,{formItemContext:o,disableIdGeneration:s,disableIdManagement:t})=>{s||(s=g(!1)),t||(t=g(!1));const c=g();let u;const h=v(()=>{var n;return!!(!i.label&&o&&o.inputIds&&((n=o.inputIds)==null?void 0:n.length)<=1)});return J(()=>{u=w([ie(i,"id"),s],([n,k])=>{const f=n??(k?void 0:ye().value);f!==c.value&&(o!=null&&o.removeInputId&&(c.value&&o.removeInputId(c.value),!(t!=null&&t.value)&&!k&&f&&o.addInputId(f)),c.value=f)},{immediate:!0})}),oe(()=>{u&&u(),o!=null&&o.removeInputId&&c.value&&o.removeInputId(c.value)}),{isLabeledByFormItem:h,inputId:c}},Te=ve({modelValue:{type:[Boolean,String,Number],default:!1},value:{type:[Boolean,String,Number],default:!1},disabled:{type:Boolean,default:!1},width:{type:[String,Number],default:""},inlinePrompt:{type:Boolean,default:!1},activeIcon:{type:L},inactiveIcon:{type:L},activeText:{type:String,default:""},inactiveText:{type:String,default:""},activeColor:{type:String,default:""},inactiveColor:{type:String,default:""},borderColor:{type:String,default:""},activeValue:{type:[Boolean,String,Number],default:!0},inactiveValue:{type:[Boolean,String,Number],default:!1},name:{type:String,default:""},validateEvent:{type:Boolean,default:!0},id:String,loading:{type:Boolean,default:!1},beforeChange:{type:pe(Function)},size:{type:String,validator:Ie},tabindex:{type:[String,Number]}}),Ne={[A]:i=>P(i)||N(i)||U(i),[D]:i=>P(i)||N(i)||U(i),[F]:i=>P(i)||N(i)||U(i)},ze=["onClick"],Be=["id","aria-checked","aria-disabled","name","true-value","false-value","disabled","tabindex","onKeydown"],_e=["aria-hidden"],Ue=["aria-hidden"],Ae=["aria-hidden"],K="ElSwitch",De=W({name:K}),Fe=W({...De,props:Te,emits:Ne,setup(i,{expose:o,emit:s}){const t=i,c=R(),{formItem:u}=Ce(),h=Ee(),n=fe("switch");Se({from:'"value"',replacement:'"model-value" or "v-model"',scope:K,version:"2.3.0",ref:"https://element-plus.org/en-US/component/switch.html#attributes",type:"Attribute"},v(()=>{var e;return!!((e=c.vnode.props)!=null&&e.value)}));const{inputId:k}=Pe(t,{formItemContext:u}),f=Ve(v(()=>t.loading)),T=g(t.modelValue!==!1),S=g(),Q=g(),X=v(()=>[n.b(),n.m(h.value),n.is("disabled",f.value),n.is("checked",l.value)]),x=v(()=>({width:ce(t.width)}));w(()=>t.modelValue,()=>{T.value=!0}),w(()=>t.value,()=>{T.value=!1});const j=v(()=>T.value?t.modelValue:t.value),l=v(()=>j.value===t.activeValue);[t.activeValue,t.inactiveValue].includes(j.value)||(s(A,t.inactiveValue),s(D,t.inactiveValue),s(F,t.inactiveValue)),w(l,e=>{var d;S.value.checked=e,t.validateEvent&&((d=u==null?void 0:u.validate)==null||d.call(u,"change").catch(te=>void 0))});const E=()=>{const e=l.value?t.inactiveValue:t.activeValue;s(A,e),s(D,e),s(F,e),re(()=>{S.value.checked=l.value})},M=()=>{if(f.value)return;const{beforeChange:e}=t;if(!e){E();return}const d=e();[H(d),P(d)].includes(!0)||ge(K,"beforeChange must return type `Promise<boolean>` or `boolean`"),H(d)?d.then($=>{$&&E()}).catch($=>{}):d&&E()},ee=v(()=>n.cssVarBlock({...t.activeColor?{"on-color":t.activeColor}:null,...t.inactiveColor?{"off-color":t.inactiveColor}:null,...t.borderColor?{"border-color":t.borderColor}:null})),ae=()=>{var e,d;(d=(e=S.value)==null?void 0:e.focus)==null||d.call(e)};return J(()=>{S.value.checked=l.value}),o({focus:ae,checked:l}),(e,d)=>(r(),y("div",{class:p(a(X)),style:q(a(ee)),onClick:le(M,["prevent"])},[z("input",{id:a(k),ref_key:"input",ref:S,class:p(a(n).e("input")),type:"checkbox",role:"switch","aria-checked":a(l),"aria-disabled":a(f),name:e.name,"true-value":e.activeValue,"false-value":e.inactiveValue,disabled:a(f),tabindex:e.tabindex,onChange:E,onKeydown:ne(M,["enter"])},null,42,Be),!e.inlinePrompt&&(e.inactiveIcon||e.inactiveText)?(r(),y("span",{key:0,class:p([a(n).e("label"),a(n).em("label","left"),a(n).is("active",!a(l))])},[e.inactiveIcon?(r(),b(a(C),{key:0},{default:V(()=>[(r(),b(B(e.inactiveIcon)))]),_:1})):m("v-if",!0),!e.inactiveIcon&&e.inactiveText?(r(),y("span",{key:1,"aria-hidden":a(l)},_(e.inactiveText),9,_e)):m("v-if",!0)],2)):m("v-if",!0),z("span",{ref_key:"core",ref:Q,class:p(a(n).e("core")),style:q(a(x))},[e.inlinePrompt?(r(),y("div",{key:0,class:p(a(n).e("inner"))},[e.activeIcon||e.inactiveIcon?(r(),b(a(C),{key:0,class:p(a(n).is("icon"))},{default:V(()=>[(r(),b(B(a(l)?e.activeIcon:e.inactiveIcon)))]),_:1},8,["class"])):e.activeText||e.inactiveText?(r(),y("span",{key:1,class:p(a(n).is("text")),"aria-hidden":!a(l)},_(a(l)?e.activeText:e.inactiveText),11,Ue)):m("v-if",!0)],2)):m("v-if",!0),z("div",{class:p(a(n).e("action"))},[e.loading?(r(),b(a(C),{key:0,class:p(a(n).is("loading"))},{default:V(()=>[se(a(ue))]),_:1},8,["class"])):m("v-if",!0)],2)],6),!e.inlinePrompt&&(e.activeIcon||e.activeText)?(r(),y("span",{key:1,class:p([a(n).e("label"),a(n).em("label","right"),a(n).is("active",a(l))])},[e.activeIcon?(r(),b(a(C),{key:0},{default:V(()=>[(r(),b(B(e.activeIcon)))]),_:1})):m("v-if",!0),!e.activeIcon&&e.activeText?(r(),y("span",{key:1,"aria-hidden":!a(l)},_(e.activeText),9,Ae)):m("v-if",!0)],2)):m("v-if",!0)],14,ze))}});var Ke=me(Fe,[["__file","/home/runner/work/element-plus/element-plus/packages/components/switch/src/switch.vue"]]);const Je=he(Ke);export{D as C,Je as E,F as I,A as U,Ce as a,Ee as b,Ve as c,Re as d,He as e,Pe as f,Le as g,Z as h,Ie as i,ge as t,Se as u};
