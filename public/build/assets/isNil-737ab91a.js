import{A as m,w as A,i as Q,B as R,C as L,D as T,E as C,F as j}from"./icon-be9b5ac7.js";import{d as N,k,E as y}from"./app-8047a45c.js";function O(e){var n;const t=m(e);return(n=t==null?void 0:t.$el)!=null?n:t}const _=Q?window:void 0;function b(...e){let n,t,r,i;if(T(e[0])||Array.isArray(e[0])?([t,r,i]=e,n=_):[n,t,r,i]=e,!n)return C;Array.isArray(t)||(t=[t]),Array.isArray(r)||(r=[r]);const c=[],o=()=>{c.forEach(l=>l()),c.length=0},u=(l,p,s,a)=>(l.addEventListener(p,s,a),()=>l.removeEventListener(p,s,a)),f=y(()=>[O(n),m(i)],([l,p])=>{o(),l&&c.push(...t.flatMap(s=>r.map(a=>u(l,s,a,p))))},{immediate:!0,flush:"post"}),v=()=>{f(),o()};return A(v),v}let I=!1;function q(e,n,t={}){const{window:r=_,ignore:i=[],capture:c=!0,detectIframe:o=!1}=t;if(!r)return;j&&!I&&(I=!0,Array.from(r.document.body.children).forEach(s=>s.addEventListener("click",C)));let u=!0;const f=s=>i.some(a=>{if(typeof a=="string")return Array.from(r.document.querySelectorAll(a)).some(d=>d===s.target||s.composedPath().includes(d));{const d=O(a);return d&&(s.target===d||s.composedPath().includes(d))}}),l=[b(r,"click",s=>{const a=O(e);if(!(!a||a===s.target||s.composedPath().includes(a))){if(s.detail===0&&(u=!f(s)),!u){u=!0;return}n(s)}},{passive:!0,capture:c}),b(r,"pointerdown",s=>{const a=O(e);a&&(u=!s.composedPath().includes(a)&&!f(s))},{passive:!0}),o&&b(r,"blur",s=>{var a;const d=O(e);((a=r.document.activeElement)==null?void 0:a.tagName)==="IFRAME"&&!(d!=null&&d.contains(r.document.activeElement))&&n(s)})].filter(Boolean);return()=>l.forEach(s=>s())}function B(e,n=!1){const t=N(),r=()=>t.value=Boolean(e());return r(),R(r,n),t}const E=typeof globalThis<"u"?globalThis:typeof window<"u"?window:typeof global<"u"?global:typeof self<"u"?self:{},h="__vueuse_ssr_handlers__";E[h]=E[h]||{};function D(e,n,{window:t=_,initialValue:r=""}={}){const i=N(r),c=k(()=>{var o;return O(n)||((o=t==null?void 0:t.document)==null?void 0:o.documentElement)});return y([c,()=>m(e)],([o,u])=>{var f;if(o&&t){const v=(f=t.getComputedStyle(o).getPropertyValue(u))==null?void 0:f.trim();i.value=v||r}},{immediate:!0}),y(i,o=>{var u;(u=c.value)!=null&&u.style&&c.value.style.setProperty(m(e),o)}),i}var g=Object.getOwnPropertySymbols,W=Object.prototype.hasOwnProperty,x=Object.prototype.propertyIsEnumerable,$=(e,n)=>{var t={};for(var r in e)W.call(e,r)&&n.indexOf(r)<0&&(t[r]=e[r]);if(e!=null&&g)for(var r of g(e))n.indexOf(r)<0&&x.call(e,r)&&(t[r]=e[r]);return t};function K(e,n,t={}){const r=t,{window:i=_}=r,c=$(r,["window"]);let o;const u=B(()=>i&&"ResizeObserver"in i),f=()=>{o&&(o.disconnect(),o=void 0)},v=y(()=>O(e),p=>{f(),u.value&&i&&p&&(o=new ResizeObserver(n),o.observe(p,c))},{immediate:!0,flush:"post"}),l=()=>{f(),v()};return A(l),{isSupported:u,stop:l}}var P;(function(e){e.UP="UP",e.RIGHT="RIGHT",e.DOWN="DOWN",e.LEFT="LEFT",e.NONE="NONE"})(P||(P={}));var F=Object.defineProperty,w=Object.getOwnPropertySymbols,z=Object.prototype.hasOwnProperty,M=Object.prototype.propertyIsEnumerable,S=(e,n,t)=>n in e?F(e,n,{enumerable:!0,configurable:!0,writable:!0,value:t}):e[n]=t,U=(e,n)=>{for(var t in n||(n={}))z.call(n,t)&&S(e,t,n[t]);if(w)for(var t of w(n))M.call(n,t)&&S(e,t,n[t]);return e};const G={easeInSine:[.12,0,.39,0],easeOutSine:[.61,1,.88,1],easeInOutSine:[.37,0,.63,1],easeInQuad:[.11,0,.5,0],easeOutQuad:[.5,1,.89,1],easeInOutQuad:[.45,0,.55,1],easeInCubic:[.32,0,.67,0],easeOutCubic:[.33,1,.68,1],easeInOutCubic:[.65,0,.35,1],easeInQuart:[.5,0,.75,0],easeOutQuart:[.25,1,.5,1],easeInOutQuart:[.76,0,.24,1],easeInQuint:[.64,0,.78,0],easeOutQuint:[.22,1,.36,1],easeInOutQuint:[.83,0,.17,1],easeInExpo:[.7,0,.84,0],easeOutExpo:[.16,1,.3,1],easeInOutExpo:[.87,0,.13,1],easeInCirc:[.55,0,1,.45],easeOutCirc:[0,.55,.45,1],easeInOutCirc:[.85,0,.15,1],easeInBack:[.36,0,.66,-.56],easeOutBack:[.34,1.56,.64,1],easeInOutBack:[.68,-.6,.32,1.6]};U({linear:L},G);function J(e){return e==null}export{K as a,D as b,O as c,J as i,q as o,b as u};