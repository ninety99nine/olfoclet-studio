import{r as O,t as j,c as Q,g as R,h as T,j as $,n as N,k}from"./icon-20febbf5.js";import{d as x,k as A,z as y,u as K,Q as L,K as W}from"./app-6e19013a.js";import{e as B}from"./base-d87a8159.js";function v(e){var r;const t=O(e);return(r=t==null?void 0:t.$el)!=null?r:t}const I=Q?window:void 0;function _(...e){let r,t,n,i;if($(e[0])||Array.isArray(e[0])?([t,n,i]=e,r=I):[r,t,n,i]=e,!r)return N;Array.isArray(t)||(t=[t]),Array.isArray(n)||(n=[n]);const c=[],o=()=>{c.forEach(l=>l()),c.length=0},u=(l,p,s,a)=>(l.addEventListener(p,s,a),()=>l.removeEventListener(p,s,a)),f=y(()=>[v(r),O(i)],([l,p])=>{o(),l&&c.push(...t.flatMap(s=>n.map(a=>u(l,s,a,p))))},{immediate:!0,flush:"post"}),m=()=>{f(),o()};return j(m),m}let b=!1;function te(e,r,t={}){const{window:n=I,ignore:i=[],capture:c=!0,detectIframe:o=!1}=t;if(!n)return;k&&!b&&(b=!0,Array.from(n.document.body.children).forEach(s=>s.addEventListener("click",N)));let u=!0;const f=s=>i.some(a=>{if(typeof a=="string")return Array.from(n.document.querySelectorAll(a)).some(d=>d===s.target||s.composedPath().includes(d));{const d=v(a);return d&&(s.target===d||s.composedPath().includes(d))}}),l=[_(n,"click",s=>{const a=v(e);if(!(!a||a===s.target||s.composedPath().includes(a))){if(s.detail===0&&(u=!f(s)),!u){u=!0;return}r(s)}},{passive:!0,capture:c}),_(n,"pointerdown",s=>{const a=v(e);a&&(u=!s.composedPath().includes(a)&&!f(s))},{passive:!0}),o&&_(n,"blur",s=>{var a;const d=v(e);((a=n.document.activeElement)==null?void 0:a.tagName)==="IFRAME"&&!(d!=null&&d.contains(n.document.activeElement))&&r(s)})].filter(Boolean);return()=>l.forEach(s=>s())}function M(e,r=!1){const t=x(),n=()=>t.value=Boolean(e());return n(),R(n,r),t}const h=typeof globalThis<"u"?globalThis:typeof window<"u"?window:typeof global<"u"?global:typeof self<"u"?self:{},E="__vueuse_ssr_handlers__";h[E]=h[E]||{};function ne(e,r,{window:t=I,initialValue:n=""}={}){const i=x(n),c=A(()=>{var o;return v(r)||((o=t==null?void 0:t.document)==null?void 0:o.documentElement)});return y([c,()=>O(e)],([o,u])=>{var f;if(o&&t){const m=(f=t.getComputedStyle(o).getPropertyValue(u))==null?void 0:f.trim();i.value=m||n}},{immediate:!0}),y(i,o=>{var u;(u=c.value)!=null&&u.style&&c.value.style.setProperty(O(e),o)}),i}var g=Object.getOwnPropertySymbols,z=Object.prototype.hasOwnProperty,F=Object.prototype.propertyIsEnumerable,G=(e,r)=>{var t={};for(var n in e)z.call(e,n)&&r.indexOf(n)<0&&(t[n]=e[n]);if(e!=null&&g)for(var n of g(e))r.indexOf(n)<0&&F.call(e,n)&&(t[n]=e[n]);return t};function re(e,r,t={}){const n=t,{window:i=I}=n,c=G(n,["window"]);let o;const u=M(()=>i&&"ResizeObserver"in i),f=()=>{o&&(o.disconnect(),o=void 0)},m=y(()=>v(e),p=>{f(),u.value&&i&&p&&(o=new ResizeObserver(r),o.observe(p,c))},{immediate:!0,flush:"post"}),l=()=>{f(),m()};return j(l),{isSupported:u,stop:l}}var P;(function(e){e.UP="UP",e.RIGHT="RIGHT",e.DOWN="DOWN",e.LEFT="LEFT",e.NONE="NONE"})(P||(P={}));var U=Object.defineProperty,w=Object.getOwnPropertySymbols,D=Object.prototype.hasOwnProperty,H=Object.prototype.propertyIsEnumerable,C=(e,r,t)=>r in e?U(e,r,{enumerable:!0,configurable:!0,writable:!0,value:t}):e[r]=t,V=(e,r)=>{for(var t in r||(r={}))D.call(r,t)&&C(e,t,r[t]);if(w)for(var t of w(r))H.call(r,t)&&C(e,t,r[t]);return e};const q={easeInSine:[.12,0,.39,0],easeOutSine:[.61,1,.88,1],easeInOutSine:[.37,0,.63,1],easeInQuad:[.11,0,.5,0],easeOutQuad:[.5,1,.89,1],easeInOutQuad:[.45,0,.55,1],easeInCubic:[.32,0,.67,0],easeOutCubic:[.33,1,.68,1],easeInOutCubic:[.65,0,.35,1],easeInQuart:[.5,0,.75,0],easeOutQuart:[.25,1,.5,1],easeInOutQuart:[.76,0,.24,1],easeInQuint:[.64,0,.78,0],easeOutQuint:[.22,1,.36,1],easeInOutQuint:[.83,0,.17,1],easeInExpo:[.7,0,.84,0],easeOutExpo:[.16,1,.3,1],easeInOutExpo:[.87,0,.13,1],easeInCirc:[.55,0,1,.45],easeOutCirc:[0,.55,.45,1],easeInOutCirc:[.85,0,.15,1],easeInBack:[.36,0,.66,-.56],easeOutBack:[.34,1.56,.64,1],easeInOutBack:[.68,-.6,.32,1.6]};V({linear:T},q);function se(e){return e==null}const S={prefix:Math.floor(Math.random()*1e4),current:0},J=Symbol("elIdInjection"),Y=()=>L()?W(J,S):S,oe=e=>{const r=Y(),t=B();return A(()=>K(e)||`${t.value}-id-${r.prefix}-${r.current++}`)},ae=Symbol("formContextKey"),ue=Symbol("formItemContextKey");export{v as a,oe as b,ae as c,re as d,ne as e,ue as f,_ as g,se as i,te as o,Y as u};
