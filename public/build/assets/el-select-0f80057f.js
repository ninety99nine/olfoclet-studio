import{K as Ve,k as f,bd as Pe,z as F,u as te,Q as il,s as ge,aX as ke,M as al,aK as Ml,q as T,l as be,A as rl,o as m,e as V,r as ae,a as z,t as X,n as g,i as J,d as q,B as sl,y as ie,b7 as fl,bC as _,bg as he,bJ as ol,G as vl,bA as Sl,bE as $l,L as Ol,x as U,a$ as Vl,b as oe,w as L,c as K,F as tl,h as ml,f as D,T as Pl,bw as B,bp as kl,ad as zl,P as hl}from"./app-6e19013a.js";import{d as wl}from"./constants-481c1ee8.js";import{j as Dl,u as Bl,b as ql,g as Al,U as Y,i as Fl,C as El,a as Wl,E as Kl,h as Hl}from"./el-tag-f7980671.js";import{b as Rl,E as Nl,a as jl}from"./el-popper-100bb3f5.js";import{I as A,J as Ql,u as ul,i as bl,d as gl,H as Gl,E as Ul,C as Jl}from"./el-scrollbar-298c55cf.js";import{E as Xl}from"./index-fe677398.js";import{c as Yl,e as Zl,i as yl,o as xl,x as _l}from"./icon-20febbf5.js";import{u as re,_ as ze,w as en,a as Ll}from"./base-d87a8159.js";import{u as ln,i as nn}from"./el-switch-1853c3da.js";const on=e=>Dl[e||"default"],tn=e=>({focus:()=>{var l,i;(i=(l=e.value)==null?void 0:l.focus)==null||i.call(l)}}),Il=Symbol("ElSelectGroup"),De=Symbol("ElSelect");function an(e,l){const i=Ve(De),h=Ve(Il,{disabled:!1}),y=f(()=>Object.prototype.toString.call(e.value).toLowerCase()==="[object object]"),d=f(()=>i.props.multiple?v(i.props.modelValue,e.value):S(e.value,i.props.modelValue)),r=f(()=>{if(i.props.multiple){const t=i.props.modelValue||[];return!d.value&&t.length>=i.props.multipleLimit&&i.props.multipleLimit>0}else return!1}),u=f(()=>e.label||(y.value?"":e.value)),b=f(()=>e.value||e.label||""),C=f(()=>e.disabled||l.groupDisabled||r.value),p=il(),v=(t=[],c)=>{if(y.value){const O=i.props.valueKey;return t&&t.some(H=>Pe(A(H,O))===A(c,O))}else return t&&t.includes(c)},S=(t,c)=>{if(y.value){const{valueKey:O}=i.props;return A(t,O)===A(c,O)}else return t===c},w=()=>{!e.disabled&&!h.disabled&&(i.hoverIndex=i.optionsArray.indexOf(p.proxy))};F(()=>u.value,()=>{!e.created&&!i.props.remote&&i.setSelected()}),F(()=>e.value,(t,c)=>{const{remote:O,valueKey:H}=i.props;if(Object.is(t,c)||(i.onOptionDestroy(c,p.proxy),i.onOptionCreate(p.proxy)),!e.created&&!O){if(H&&typeof t=="object"&&typeof c=="object"&&t[H]===c[H])return;i.setSelected()}}),F(()=>h.disabled,()=>{l.groupDisabled=h.disabled},{immediate:!0});const{queryChange:P}=Pe(i);return F(P,t=>{const{query:c}=te(t),O=new RegExp(Ql(c),"i");l.visible=O.test(u.value)||e.created,l.visible||i.filteredOptionsCount--},{immediate:!0}),{select:i,currentLabel:u,currentValue:b,itemSelected:d,isDisabled:C,hoverItem:w}}const rn=ge({name:"ElOption",componentName:"ElOption",props:{value:{required:!0,type:[String,Number,Boolean,Object]},label:[String,Number],created:Boolean,disabled:{type:Boolean,default:!1}},setup(e){const l=re("select"),i=ke({index:-1,groupDisabled:!1,visible:!0,hitState:!1,hover:!1}),{currentLabel:h,itemSelected:y,isDisabled:d,select:r,hoverItem:u}=an(e,i),{visible:b,hover:C}=al(i),p=il().proxy;r.onOptionCreate(p),Ml(()=>{const S=p.value,{selected:w}=r,t=(r.props.multiple?w:[w]).some(c=>c.value===p.value);T(()=>{r.cachedOptions.get(S)===p&&!t&&r.cachedOptions.delete(S)}),r.onOptionDestroy(S,p)});function v(){e.disabled!==!0&&i.groupDisabled!==!0&&r.handleOptionSelect(p,!0)}return{ns:l,currentLabel:h,itemSelected:y,isDisabled:d,select:r,hoverItem:u,visible:b,hover:C,selectOptionClick:v,states:i}}});function sn(e,l,i,h,y,d){return be((m(),V("li",{class:g([e.ns.be("dropdown","item"),e.ns.is("disabled",e.isDisabled),{selected:e.itemSelected,hover:e.hover}]),onMouseenter:l[0]||(l[0]=(...r)=>e.hoverItem&&e.hoverItem(...r)),onClick:l[1]||(l[1]=J((...r)=>e.selectOptionClick&&e.selectOptionClick(...r),["stop"]))},[ae(e.$slots,"default",{},()=>[z("span",null,X(e.currentLabel),1)])],34)),[[rl,e.visible]])}var dl=ze(rn,[["render",sn],["__file","/home/runner/work/element-plus/element-plus/packages/components/select/src/option.vue"]]);const un=ge({name:"ElSelectDropdown",componentName:"ElSelectDropdown",setup(){const e=Ve(De),l=re("select"),i=f(()=>e.props.popperClass),h=f(()=>e.props.multiple),y=f(()=>e.props.fitInputWidth),d=q("");function r(){var u;d.value=`${(u=e.selectWrapper)==null?void 0:u.offsetWidth}px`}return sl(()=>{r(),wl(e.selectWrapper,r)}),{ns:l,minWidth:d,popperClass:i,isMultiple:h,isFitInputWidth:y}}});function dn(e,l,i,h,y,d){return m(),V("div",{class:g([e.ns.b("dropdown"),e.ns.is("multiple",e.isMultiple),e.popperClass]),style:ie({[e.isFitInputWidth?"width":"minWidth"]:e.minWidth})},[ae(e.$slots,"default")],6)}var cn=ze(un,[["render",dn],["__file","/home/runner/work/element-plus/element-plus/packages/components/select/src/select-dropdown.vue"]]);function pn(e){const{t:l}=ul();return ke({options:new Map,cachedOptions:new Map,createdLabel:null,createdSelected:!1,selected:e.multiple?[]:{},inputLength:20,inputWidth:0,optionsCount:0,filteredOptionsCount:0,visible:!1,softFocus:!1,selectedLabel:"",hoverIndex:-1,query:"",previousQuery:null,inputHovering:!1,cachedPlaceHolder:"",currentPlaceholder:l("el.select.placeholder"),menuVisibleOnFocus:!1,isOnComposition:!1,isSilentBlur:!1,prefixWidth:11,tagInMultiLine:!1,mouseEnter:!1})}const fn=(e,l,i)=>{const{t:h}=ul(),y=re("select");ln({from:"suffixTransition",replacement:"override style scheme",version:"2.3.0",scope:"props",ref:"https://element-plus.org/en-US/component/select.html#select-attributes"},f(()=>e.suffixTransition===!1));const d=q(null),r=q(null),u=q(null),b=q(null),C=q(null),p=q(null),v=q(-1),S=fl({query:""}),w=fl(""),P=q([]);let t=0;const{form:c,formItem:O}=Bl(),H=f(()=>!e.filterable||e.multiple||!l.visible),j=f(()=>e.disabled||(c==null?void 0:c.disabled)),ye=f(()=>{const n=e.multiple?Array.isArray(e.modelValue)&&e.modelValue.length>0:e.modelValue!==void 0&&e.modelValue!==null&&e.modelValue!=="";return e.clearable&&!j.value&&l.inputHovering&&n}),Ce=f(()=>e.remote&&e.filterable&&!e.remoteShowSuffix?"":e.suffixIcon),Be=f(()=>y.is("reverse",Ce.value&&l.visible&&e.suffixTransition)),Se=f(()=>e.remote?300:0),se=f(()=>e.loading?e.loadingText||h("el.select.loading"):e.remote&&l.query===""&&l.options.size===0?!1:e.filterable&&l.query&&l.options.size>0&&l.filteredOptionsCount===0?e.noMatchText||h("el.select.noMatch"):l.options.size===0?e.noDataText||h("el.select.noData"):null),I=f(()=>{const n=Array.from(l.options.values()),o=[];return P.value.forEach(a=>{const s=n.findIndex(E=>E.currentLabel===a);s>-1&&o.push(n[s])}),o.length?o:n}),qe=f(()=>Array.from(l.cachedOptions.values())),Ae=f(()=>{const n=I.value.filter(o=>!o.created).some(o=>o.currentLabel===l.query);return e.filterable&&e.allowCreate&&l.query!==""&&!n}),ee=ql(),Fe=f(()=>["small"].includes(ee.value)?"small":"default"),We=f({get(){return l.visible&&se.value!==!1},set(n){l.visible=n}});F([()=>j.value,()=>ee.value,()=>c==null?void 0:c.size],()=>{T(()=>{W()})}),F(()=>e.placeholder,n=>{l.cachedPlaceHolder=l.currentPlaceholder=n}),F(()=>e.modelValue,(n,o)=>{e.multiple&&(W(),n&&n.length>0||r.value&&l.query!==""?l.currentPlaceholder="":l.currentPlaceholder=l.cachedPlaceHolder,e.filterable&&!e.reserveKeyword&&(l.query="",Q(l.query))),ue(),e.filterable&&!e.multiple&&(l.inputLength=20),!bl(n,o)&&e.validateEvent&&(O==null||O.validate("change").catch(a=>Al()))},{flush:"post",deep:!0}),F(()=>l.visible,n=>{var o,a,s;n?((a=(o=u.value)==null?void 0:o.updatePopper)==null||a.call(o),e.filterable&&(l.filteredOptionsCount=l.optionsCount,l.query=e.remote?"":l.selectedLabel,e.multiple?(s=r.value)==null||s.focus():l.selectedLabel&&(l.currentPlaceholder=`${l.selectedLabel}`,l.selectedLabel=""),Q(l.query),!e.multiple&&!e.remote&&(S.value.query="",he(S),he(w)))):(e.filterable&&(_(e.filterMethod)&&e.filterMethod(""),_(e.remoteMethod)&&e.remoteMethod("")),r.value&&r.value.blur(),l.query="",l.previousQuery=null,l.selectedLabel="",l.inputLength=20,l.menuVisibleOnFocus=!1,Ke(),T(()=>{r.value&&r.value.value===""&&l.selected.length===0&&(l.currentPlaceholder=l.cachedPlaceHolder)}),e.multiple||(l.selected&&(e.filterable&&e.allowCreate&&l.createdSelected&&l.createdLabel?l.selectedLabel=l.createdLabel:l.selectedLabel=l.selected.currentLabel,e.filterable&&(l.query=l.selectedLabel)),e.filterable&&(l.currentPlaceholder=l.cachedPlaceHolder))),i.emit("visible-change",n)}),F(()=>l.options.entries(),()=>{var n,o,a;if(!Yl)return;(o=(n=u.value)==null?void 0:n.updatePopper)==null||o.call(n),e.multiple&&W();const s=((a=C.value)==null?void 0:a.querySelectorAll("input"))||[];Array.from(s).includes(document.activeElement)||ue(),e.defaultFirstOption&&(e.filterable||e.remote)&&l.filteredOptionsCount&&we()},{flush:"post"}),F(()=>l.hoverIndex,n=>{Zl(n)&&n>-1?v.value=I.value[n]||{}:v.value={},I.value.forEach(o=>{o.hover=v.value===o})});const W=()=>{e.collapseTags&&!e.filterable||T(()=>{var n,o;if(!d.value)return;const a=d.value.$el.querySelector("input");t=t||(a.clientHeight>0?a.clientHeight+2:0);const s=b.value,E=on(ee.value||(c==null?void 0:c.size)),M=E===t||t<=0?E:t;!(a.offsetParent===null)&&(a.style.height=`${(l.selected.length===0?M:Math.max(s?s.clientHeight+(s.clientHeight>M?6:0):0,M))-2}px`),l.tagInMultiLine=Number.parseFloat(a.style.height)>=M,l.visible&&se.value!==!1&&((o=(n=u.value)==null?void 0:n.updatePopper)==null||o.call(n))})},Q=async n=>{if(!(l.previousQuery===n||l.isOnComposition)){if(l.previousQuery===null&&(_(e.filterMethod)||_(e.remoteMethod))){l.previousQuery=n;return}l.previousQuery=n,T(()=>{var o,a;l.visible&&((a=(o=u.value)==null?void 0:o.updatePopper)==null||a.call(o))}),l.hoverIndex=-1,e.multiple&&e.filterable&&T(()=>{const o=r.value.value.length*15+20;l.inputLength=e.collapseTags?Math.min(50,o):o,Oe(),W()}),e.remote&&_(e.remoteMethod)?(l.hoverIndex=-1,e.remoteMethod(n)):_(e.filterMethod)?(e.filterMethod(n),he(w)):(l.filteredOptionsCount=l.optionsCount,S.value.query=n,he(S),he(w)),e.defaultFirstOption&&(e.filterable||e.remote)&&l.filteredOptionsCount&&(await T(),we())}},Oe=()=>{l.currentPlaceholder!==""&&(l.currentPlaceholder=r.value.value?"":l.cachedPlaceHolder)},we=()=>{const n=I.value.filter(s=>s.visible&&!s.disabled&&!s.states.groupDisabled),o=n.find(s=>s.created),a=n[0];l.hoverIndex=Ie(I.value,o||a)},ue=()=>{var n;if(e.multiple)l.selectedLabel="";else{const a=Ee(e.modelValue);(n=a.props)!=null&&n.created?(l.createdLabel=a.props.value,l.createdSelected=!0):l.createdSelected=!1,l.selectedLabel=a.currentLabel,l.selected=a,e.filterable&&(l.query=l.selectedLabel);return}const o=[];Array.isArray(e.modelValue)&&e.modelValue.forEach(a=>{o.push(Ee(a))}),l.selected=o,T(()=>{W()})},Ee=n=>{let o;const a=ol(n).toLowerCase()==="object",s=ol(n).toLowerCase()==="null",E=ol(n).toLowerCase()==="undefined";for(let N=l.cachedOptions.size-1;N>=0;N--){const k=qe.value[N];if(a?A(k.value,e.valueKey)===A(n,e.valueKey):k.value===n){o={value:n,currentLabel:k.currentLabel,isDisabled:k.isDisabled};break}}if(o)return o;const M=a?n.label:!s&&!E?n:"",R={value:n,currentLabel:M};return e.multiple&&(R.hitState=!1),R},Ke=()=>{setTimeout(()=>{const n=e.valueKey;e.multiple?l.selected.length>0?l.hoverIndex=Math.min.apply(null,l.selected.map(o=>I.value.findIndex(a=>A(a,n)===A(o,n)))):l.hoverIndex=-1:l.hoverIndex=I.value.findIndex(o=>ve(o)===ve(l.selected))},300)},He=()=>{var n,o;Re(),(o=(n=u.value)==null?void 0:n.updatePopper)==null||o.call(n),e.multiple&&W()},Re=()=>{var n;l.inputWidth=(n=d.value)==null?void 0:n.$el.offsetWidth},Ne=()=>{e.filterable&&l.query!==l.selectedLabel&&(l.query=l.selectedLabel,Q(l.query))},je=gl(()=>{Ne()},Se.value),Qe=gl(n=>{Q(n.target.value)},Se.value),Z=n=>{bl(e.modelValue,n)||i.emit(El,n)},Ge=n=>{if(n.target.value.length<=0&&!pe()){const o=e.modelValue.slice();o.pop(),i.emit(Y,o),Z(o)}n.target.value.length===1&&e.modelValue.length===0&&(l.currentPlaceholder=l.cachedPlaceHolder)},le=(n,o)=>{const a=l.selected.indexOf(o);if(a>-1&&!j.value){const s=e.modelValue.slice();s.splice(a,1),i.emit(Y,s),Z(s),i.emit("remove-tag",o.value)}n.stopPropagation()},Le=n=>{n.stopPropagation();const o=e.multiple?[]:"";if(!Sl(o))for(const a of l.selected)a.isDisabled&&o.push(a.value);i.emit(Y,o),Z(o),l.hoverIndex=-1,l.visible=!1,i.emit("clear")},de=(n,o)=>{var a;if(e.multiple){const s=(e.modelValue||[]).slice(),E=Ie(s,n.value);E>-1?s.splice(E,1):(e.multipleLimit<=0||s.length<e.multipleLimit)&&s.push(n.value),i.emit(Y,s),Z(s),n.created&&(l.query="",Q(""),l.inputLength=20),e.filterable&&((a=r.value)==null||a.focus())}else i.emit(Y,n.value),Z(n.value),l.visible=!1;l.isSilentBlur=o,ce(),!l.visible&&T(()=>{ne(n)})},Ie=(n=[],o)=>{if(!vl(o))return n.indexOf(o);const a=e.valueKey;let s=-1;return n.some((E,M)=>Pe(A(E,a))===A(o,a)?(s=M,!0):!1),s},ce=()=>{l.softFocus=!0;const n=r.value||d.value;n&&(n==null||n.focus())},ne=n=>{var o,a,s,E,M;const R=Array.isArray(n)?n[0]:n;let N=null;if(R!=null&&R.value){const k=I.value.filter(nl=>nl.value===R.value);k.length>0&&(N=k[0].$el)}if(u.value&&N){const k=(E=(s=(a=(o=u.value)==null?void 0:o.popperRef)==null?void 0:a.contentRef)==null?void 0:s.querySelector)==null?void 0:E.call(s,`.${y.be("dropdown","wrap")}`);k&&Gl(k,N)}(M=p.value)==null||M.handleScroll()},Ue=n=>{l.optionsCount++,l.filteredOptionsCount++,l.options.set(n.value,n),l.cachedOptions.set(n.value,n)},Je=(n,o)=>{l.options.get(n)===o&&(l.optionsCount--,l.filteredOptionsCount--,l.options.delete(n))},Xe=n=>{n.code!==Rl.backspace&&pe(!1),l.inputLength=r.value.value.length*15+20,W()},pe=n=>{if(!Array.isArray(l.selected))return;const o=l.selected[l.selected.length-1];if(o)return n===!0||n===!1?(o.hitState=n,n):(o.hitState=!o.hitState,o.hitState)},Ye=n=>{const o=n.target.value;if(n.type==="compositionend")l.isOnComposition=!1,T(()=>Q(o));else{const a=o[o.length-1]||"";l.isOnComposition=!Fl(a)}},G=()=>{T(()=>ne(l.selected))},Te=n=>{l.softFocus?l.softFocus=!1:((e.automaticDropdown||e.filterable)&&(e.filterable&&!l.visible&&(l.menuVisibleOnFocus=!0),l.visible=!0),i.emit("focus",n))},Ze=()=>{var n;l.visible=!1,(n=d.value)==null||n.blur()},Me=n=>{T(()=>{l.isSilentBlur?l.isSilentBlur=!1:i.emit("blur",n)}),l.softFocus=!1},xe=n=>{Le(n)},_e=()=>{l.visible=!1},el=n=>{l.visible&&(n.preventDefault(),n.stopPropagation(),l.visible=!1)},fe=n=>{var o;n&&!l.mouseEnter||j.value||(l.menuVisibleOnFocus?l.menuVisibleOnFocus=!1:(!u.value||!u.value.isFocusInsideContent())&&(l.visible=!l.visible),l.visible&&((o=r.value||d.value)==null||o.focus()))},ll=()=>{l.visible?I.value[l.hoverIndex]&&de(I.value[l.hoverIndex],void 0):fe()},ve=n=>vl(n.value)?A(n.value,e.valueKey):n.value,me=f(()=>I.value.filter(n=>n.visible).every(n=>n.disabled)),$e=n=>{if(!l.visible){l.visible=!0;return}if(!(l.options.size===0||l.filteredOptionsCount===0)&&!l.isOnComposition&&!me.value){n==="next"?(l.hoverIndex++,l.hoverIndex===l.options.size&&(l.hoverIndex=0)):n==="prev"&&(l.hoverIndex--,l.hoverIndex<0&&(l.hoverIndex=l.options.size-1));const o=I.value[l.hoverIndex];(o.disabled===!0||o.states.groupDisabled===!0||!o.visible)&&$e(n),T(()=>ne(v.value))}};return{optionList:P,optionsArray:I,selectSize:ee,handleResize:He,debouncedOnInputChange:je,debouncedQueryChange:Qe,deletePrevTag:Ge,deleteTag:le,deleteSelected:Le,handleOptionSelect:de,scrollToOption:ne,readonly:H,resetInputHeight:W,showClose:ye,iconComponent:Ce,iconReverse:Be,showNewOption:Ae,collapseTagSize:Fe,setSelected:ue,managePlaceholder:Oe,selectDisabled:j,emptyText:se,toggleLastOptionHitState:pe,resetInputState:Xe,handleComposition:Ye,onOptionCreate:Ue,onOptionDestroy:Je,handleMenuEnter:G,handleFocus:Te,blur:Ze,handleBlur:Me,handleClearClick:xe,handleClose:_e,handleKeydownEscape:el,toggleMenu:fe,selectOption:ll,getValueKey:ve,navigateOptions:$e,dropMenuVisible:We,queryChange:S,groupQueryChange:w,reference:d,input:r,tooltipRef:u,tags:b,selectWrapper:C,scrollbar:p,handleMouseEnter:()=>{l.mouseEnter=!0},handleMouseLeave:()=>{l.mouseEnter=!1}}};var vn=ge({name:"ElOptions",emits:["update-options"],setup(e,{slots:l,emit:i}){let h=[];function y(d,r){if(d.length!==r.length)return!1;for(const[u]of d.entries())if(d[u]!=r[u])return!1;return!0}return()=>{var d,r;const u=(d=l.default)==null?void 0:d.call(l),b=[];function C(p){Array.isArray(p)&&p.forEach(v=>{var S,w,P,t;const c=(S=(v==null?void 0:v.type)||{})==null?void 0:S.name;c==="ElOptionGroup"?C(!Sl(v.children)&&!Array.isArray(v.children)&&_((w=v.children)==null?void 0:w.default)?(P=v.children)==null?void 0:P.default():v.children):c==="ElOption"?b.push((t=v.props)==null?void 0:t.label):Array.isArray(v.children)&&C(v.children)})}return u.length&&C((r=u[0])==null?void 0:r.children),y(b,h)||(h=b,i("update-options",b)),u}}});const Cl="ElSelect",mn=ge({name:Cl,componentName:Cl,components:{ElInput:Wl,ElSelectMenu:cn,ElOption:dl,ElOptions:vn,ElTag:Kl,ElScrollbar:Ul,ElTooltip:Nl,ElIcon:Xl},directives:{ClickOutside:Jl},props:{name:String,id:String,modelValue:{type:[Array,String,Number,Boolean,Object],default:void 0},autocomplete:{type:String,default:"off"},automaticDropdown:Boolean,size:{type:String,validator:nn},effect:{type:String,default:"light"},disabled:Boolean,clearable:Boolean,filterable:Boolean,allowCreate:Boolean,loading:Boolean,popperClass:{type:String,default:""},popperOptions:{type:Object,default:()=>({})},remote:Boolean,loadingText:String,noMatchText:String,noDataText:String,remoteMethod:Function,filterMethod:Function,multiple:Boolean,multipleLimit:{type:Number,default:0},placeholder:{type:String},defaultFirstOption:Boolean,reserveKeyword:{type:Boolean,default:!0},valueKey:{type:String,default:"value"},collapseTags:Boolean,collapseTagsTooltip:{type:Boolean,default:!1},teleported:jl.teleported,persistent:{type:Boolean,default:!0},clearIcon:{type:yl,default:xl},fitInputWidth:{type:Boolean,default:!1},suffixIcon:{type:yl,default:_l},tagType:{...Hl.type,default:"info"},validateEvent:{type:Boolean,default:!0},remoteShowSuffix:{type:Boolean,default:!1},suffixTransition:{type:Boolean,default:!0},placement:{type:String,values:$l,default:"bottom-start"}},emits:[Y,El,"remove-tag","clear","visible-change","focus","blur"],setup(e,l){const i=re("select"),h=re("input"),{t:y}=ul(),d=pn(e),{optionList:r,optionsArray:u,selectSize:b,readonly:C,handleResize:p,collapseTagSize:v,debouncedOnInputChange:S,debouncedQueryChange:w,deletePrevTag:P,deleteTag:t,deleteSelected:c,handleOptionSelect:O,scrollToOption:H,setSelected:j,resetInputHeight:ye,managePlaceholder:Ce,showClose:Be,selectDisabled:Se,iconComponent:se,iconReverse:I,showNewOption:qe,emptyText:Ae,toggleLastOptionHitState:ee,resetInputState:Fe,handleComposition:We,onOptionCreate:W,onOptionDestroy:Q,handleMenuEnter:Oe,handleFocus:we,blur:ue,handleBlur:Ee,handleClearClick:Ke,handleClose:He,handleKeydownEscape:Re,toggleMenu:Ne,selectOption:je,getValueKey:Qe,navigateOptions:Z,dropMenuVisible:Ge,reference:le,input:Le,tooltipRef:de,tags:Ie,selectWrapper:ce,scrollbar:ne,queryChange:Ue,groupQueryChange:Je,handleMouseEnter:Xe,handleMouseLeave:pe}=fn(e,d,l),{focus:Ye}=tn(le),{inputWidth:G,selected:Te,inputLength:Ze,filteredOptionsCount:Me,visible:xe,softFocus:_e,selectedLabel:el,hoverIndex:fe,query:ll,inputHovering:ve,currentPlaceholder:me,menuVisibleOnFocus:$e,isOnComposition:cl,isSilentBlur:pl,options:n,cachedOptions:o,optionsCount:a,prefixWidth:s,tagInMultiLine:E}=al(d),M=f(()=>{const $=[i.b()],x=te(b);return x&&$.push(i.m(x)),e.disabled&&$.push(i.m("disabled")),$}),R=f(()=>({maxWidth:`${te(G)-32}px`,width:"100%"})),N=f(()=>({maxWidth:`${te(G)>123?te(G)-123:te(G)-75}px`}));Ol(De,ke({props:e,options:n,optionsArray:u,cachedOptions:o,optionsCount:a,filteredOptionsCount:Me,hoverIndex:fe,handleOptionSelect:O,onOptionCreate:W,onOptionDestroy:Q,selectWrapper:ce,selected:Te,setSelected:j,queryChange:Ue,groupQueryChange:Je})),sl(()=>{d.cachedPlaceHolder=me.value=e.placeholder||(()=>y("el.select.placeholder")),e.multiple&&Array.isArray(e.modelValue)&&e.modelValue.length>0&&(me.value=""),wl(ce,p),e.remote&&e.multiple&&ye(),T(()=>{const $=le.value&&le.value.$el;if($&&(G.value=$.getBoundingClientRect().width,l.slots.prefix)){const x=$.querySelector(`.${h.e("prefix")}`);s.value=Math.max(x.getBoundingClientRect().width+5,30)}}),j()}),e.multiple&&!Array.isArray(e.modelValue)&&l.emit(Y,[]),!e.multiple&&Array.isArray(e.modelValue)&&l.emit(Y,"");const k=f(()=>{var $,x;return(x=($=de.value)==null?void 0:$.popperRef)==null?void 0:x.contentRef});return{onOptionsRendered:$=>{r.value=$},tagInMultiLine:E,prefixWidth:s,selectSize:b,readonly:C,handleResize:p,collapseTagSize:v,debouncedOnInputChange:S,debouncedQueryChange:w,deletePrevTag:P,deleteTag:t,deleteSelected:c,handleOptionSelect:O,scrollToOption:H,inputWidth:G,selected:Te,inputLength:Ze,filteredOptionsCount:Me,visible:xe,softFocus:_e,selectedLabel:el,hoverIndex:fe,query:ll,inputHovering:ve,currentPlaceholder:me,menuVisibleOnFocus:$e,isOnComposition:cl,isSilentBlur:pl,options:n,resetInputHeight:ye,managePlaceholder:Ce,showClose:Be,selectDisabled:Se,iconComponent:se,iconReverse:I,showNewOption:qe,emptyText:Ae,toggleLastOptionHitState:ee,resetInputState:Fe,handleComposition:We,handleMenuEnter:Oe,handleFocus:we,blur:ue,handleBlur:Ee,handleClearClick:Ke,handleClose:He,handleKeydownEscape:Re,toggleMenu:Ne,selectOption:je,getValueKey:Qe,navigateOptions:Z,dropMenuVisible:Ge,focus:Ye,reference:le,input:Le,tooltipRef:de,popperPaneRef:k,tags:Ie,selectWrapper:ce,scrollbar:ne,wrapperKls:M,selectTagsStyle:R,nsSelect:i,tagTextStyle:N,handleMouseEnter:Xe,handleMouseLeave:pe}}}),hn=["disabled","autocomplete"],bn={style:{height:"100%",display:"flex","justify-content":"center","align-items":"center"}};function gn(e,l,i,h,y,d){const r=U("el-tag"),u=U("el-tooltip"),b=U("el-icon"),C=U("el-input"),p=U("el-option"),v=U("el-options"),S=U("el-scrollbar"),w=U("el-select-menu"),P=Vl("click-outside");return be((m(),V("div",{ref:"selectWrapper",class:g(e.wrapperKls),onMouseenter:l[22]||(l[22]=(...t)=>e.handleMouseEnter&&e.handleMouseEnter(...t)),onMouseleave:l[23]||(l[23]=(...t)=>e.handleMouseLeave&&e.handleMouseLeave(...t)),onClick:l[24]||(l[24]=J((...t)=>e.toggleMenu&&e.toggleMenu(...t),["stop"]))},[oe(u,{ref:"tooltipRef",visible:e.dropMenuVisible,placement:e.placement,teleported:e.teleported,"popper-class":[e.nsSelect.e("popper"),e.popperClass],"popper-options":e.popperOptions,"fallback-placements":["bottom-start","top-start","right","left"],effect:e.effect,pure:"",trigger:"click",transition:`${e.nsSelect.namespace.value}-zoom-in-top`,"stop-popper-mouse-event":!1,"gpu-acceleration":!1,persistent:e.persistent,onShow:e.handleMenuEnter},{default:L(()=>[z("div",{class:"select-trigger",onMouseenter:l[20]||(l[20]=t=>e.inputHovering=!0),onMouseleave:l[21]||(l[21]=t=>e.inputHovering=!1)},[e.multiple?(m(),V("div",{key:0,ref:"tags",class:g(e.nsSelect.e("tags")),style:ie(e.selectTagsStyle)},[e.collapseTags&&e.selected.length?(m(),V("span",{key:0,class:g([e.nsSelect.b("tags-wrapper"),{"has-prefix":e.prefixWidth&&e.selected.length}])},[oe(r,{closable:!e.selectDisabled&&!e.selected[0].isDisabled,size:e.collapseTagSize,hit:e.selected[0].hitState,type:e.tagType,"disable-transitions":"",onClose:l[0]||(l[0]=t=>e.deleteTag(t,e.selected[0]))},{default:L(()=>[z("span",{class:g(e.nsSelect.e("tags-text")),style:ie(e.tagTextStyle)},X(e.selected[0].currentLabel),7)]),_:1},8,["closable","size","hit","type"]),e.selected.length>1?(m(),K(r,{key:0,closable:!1,size:e.collapseTagSize,type:e.tagType,"disable-transitions":""},{default:L(()=>[e.collapseTagsTooltip?(m(),K(u,{key:0,disabled:e.dropMenuVisible,"fallback-placements":["bottom","top","right","left"],effect:e.effect,placement:"bottom",teleported:e.teleported},{default:L(()=>[z("span",{class:g(e.nsSelect.e("tags-text"))},"+ "+X(e.selected.length-1),3)]),content:L(()=>[z("div",{class:g(e.nsSelect.e("collapse-tags"))},[(m(!0),V(tl,null,ml(e.selected.slice(1),(t,c)=>(m(),V("div",{key:c,class:g(e.nsSelect.e("collapse-tag"))},[(m(),K(r,{key:e.getValueKey(t),class:"in-tooltip",closable:!e.selectDisabled&&!t.isDisabled,size:e.collapseTagSize,hit:t.hitState,type:e.tagType,"disable-transitions":"",style:{margin:"2px"},onClose:O=>e.deleteTag(O,t)},{default:L(()=>[z("span",{class:g(e.nsSelect.e("tags-text")),style:ie({maxWidth:e.inputWidth-75+"px"})},X(t.currentLabel),7)]),_:2},1032,["closable","size","hit","type","onClose"]))],2))),128))],2)]),_:1},8,["disabled","effect","teleported"])):(m(),V("span",{key:1,class:g(e.nsSelect.e("tags-text"))},"+ "+X(e.selected.length-1),3))]),_:1},8,["size","type"])):D("v-if",!0)],2)):D("v-if",!0),D(" <div> "),e.collapseTags?D("v-if",!0):(m(),K(Pl,{key:1,onAfterLeave:e.resetInputHeight},{default:L(()=>[z("span",{class:g([e.nsSelect.b("tags-wrapper"),{"has-prefix":e.prefixWidth&&e.selected.length}])},[(m(!0),V(tl,null,ml(e.selected,t=>(m(),K(r,{key:e.getValueKey(t),closable:!e.selectDisabled&&!t.isDisabled,size:e.collapseTagSize,hit:t.hitState,type:e.tagType,"disable-transitions":"",onClose:c=>e.deleteTag(c,t)},{default:L(()=>[z("span",{class:g(e.nsSelect.e("tags-text")),style:ie({maxWidth:e.inputWidth-75+"px"})},X(t.currentLabel),7)]),_:2},1032,["closable","size","hit","type","onClose"]))),128))],2)]),_:1},8,["onAfterLeave"])),D(" </div> "),e.filterable?be((m(),V("input",{key:2,ref:"input","onUpdate:modelValue":l[1]||(l[1]=t=>e.query=t),type:"text",class:g([e.nsSelect.e("input"),e.nsSelect.is(e.selectSize)]),disabled:e.selectDisabled,autocomplete:e.autocomplete,style:ie({marginLeft:e.prefixWidth&&!e.selected.length||e.tagInMultiLine?`${e.prefixWidth}px`:"",flexGrow:1,width:`${e.inputLength/(e.inputWidth-32)}%`,maxWidth:`${e.inputWidth-42}px`}),onFocus:l[2]||(l[2]=(...t)=>e.handleFocus&&e.handleFocus(...t)),onBlur:l[3]||(l[3]=(...t)=>e.handleBlur&&e.handleBlur(...t)),onKeyup:l[4]||(l[4]=(...t)=>e.managePlaceholder&&e.managePlaceholder(...t)),onKeydown:[l[5]||(l[5]=(...t)=>e.resetInputState&&e.resetInputState(...t)),l[6]||(l[6]=B(J(t=>e.navigateOptions("next"),["prevent"]),["down"])),l[7]||(l[7]=B(J(t=>e.navigateOptions("prev"),["prevent"]),["up"])),l[8]||(l[8]=B((...t)=>e.handleKeydownEscape&&e.handleKeydownEscape(...t),["esc"])),l[9]||(l[9]=B(J((...t)=>e.selectOption&&e.selectOption(...t),["stop","prevent"]),["enter"])),l[10]||(l[10]=B((...t)=>e.deletePrevTag&&e.deletePrevTag(...t),["delete"])),l[11]||(l[11]=B(t=>e.visible=!1,["tab"]))],onCompositionstart:l[12]||(l[12]=(...t)=>e.handleComposition&&e.handleComposition(...t)),onCompositionupdate:l[13]||(l[13]=(...t)=>e.handleComposition&&e.handleComposition(...t)),onCompositionend:l[14]||(l[14]=(...t)=>e.handleComposition&&e.handleComposition(...t)),onInput:l[15]||(l[15]=(...t)=>e.debouncedQueryChange&&e.debouncedQueryChange(...t))},null,46,hn)),[[kl,e.query]]):D("v-if",!0)],6)):D("v-if",!0),oe(C,{id:e.id,ref:"reference",modelValue:e.selectedLabel,"onUpdate:modelValue":l[16]||(l[16]=t=>e.selectedLabel=t),type:"text",placeholder:typeof e.currentPlaceholder=="function"?e.currentPlaceholder():e.currentPlaceholder,name:e.name,autocomplete:e.autocomplete,size:e.selectSize,disabled:e.selectDisabled,readonly:e.readonly,"validate-event":!1,class:g([e.nsSelect.is("focus",e.visible)]),tabindex:e.multiple&&e.filterable?-1:void 0,onFocus:e.handleFocus,onBlur:e.handleBlur,onInput:e.debouncedOnInputChange,onPaste:e.debouncedOnInputChange,onCompositionstart:e.handleComposition,onCompositionupdate:e.handleComposition,onCompositionend:e.handleComposition,onKeydown:[l[17]||(l[17]=B(J(t=>e.navigateOptions("next"),["stop","prevent"]),["down"])),l[18]||(l[18]=B(J(t=>e.navigateOptions("prev"),["stop","prevent"]),["up"])),B(J(e.selectOption,["stop","prevent"]),["enter"]),B(e.handleKeydownEscape,["esc"]),l[19]||(l[19]=B(t=>e.visible=!1,["tab"]))]},zl({suffix:L(()=>[e.iconComponent&&!e.showClose?(m(),K(b,{key:0,class:g([e.nsSelect.e("caret"),e.nsSelect.e("icon"),e.iconReverse])},{default:L(()=>[(m(),K(hl(e.iconComponent)))]),_:1},8,["class"])):D("v-if",!0),e.showClose&&e.clearIcon?(m(),K(b,{key:1,class:g([e.nsSelect.e("caret"),e.nsSelect.e("icon")]),onClick:e.handleClearClick},{default:L(()=>[(m(),K(hl(e.clearIcon)))]),_:1},8,["class","onClick"])):D("v-if",!0)]),_:2},[e.$slots.prefix?{name:"prefix",fn:L(()=>[z("div",bn,[ae(e.$slots,"prefix")])])}:void 0]),1032,["id","modelValue","placeholder","name","autocomplete","size","disabled","readonly","class","tabindex","onFocus","onBlur","onInput","onPaste","onCompositionstart","onCompositionupdate","onCompositionend","onKeydown"])],32)]),content:L(()=>[oe(w,null,{default:L(()=>[be(oe(S,{ref:"scrollbar",tag:"ul","wrap-class":e.nsSelect.be("dropdown","wrap"),"view-class":e.nsSelect.be("dropdown","list"),class:g([e.nsSelect.is("empty",!e.allowCreate&&Boolean(e.query)&&e.filteredOptionsCount===0)])},{default:L(()=>[e.showNewOption?(m(),K(p,{key:0,value:e.query,created:!0},null,8,["value"])):D("v-if",!0),oe(v,{onUpdateOptions:e.onOptionsRendered},{default:L(()=>[ae(e.$slots,"default")]),_:3},8,["onUpdateOptions"])]),_:3},8,["wrap-class","view-class","class"]),[[rl,e.options.size>0&&!e.loading]]),e.emptyText&&(!e.allowCreate||e.loading||e.allowCreate&&e.options.size===0)?(m(),V(tl,{key:0},[e.$slots.empty?ae(e.$slots,"empty",{key:0}):(m(),V("p",{key:1,class:g(e.nsSelect.be("dropdown","empty"))},X(e.emptyText),3))],64)):D("v-if",!0)]),_:3})]),_:3},8,["visible","placement","teleported","popper-class","popper-options","effect","transition","persistent","onShow"])],34)),[[P,e.handleClose,e.popperPaneRef]])}var yn=ze(mn,[["render",gn],["__file","/home/runner/work/element-plus/element-plus/packages/components/select/src/select.vue"]]);const Cn=ge({name:"ElOptionGroup",componentName:"ElOptionGroup",props:{label:String,disabled:{type:Boolean,default:!1}},setup(e){const l=re("select"),i=q(!0),h=il(),y=q([]);Ol(Il,ke({...al(e)}));const d=Ve(De);sl(()=>{y.value=r(h.subTree)});const r=b=>{const C=[];return Array.isArray(b.children)&&b.children.forEach(p=>{var v;p.type&&p.type.name==="ElOption"&&p.component&&p.component.proxy?C.push(p.component.proxy):(v=p.children)!=null&&v.length&&C.push(...r(p))}),C},{groupQueryChange:u}=Pe(d);return F(u,()=>{i.value=y.value.some(b=>b.visible===!0)},{flush:"post"}),{visible:i,ns:l}}});function Sn(e,l,i,h,y,d){return be((m(),V("ul",{class:g(e.ns.be("group","wrap"))},[z("li",{class:g(e.ns.be("group","title"))},X(e.label),3),z("li",null,[z("ul",{class:g(e.ns.b("group"))},[ae(e.$slots,"default")],2)])],2)),[[rl,e.visible]])}var Tl=ze(Cn,[["render",Sn],["__file","/home/runner/work/element-plus/element-plus/packages/components/select/src/option-group.vue"]]);const Pn=en(yn,{Option:dl,OptionGroup:Tl}),kn=Ll(dl);Ll(Tl);export{Pn as E,kn as a};
