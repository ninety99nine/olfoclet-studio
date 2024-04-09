import{c as De,d as P,f as Fe,e as ot,b as D,_ as q,u as re,w as Ke}from"./base-2406453b.js";import{k as b,bC as se,z as R,B as G,Q as nt,u as r,b7 as rt,d as F,bD as st,aK as V,aJ as at,L as x,K as W,s as A,r as Z,o as L,e as ce,n as Ue,y as lt,N as me,l as je,a6 as it,G as ut,F as ct,_ as pt,S as dt,b as oe,c as ee,w as H,aG as be,f as ne,q as Pe,bA as ft,bE as vt,bB as gt,be as ue,A as mt,T as bt,D as yt,aY as Et,aM as Tt,t as wt}from"./app-9de9f9bd.js";import{c as Y,d as ze,t as ht,e as Ct,f as pe}from"./icon-69acd7da.js";import{u as Ft,a as He,i as ye,f as Re,o as Pt,b as Rt}from"./constants-d0653f0e.js";const $=(e,t,{checkForDefaultPrevented:o=!0}={})=>l=>{const i=e==null?void 0:e(l);if(o===!1||!i)return t==null?void 0:t(l)};function St(e){return e===void 0}const de={tab:"Tab",enter:"Enter",space:"Space",left:"ArrowLeft",up:"ArrowUp",right:"ArrowRight",down:"ArrowDown",esc:"Escape",delete:"Delete",backspace:"Backspace",numpadEnter:"NumpadEnter",pageUp:"PageUp",pageDown:"PageDown",home:"Home",end:"End"},_t=De({type:P(Boolean),default:null}),At=De({type:P(Function)}),Ze=e=>{const t=`update:${e}`,o=`onUpdate:${e}`,n=[t],l={[e]:_t,[o]:At};return{useModelToggle:({indicator:d,toggleReason:p,shouldHideWhenRouteChanges:u,shouldProceed:c,onShow:v,onHide:g})=>{const y=nt(),{emit:T}=y,a=y.props,m=b(()=>se(a[o])),w=b(()=>a[e]===null),s=C=>{d.value!==!0&&(d.value=!0,p&&(p.value=C),se(v)&&v(C))},E=C=>{d.value!==!1&&(d.value=!1,p&&(p.value=C),se(g)&&g(C))},S=C=>{if(a.disabled===!0||se(c)&&!c())return;const _=m.value&&Y;_&&T(t,!0),(w.value||!_)&&s(C)},h=C=>{if(a.disabled===!0||!Y)return;const _=m.value&&Y;_&&T(t,!1),(w.value||!_)&&E(C)},O=C=>{ze(C)&&(a.disabled&&C?m.value&&T(t,!1):d.value!==C&&(C?s():E()))},K=()=>{d.value?h():S()};return R(()=>a[e],O),u&&y.appContext.config.globalProperties.$route!==void 0&&R(()=>({...y.proxy.$route}),()=>{u.value&&d.value&&h()}),G(()=>{O(a[e])}),{hide:h,show:S,toggle:K,hasUpdateHandler:m}},useModelToggleProps:l,useModelToggleEmits:n}};Ze("modelValue");const It=(e,t,o={})=>{const n={name:"updateState",enabled:!0,phase:"write",fn:({state:u})=>{const c=Ot(u);Object.assign(d.value,c)},requires:["computeStyles"]},l=b(()=>{const{onFirstUpdate:u,placement:c,strategy:v,modifiers:g}=r(o);return{onFirstUpdate:u,placement:c||"bottom",strategy:v||"absolute",modifiers:[...g||[],n,{name:"applyStyles",enabled:!1}]}}),i=rt(),d=F({styles:{popper:{position:r(l).strategy,left:"0",top:"0"},arrow:{position:"absolute"}},attributes:{}}),p=()=>{i.value&&(i.value.destroy(),i.value=void 0)};return R(l,u=>{const c=r(i);c&&c.setOptions(u)},{deep:!0}),R([e,t],([u,c])=>{p(),!(!u||!c)&&(i.value=st(u,c,r(l)))}),V(()=>{p()}),{state:b(()=>{var u;return{...((u=r(i))==null?void 0:u.state)||{}}}),styles:b(()=>r(d).styles),attributes:b(()=>r(d).attributes),update:()=>{var u;return(u=r(i))==null?void 0:u.update()},forceUpdate:()=>{var u;return(u=r(i))==null?void 0:u.forceUpdate()},instanceRef:b(()=>r(i))}};function Ot(e){const t=Object.keys(e.elements),o=Fe(t.map(l=>[l,e.styles[l]||{}])),n=Fe(t.map(l=>[l,e.attributes[l]]));return{styles:o,attributes:n}}function kt(){let e;const t=(n,l)=>{o(),e=window.setTimeout(n,l)},o=()=>window.clearTimeout(e);return ht(()=>o()),{registerTimeout:t,cancelTimeout:o}}let X=[];const Se=e=>{const t=e;t.key===de.esc&&X.forEach(o=>o(t))},Mt=e=>{G(()=>{X.length===0&&document.addEventListener("keydown",Se),Y&&X.push(e)}),V(()=>{X=X.filter(t=>t!==e),X.length===0&&Y&&document.removeEventListener("keydown",Se)})};let _e;const qe=()=>{const e=ot(),t=Ft(),o=b(()=>`${e.value}-popper-container-${t.prefix}`),n=b(()=>`#${o.value}`);return{id:o,selector:n}},Lt=e=>{const t=document.createElement("div");return t.id=e,document.body.appendChild(t),t},Nt=()=>{const{id:e,selector:t}=qe();return at(()=>{Y&&!_e&&!document.body.querySelector(t.value)&&(_e=Lt(e.value))}),{id:e,selector:t}},Bt=D({showAfter:{type:Number,default:0},hideAfter:{type:Number,default:200}}),$t=({showAfter:e,hideAfter:t,open:o,close:n})=>{const{registerTimeout:l}=kt();return{onOpen:p=>{l(()=>{o(p)},r(e))},onClose:p=>{l(()=>{n(p)},r(t))}}},Ve=Symbol("elForwardRef"),Dt=e=>{x(Ve,{setForwardRef:o=>{e.value=o}})},Kt=e=>({mounted(t){e(t)},updated(t){e(t)},unmounted(){e(null)}}),Ae=F(0),Ut=2e3,jt=Symbol("zIndexContextKey"),zt=e=>{const t=e||W(jt,void 0),o=b(()=>{const i=r(t);return Ct(i)?i:Ut}),n=b(()=>o.value+Ae.value);return{initialZIndex:o,currentZIndex:n,nextZIndex:()=>(Ae.value++,n.value)}},Ee=Symbol("popper"),We=Symbol("popperContent"),Ht=["dialog","grid","group","listbox","menu","navigation","tooltip","tree"],Je=D({role:{type:String,values:Ht,default:"tooltip"}}),Zt=A({name:"ElPopper",inheritAttrs:!1}),qt=A({...Zt,props:Je,setup(e,{expose:t}){const o=e,n=F(),l=F(),i=F(),d=F(),p=b(()=>o.role),u={triggerRef:n,popperInstanceRef:l,contentRef:i,referenceRef:d,role:p};return t(u),x(Ee,u),(c,v)=>Z(c.$slots,"default")}});var Vt=q(qt,[["__file","/home/runner/work/element-plus/element-plus/packages/components/popper/src/popper.vue"]]);const Ye=D({arrowOffset:{type:Number,default:5}}),Wt=A({name:"ElPopperArrow",inheritAttrs:!1}),Jt=A({...Wt,props:Ye,setup(e,{expose:t}){const o=e,n=re("popper"),{arrowOffset:l,arrowRef:i,arrowStyle:d}=W(We,void 0);return R(()=>o.arrowOffset,p=>{l.value=p}),V(()=>{i.value=void 0}),t({arrowRef:i}),(p,u)=>(L(),ce("span",{ref_key:"arrowRef",ref:i,class:Ue(r(n).e("arrow")),style:lt(r(d)),"data-popper-arrow":""},null,6))}});var Yt=q(Jt,[["__file","/home/runner/work/element-plus/element-plus/packages/components/popper/src/arrow.vue"]]);const Gt="ElOnlyChild",Qt=A({name:Gt,setup(e,{slots:t,attrs:o}){var n;const l=W(Ve),i=Kt((n=l==null?void 0:l.setForwardRef)!=null?n:me);return()=>{var d;const p=(d=t.default)==null?void 0:d.call(t,o);if(!p||p.length>1)return null;const u=Ge(p);return u?je(it(u,o),[[i]]):null}}});function Ge(e){if(!e)return null;const t=e;for(const o of t){if(ut(o))switch(o.type){case dt:continue;case pt:case"svg":return Ie(o);case ct:return Ge(o.children);default:return o}return Ie(o)}return null}function Ie(e){const t=re("only-child");return oe("span",{class:t.e("content")},[e])}const Qe=D({virtualRef:{type:P(Object)},virtualTriggering:Boolean,onMouseenter:{type:P(Function)},onMouseleave:{type:P(Function)},onClick:{type:P(Function)},onKeydown:{type:P(Function)},onFocus:{type:P(Function)},onBlur:{type:P(Function)},onContextmenu:{type:P(Function)},id:String,open:Boolean}),Xt=A({name:"ElPopperTrigger",inheritAttrs:!1}),xt=A({...Xt,props:Qe,setup(e,{expose:t}){const o=e,{role:n,triggerRef:l}=W(Ee,void 0);Dt(l);const i=b(()=>p.value?o.id:void 0),d=b(()=>{if(n&&n.value==="tooltip")return o.open&&o.id?o.id:void 0}),p=b(()=>{if(n&&n.value!=="tooltip")return n.value}),u=b(()=>p.value?`${o.open}`:void 0);let c;return G(()=>{R(()=>o.virtualRef,v=>{v&&(l.value=He(v))},{immediate:!0}),R(l,(v,g)=>{c==null||c(),c=void 0,pe(v)&&(["onMouseenter","onMouseleave","onClick","onKeydown","onFocus","onBlur","onContextmenu"].forEach(y=>{var T;const a=o[y];a&&(v.addEventListener(y.slice(2).toLowerCase(),a),(T=g==null?void 0:g.removeEventListener)==null||T.call(g,y.slice(2).toLowerCase(),a))}),c=R([i,d,p,u],y=>{["aria-controls","aria-describedby","aria-haspopup","aria-expanded"].forEach((T,a)=>{ye(y[a])?v.removeAttribute(T):v.setAttribute(T,y[a])})},{immediate:!0})),pe(g)&&["aria-controls","aria-describedby","aria-haspopup","aria-expanded"].forEach(y=>g.removeAttribute(y))},{immediate:!0})}),V(()=>{c==null||c(),c=void 0}),t({triggerRef:l}),(v,g)=>v.virtualTriggering?ne("v-if",!0):(L(),ee(r(Qt),be({key:0},v.$attrs,{"aria-controls":r(i),"aria-describedby":r(d),"aria-expanded":r(u),"aria-haspopup":r(p)}),{default:H(()=>[Z(v.$slots,"default")]),_:3},16,["aria-controls","aria-describedby","aria-expanded","aria-haspopup"]))}});var eo=q(xt,[["__file","/home/runner/work/element-plus/element-plus/packages/components/popper/src/trigger.vue"]]);const ve="focus-trap.focus-after-trapped",ge="focus-trap.focus-after-released",to="focus-trap.focusout-prevented",Oe={cancelable:!0,bubbles:!1},oo={cancelable:!0,bubbles:!1},ke="focusAfterTrapped",Me="focusAfterReleased",no=Symbol("elFocusTrap"),Te=F(),fe=F(0),we=F(0);let ae=0;const Xe=e=>{const t=[],o=document.createTreeWalker(e,NodeFilter.SHOW_ELEMENT,{acceptNode:n=>{const l=n.tagName==="INPUT"&&n.type==="hidden";return n.disabled||n.hidden||l?NodeFilter.FILTER_SKIP:n.tabIndex>=0||n===document.activeElement?NodeFilter.FILTER_ACCEPT:NodeFilter.FILTER_SKIP}});for(;o.nextNode();)t.push(o.currentNode);return t},Le=(e,t)=>{for(const o of e)if(!ro(o,t))return o},ro=(e,t)=>{if(getComputedStyle(e).visibility==="hidden")return!0;for(;e;){if(t&&e===t)return!1;if(getComputedStyle(e).display==="none")return!0;e=e.parentElement}return!1},so=e=>{const t=Xe(e),o=Le(t,e),n=Le(t.reverse(),e);return[o,n]},ao=e=>e instanceof HTMLInputElement&&"select"in e,z=(e,t)=>{if(e&&e.focus){const o=document.activeElement;e.focus({preventScroll:!0}),we.value=window.performance.now(),e!==o&&ao(e)&&t&&e.select()}};function Ne(e,t){const o=[...e],n=e.indexOf(t);return n!==-1&&o.splice(n,1),o}const lo=()=>{let e=[];return{push:n=>{const l=e[0];l&&n!==l&&l.pause(),e=Ne(e,n),e.unshift(n)},remove:n=>{var l,i;e=Ne(e,n),(i=(l=e[0])==null?void 0:l.resume)==null||i.call(l)}}},io=(e,t=!1)=>{const o=document.activeElement;for(const n of e)if(z(n,t),document.activeElement!==o)return},Be=lo(),uo=()=>fe.value>we.value,le=()=>{Te.value="pointer",fe.value=window.performance.now()},$e=()=>{Te.value="keyboard",fe.value=window.performance.now()},co=()=>(G(()=>{ae===0&&(document.addEventListener("mousedown",le),document.addEventListener("touchstart",le),document.addEventListener("keydown",$e)),ae++}),V(()=>{ae--,ae<=0&&(document.removeEventListener("mousedown",le),document.removeEventListener("touchstart",le),document.removeEventListener("keydown",$e))}),{focusReason:Te,lastUserFocusTimestamp:fe,lastAutomatedFocusTimestamp:we}),ie=e=>new CustomEvent(to,{...oo,detail:e}),po=A({name:"ElFocusTrap",inheritAttrs:!1,props:{loop:Boolean,trapped:Boolean,focusTrapEl:Object,focusStartEl:{type:[Object,String],default:"first"}},emits:[ke,Me,"focusin","focusout","focusout-prevented","release-requested"],setup(e,{emit:t}){const o=F();let n,l;const{focusReason:i}=co();Mt(a=>{e.trapped&&!d.paused&&t("release-requested",a)});const d={paused:!1,pause(){this.paused=!0},resume(){this.paused=!1}},p=a=>{if(!e.loop&&!e.trapped||d.paused)return;const{key:m,altKey:w,ctrlKey:s,metaKey:E,currentTarget:S,shiftKey:h}=a,{loop:O}=e,K=m===de.tab&&!w&&!s&&!E,C=document.activeElement;if(K&&C){const _=S,[U,I]=so(_);if(U&&I){if(!h&&C===I){const k=ie({focusReason:i.value});t("focusout-prevented",k),k.defaultPrevented||(a.preventDefault(),O&&z(U,!0))}else if(h&&[U,_].includes(C)){const k=ie({focusReason:i.value});t("focusout-prevented",k),k.defaultPrevented||(a.preventDefault(),O&&z(I,!0))}}else if(C===_){const k=ie({focusReason:i.value});t("focusout-prevented",k),k.defaultPrevented||a.preventDefault()}}};x(no,{focusTrapRef:o,onKeydown:p}),R(()=>e.focusTrapEl,a=>{a&&(o.value=a)},{immediate:!0}),R([o],([a],[m])=>{a&&(a.addEventListener("keydown",p),a.addEventListener("focusin",v),a.addEventListener("focusout",g)),m&&(m.removeEventListener("keydown",p),m.removeEventListener("focusin",v),m.removeEventListener("focusout",g))});const u=a=>{t(ke,a)},c=a=>t(Me,a),v=a=>{const m=r(o);if(!m)return;const w=a.target,s=a.relatedTarget,E=w&&m.contains(w);e.trapped||s&&m.contains(s)||(n=s),E&&t("focusin",a),!d.paused&&e.trapped&&(E?l=w:z(l,!0))},g=a=>{const m=r(o);if(!(d.paused||!m))if(e.trapped){const w=a.relatedTarget;!ye(w)&&!m.contains(w)&&setTimeout(()=>{if(!d.paused&&e.trapped){const s=ie({focusReason:i.value});t("focusout-prevented",s),s.defaultPrevented||z(l,!0)}},0)}else{const w=a.target;w&&m.contains(w)||t("focusout",a)}};async function y(){await Pe();const a=r(o);if(a){Be.push(d);const m=a.contains(document.activeElement)?n:document.activeElement;if(n=m,!a.contains(m)){const s=new Event(ve,Oe);a.addEventListener(ve,u),a.dispatchEvent(s),s.defaultPrevented||Pe(()=>{let E=e.focusStartEl;ft(E)||(z(E),document.activeElement!==E&&(E="first")),E==="first"&&io(Xe(a),!0),(document.activeElement===m||E==="container")&&z(a)})}}}function T(){const a=r(o);if(a){a.removeEventListener(ve,u);const m=new CustomEvent(ge,{...Oe,detail:{focusReason:i.value}});a.addEventListener(ge,c),a.dispatchEvent(m),!m.defaultPrevented&&(i.value=="keyboard"||!uo()||a.contains(document.activeElement))&&z(n??document.body),a.removeEventListener(ge,u),Be.remove(d)}}return G(()=>{e.trapped&&y(),R(()=>e.trapped,a=>{a?y():T()})}),V(()=>{e.trapped&&T()}),{onKeydown:p}}});function fo(e,t,o,n,l,i){return Z(e.$slots,"default",{handleKeydown:e.onKeydown})}var vo=q(po,[["render",fo],["__file","/home/runner/work/element-plus/element-plus/packages/components/focus-trap/src/focus-trap.vue"]]);const go=["fixed","absolute"],mo=D({boundariesPadding:{type:Number,default:0},fallbackPlacements:{type:P(Array),default:void 0},gpuAcceleration:{type:Boolean,default:!0},offset:{type:Number,default:12},placement:{type:String,values:vt,default:"bottom"},popperOptions:{type:P(Object),default:()=>({})},strategy:{type:String,values:go,default:"absolute"}}),xe=D({...mo,id:String,style:{type:P([String,Array,Object])},className:{type:P([String,Array,Object])},effect:{type:String,default:"dark"},visible:Boolean,enterable:{type:Boolean,default:!0},pure:Boolean,focusOnShow:{type:Boolean,default:!1},trapping:{type:Boolean,default:!1},popperClass:{type:P([String,Array,Object])},popperStyle:{type:P([String,Array,Object])},referenceEl:{type:P(Object)},triggerTargetEl:{type:P(Object)},stopPopperMouseEvent:{type:Boolean,default:!0},ariaLabel:{type:String,default:void 0},virtualTriggering:Boolean,zIndex:Number}),bo={mouseenter:e=>e instanceof MouseEvent,mouseleave:e=>e instanceof MouseEvent,focus:()=>!0,blur:()=>!0,close:()=>!0},yo=(e,t=[])=>{const{placement:o,strategy:n,popperOptions:l}=e,i={placement:o,strategy:n,...l,modifiers:[...To(e),...t]};return wo(i,l==null?void 0:l.modifiers),i},Eo=e=>{if(Y)return He(e)};function To(e){const{offset:t,gpuAcceleration:o,fallbackPlacements:n}=e;return[{name:"offset",options:{offset:[0,t??12]}},{name:"preventOverflow",options:{padding:{top:2,bottom:2,left:5,right:5}}},{name:"flip",options:{padding:5,fallbackPlacements:n}},{name:"computeStyles",options:{gpuAcceleration:o}}]}function wo(e,t){t&&(e.modifiers=[...e.modifiers,...t??[]])}const ho=0,Co=e=>{const{popperInstanceRef:t,contentRef:o,triggerRef:n,role:l}=W(Ee,void 0),i=F(),d=F(),p=b(()=>({name:"eventListeners",enabled:!!e.visible})),u=b(()=>{var s;const E=r(i),S=(s=r(d))!=null?s:ho;return{name:"arrow",enabled:!St(E),options:{element:E,padding:S}}}),c=b(()=>({onFirstUpdate:()=>{a()},...yo(e,[r(u),r(p)])})),v=b(()=>Eo(e.referenceEl)||r(n)),{attributes:g,state:y,styles:T,update:a,forceUpdate:m,instanceRef:w}=It(v,o,c);return R(w,s=>t.value=s),G(()=>{R(()=>{var s;return(s=r(v))==null?void 0:s.getBoundingClientRect()},()=>{a()})}),{attributes:g,arrowRef:i,contentRef:o,instanceRef:w,state:y,styles:T,role:l,forceUpdate:m,update:a}},Fo=(e,{attributes:t,styles:o,role:n})=>{const{nextZIndex:l}=zt(),i=re("popper"),d=b(()=>r(t).popper),p=F(e.zIndex||l()),u=b(()=>[i.b(),i.is("pure",e.pure),i.is(e.effect),e.popperClass]),c=b(()=>[{zIndex:r(p)},e.popperStyle||{},r(o).popper]),v=b(()=>n.value==="dialog"?"false":void 0),g=b(()=>r(o).arrow||{});return{ariaModal:v,arrowStyle:g,contentAttrs:d,contentClass:u,contentStyle:c,contentZIndex:p,updateZIndex:()=>{p.value=e.zIndex||l()}}},Po=(e,t)=>{const o=F(!1),n=F();return{focusStartRef:n,trapped:o,onFocusAfterReleased:c=>{var v;((v=c.detail)==null?void 0:v.focusReason)!=="pointer"&&(n.value="first",t("blur"))},onFocusAfterTrapped:()=>{t("focus")},onFocusInTrap:c=>{e.visible&&!o.value&&(c.target&&(n.value=c.target),o.value=!0)},onFocusoutPrevented:c=>{e.trapping||(c.detail.focusReason==="pointer"&&c.preventDefault(),o.value=!1)},onReleaseRequested:()=>{o.value=!1,t("close")}}},Ro=A({name:"ElPopperContent"}),So=A({...Ro,props:xe,emits:bo,setup(e,{expose:t,emit:o}){const n=e,{focusStartRef:l,trapped:i,onFocusAfterReleased:d,onFocusAfterTrapped:p,onFocusInTrap:u,onFocusoutPrevented:c,onReleaseRequested:v}=Po(n,o),{attributes:g,arrowRef:y,contentRef:T,styles:a,instanceRef:m,role:w,update:s}=Co(n),{ariaModal:E,arrowStyle:S,contentAttrs:h,contentClass:O,contentStyle:K,updateZIndex:C}=Fo(n,{styles:a,attributes:g,role:w}),_=W(Re,void 0),U=F();x(We,{arrowStyle:S,arrowRef:y,arrowOffset:U}),_&&(_.addInputId||_.removeInputId)&&x(Re,{..._,addInputId:me,removeInputId:me});let I;const te=(N=!0)=>{s(),N&&C()},k=()=>{te(!1),n.visible&&n.focusOnShow?i.value=!0:n.visible===!1&&(i.value=!1)};return G(()=>{R(()=>n.triggerTargetEl,(N,j)=>{I==null||I(),I=void 0;const M=r(N||T.value),J=r(j||T.value);pe(M)&&(I=R([w,()=>n.ariaLabel,E,()=>n.id],f=>{["role","aria-label","aria-modal","id"].forEach((B,Ce)=>{ye(f[Ce])?M.removeAttribute(B):M.setAttribute(B,f[Ce])})},{immediate:!0})),J!==M&&pe(J)&&["role","aria-label","aria-modal","id"].forEach(f=>{J.removeAttribute(f)})},{immediate:!0}),R(()=>n.visible,k,{immediate:!0})}),V(()=>{I==null||I(),I=void 0}),t({popperContentRef:T,popperInstanceRef:m,updatePopper:te,contentStyle:K}),(N,j)=>(L(),ce("div",be({ref_key:"contentRef",ref:T},r(h),{style:r(K),class:r(O),tabindex:"-1",onMouseenter:j[0]||(j[0]=M=>N.$emit("mouseenter",M)),onMouseleave:j[1]||(j[1]=M=>N.$emit("mouseleave",M))}),[oe(r(vo),{trapped:r(i),"trap-on-focus-in":!0,"focus-trap-el":r(T),"focus-start-el":r(l),onFocusAfterTrapped:r(p),onFocusAfterReleased:r(d),onFocusin:r(u),onFocusoutPrevented:r(c),onReleaseRequested:r(v)},{default:H(()=>[Z(N.$slots,"default")]),_:3},8,["trapped","focus-trap-el","focus-start-el","onFocusAfterTrapped","onFocusAfterReleased","onFocusin","onFocusoutPrevented","onReleaseRequested"])],16))}});var _o=q(So,[["__file","/home/runner/work/element-plus/element-plus/packages/components/popper/src/content.vue"]]);const Ao=Ke(Vt),he=Symbol("elTooltip"),et=D({...Bt,...xe,appendTo:{type:P([String,Object])},content:{type:String,default:""},rawContent:{type:Boolean,default:!1},persistent:Boolean,ariaLabel:String,visible:{type:P(Boolean),default:null},transition:{type:String,default:""},teleported:{type:Boolean,default:!0},disabled:{type:Boolean}}),tt=D({...Qe,disabled:Boolean,trigger:{type:P([String,Array]),default:"hover"},triggerKeys:{type:P(Array),default:()=>[de.enter,de.space]}}),{useModelToggleProps:Io,useModelToggleEmits:Oo,useModelToggle:ko}=Ze("visible"),Mo=D({...Je,...Io,...et,...tt,...Ye,showArrow:{type:Boolean,default:!0}}),Lo=[...Oo,"before-show","before-hide","show","hide","open","close"],No=(e,t)=>gt(e)?e.includes(t):e===t,Q=(e,t,o)=>n=>{No(r(e),t)&&o(n)},Bo=A({name:"ElTooltipTrigger"}),$o=A({...Bo,props:tt,setup(e,{expose:t}){const o=e,n=re("tooltip"),{controlled:l,id:i,open:d,onOpen:p,onClose:u,onToggle:c}=W(he,void 0),v=F(null),g=()=>{if(r(l)||o.disabled)return!0},y=ue(o,"trigger"),T=$(g,Q(y,"hover",p)),a=$(g,Q(y,"hover",u)),m=$(g,Q(y,"click",h=>{h.button===0&&c(h)})),w=$(g,Q(y,"focus",p)),s=$(g,Q(y,"focus",u)),E=$(g,Q(y,"contextmenu",h=>{h.preventDefault(),c(h)})),S=$(g,h=>{const{code:O}=h;o.triggerKeys.includes(O)&&(h.preventDefault(),c(h))});return t({triggerRef:v}),(h,O)=>(L(),ee(r(eo),{id:r(i),"virtual-ref":h.virtualRef,open:r(d),"virtual-triggering":h.virtualTriggering,class:Ue(r(n).e("trigger")),onBlur:r(s),onClick:r(m),onContextmenu:r(E),onFocus:r(w),onMouseenter:r(T),onMouseleave:r(a),onKeydown:r(S)},{default:H(()=>[Z(h.$slots,"default")]),_:3},8,["id","virtual-ref","open","virtual-triggering","class","onBlur","onClick","onContextmenu","onFocus","onMouseenter","onMouseleave","onKeydown"]))}});var Do=q($o,[["__file","/home/runner/work/element-plus/element-plus/packages/components/tooltip/src/trigger.vue"]]);const Ko=A({name:"ElTooltipContent",inheritAttrs:!1}),Uo=A({...Ko,props:et,setup(e,{expose:t}){const o=e,{selector:n}=qe(),l=re("tooltip"),i=F(null),d=F(!1),{controlled:p,id:u,open:c,trigger:v,onClose:g,onOpen:y,onShow:T,onHide:a,onBeforeShow:m,onBeforeHide:w}=W(he,void 0),s=b(()=>o.transition||`${l.namespace.value}-fade-in-linear`),E=b(()=>o.persistent);V(()=>{d.value=!0});const S=b(()=>r(E)?!0:r(c)),h=b(()=>o.disabled?!1:r(c)),O=b(()=>o.appendTo||n.value),K=b(()=>{var f;return(f=o.style)!=null?f:{}}),C=b(()=>!r(c)),_=()=>{a()},U=()=>{if(r(p))return!0},I=$(U,()=>{o.enterable&&r(v)==="hover"&&y()}),te=$(U,()=>{r(v)==="hover"&&g()}),k=()=>{var f,B;(B=(f=i.value)==null?void 0:f.updatePopper)==null||B.call(f),m==null||m()},N=()=>{w==null||w()},j=()=>{T(),J=Pt(b(()=>{var f;return(f=i.value)==null?void 0:f.popperContentRef}),()=>{if(r(p))return;r(v)!=="hover"&&g()})},M=()=>{o.virtualTriggering||g()};let J;return R(()=>r(c),f=>{f||J==null||J()},{flush:"post"}),R(()=>o.content,()=>{var f,B;(B=(f=i.value)==null?void 0:f.updatePopper)==null||B.call(f)}),t({contentRef:i}),(f,B)=>(L(),ee(yt,{disabled:!f.teleported,to:r(O)},[oe(bt,{name:r(s),onAfterLeave:_,onBeforeEnter:k,onAfterEnter:j,onBeforeLeave:N},{default:H(()=>[r(S)?je((L(),ee(r(_o),be({key:0,id:r(u),ref_key:"contentRef",ref:i},f.$attrs,{"aria-label":f.ariaLabel,"aria-hidden":r(C),"boundaries-padding":f.boundariesPadding,"fallback-placements":f.fallbackPlacements,"gpu-acceleration":f.gpuAcceleration,offset:f.offset,placement:f.placement,"popper-options":f.popperOptions,strategy:f.strategy,effect:f.effect,enterable:f.enterable,pure:f.pure,"popper-class":f.popperClass,"popper-style":[f.popperStyle,r(K)],"reference-el":f.referenceEl,"trigger-target-el":f.triggerTargetEl,visible:r(h),"z-index":f.zIndex,onMouseenter:r(I),onMouseleave:r(te),onBlur:M,onClose:r(g)}),{default:H(()=>[d.value?ne("v-if",!0):Z(f.$slots,"default",{key:0})]),_:3},16,["id","aria-label","aria-hidden","boundaries-padding","fallback-placements","gpu-acceleration","offset","placement","popper-options","strategy","effect","enterable","pure","popper-class","popper-style","reference-el","trigger-target-el","visible","z-index","onMouseenter","onMouseleave","onClose"])),[[mt,r(h)]]):ne("v-if",!0)]),_:3},8,["name"])],8,["disabled","to"]))}});var jo=q(Uo,[["__file","/home/runner/work/element-plus/element-plus/packages/components/tooltip/src/content.vue"]]);const zo=["innerHTML"],Ho={key:1},Zo=A({name:"ElTooltip"}),qo=A({...Zo,props:Mo,emits:Lo,setup(e,{expose:t,emit:o}){const n=e;Nt();const l=Rt(),i=F(),d=F(),p=()=>{var s;const E=r(i);E&&((s=E.popperInstanceRef)==null||s.update())},u=F(!1),c=F(),{show:v,hide:g,hasUpdateHandler:y}=ko({indicator:u,toggleReason:c}),{onOpen:T,onClose:a}=$t({showAfter:ue(n,"showAfter"),hideAfter:ue(n,"hideAfter"),open:v,close:g}),m=b(()=>ze(n.visible)&&!y.value);x(he,{controlled:m,id:l,open:Et(u),trigger:ue(n,"trigger"),onOpen:s=>{T(s)},onClose:s=>{a(s)},onToggle:s=>{r(u)?a(s):T(s)},onShow:()=>{o("show",c.value)},onHide:()=>{o("hide",c.value)},onBeforeShow:()=>{o("before-show",c.value)},onBeforeHide:()=>{o("before-hide",c.value)},updatePopper:p}),R(()=>n.disabled,s=>{s&&u.value&&(u.value=!1)});const w=()=>{var s,E;const S=(E=(s=d.value)==null?void 0:s.contentRef)==null?void 0:E.popperContentRef;return S&&S.contains(document.activeElement)};return Tt(()=>u.value&&g()),t({popperRef:i,contentRef:d,isFocusInsideContent:w,updatePopper:p,onOpen:T,onClose:a,hide:g}),(s,E)=>(L(),ee(r(Ao),{ref_key:"popperRef",ref:i,role:s.role},{default:H(()=>[oe(Do,{disabled:s.disabled,trigger:s.trigger,"trigger-keys":s.triggerKeys,"virtual-ref":s.virtualRef,"virtual-triggering":s.virtualTriggering},{default:H(()=>[s.$slots.default?Z(s.$slots,"default",{key:0}):ne("v-if",!0)]),_:3},8,["disabled","trigger","trigger-keys","virtual-ref","virtual-triggering"]),oe(jo,{ref_key:"contentRef",ref:d,"aria-label":s.ariaLabel,"boundaries-padding":s.boundariesPadding,content:s.content,disabled:s.disabled,effect:s.effect,enterable:s.enterable,"fallback-placements":s.fallbackPlacements,"hide-after":s.hideAfter,"gpu-acceleration":s.gpuAcceleration,offset:s.offset,persistent:s.persistent,"popper-class":s.popperClass,"popper-style":s.popperStyle,placement:s.placement,"popper-options":s.popperOptions,pure:s.pure,"raw-content":s.rawContent,"reference-el":s.referenceEl,"trigger-target-el":s.triggerTargetEl,"show-after":s.showAfter,strategy:s.strategy,teleported:s.teleported,transition:s.transition,"virtual-triggering":s.virtualTriggering,"z-index":s.zIndex,"append-to":s.appendTo},{default:H(()=>[Z(s.$slots,"content",{},()=>[s.rawContent?(L(),ce("span",{key:0,innerHTML:s.content},null,8,zo)):(L(),ce("span",Ho,wt(s.content),1))]),s.showArrow?(L(),ee(r(Yt),{key:0,"arrow-offset":s.arrowOffset},null,8,["arrow-offset"])):ne("v-if",!0)]),_:3},8,["aria-label","boundaries-padding","content","disabled","effect","enterable","fallback-placements","hide-after","gpu-acceleration","offset","persistent","popper-class","popper-style","placement","popper-options","pure","raw-content","reference-el","trigger-target-el","show-after","strategy","teleported","transition","virtual-triggering","z-index","append-to"])]),_:3},8,["role"]))}});var Vo=q(qo,[["__file","/home/runner/work/element-plus/element-plus/packages/components/tooltip/src/tooltip.vue"]]);const Qo=Ke(Vo);export{Qo as E,he as T,et as a,de as b,tt as u};