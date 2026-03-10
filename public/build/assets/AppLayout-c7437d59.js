import{E as pe,G as y,H as me,I as ge,C as fe,o as u,e as c,c as k,K as E,L as g,F as w,a as n,t as f,f as v,M as be,s as he,j as q,w as z,b as x,N as ye,h as L,d as $,q as S,J as Z,O as U,n as p,m as F,P as ve,Q as we,u as R,X as ke,r as xe,R as J}from"./app-d8378d3b.js";import{x as G,s as Se,a as Ce,R as je}from"./index-34d3f7dc.js";import{f as re,s as ae}from"./index-984f72ad.js";import{s as Q}from"./index-7199608b.js";import{s as W,a as Y,b as ee}from"./index-83b7f6a0.js";import{_ as Pe}from"./_plugin-vue_export-helper-c27b6911.js";var Ie=`
    .p-toast {
        width: dt('toast.width');
        white-space: pre-line;
        word-break: break-word;
    }

    .p-toast-message {
        margin: 0 0 1rem 0;
        display: grid;
        grid-template-rows: 1fr;
    }

    .p-toast-message-icon {
        flex-shrink: 0;
        font-size: dt('toast.icon.size');
        width: dt('toast.icon.size');
        height: dt('toast.icon.size');
    }

    .p-toast-message-content {
        display: flex;
        align-items: flex-start;
        padding: dt('toast.content.padding');
        gap: dt('toast.content.gap');
        min-height: 0;
        overflow: hidden;
        transition: padding 250ms ease-in;
    }

    .p-toast-message-text {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        gap: dt('toast.text.gap');
    }

    .p-toast-summary {
        font-weight: dt('toast.summary.font.weight');
        font-size: dt('toast.summary.font.size');
    }

    .p-toast-detail {
        font-weight: dt('toast.detail.font.weight');
        font-size: dt('toast.detail.font.size');
    }

    .p-toast-close-button {
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        background: transparent;
        transition:
            background dt('toast.transition.duration'),
            color dt('toast.transition.duration'),
            outline-color dt('toast.transition.duration'),
            box-shadow dt('toast.transition.duration');
        outline-color: transparent;
        color: inherit;
        width: dt('toast.close.button.width');
        height: dt('toast.close.button.height');
        border-radius: dt('toast.close.button.border.radius');
        margin: -25% 0 0 0;
        right: -25%;
        padding: 0;
        border: none;
        user-select: none;
    }

    .p-toast-close-button:dir(rtl) {
        margin: -25% 0 0 auto;
        left: -25%;
        right: auto;
    }

    .p-toast-message-info,
    .p-toast-message-success,
    .p-toast-message-warn,
    .p-toast-message-error,
    .p-toast-message-secondary,
    .p-toast-message-contrast {
        border-width: dt('toast.border.width');
        border-style: solid;
        backdrop-filter: blur(dt('toast.blur'));
        border-radius: dt('toast.border.radius');
    }

    .p-toast-close-icon {
        font-size: dt('toast.close.icon.size');
        width: dt('toast.close.icon.size');
        height: dt('toast.close.icon.size');
    }

    .p-toast-close-button:focus-visible {
        outline-width: dt('focus.ring.width');
        outline-style: dt('focus.ring.style');
        outline-offset: dt('focus.ring.offset');
    }

    .p-toast-message-info {
        background: dt('toast.info.background');
        border-color: dt('toast.info.border.color');
        color: dt('toast.info.color');
        box-shadow: dt('toast.info.shadow');
    }

    .p-toast-message-info .p-toast-detail {
        color: dt('toast.info.detail.color');
    }

    .p-toast-message-info .p-toast-close-button:focus-visible {
        outline-color: dt('toast.info.close.button.focus.ring.color');
        box-shadow: dt('toast.info.close.button.focus.ring.shadow');
    }

    .p-toast-message-info .p-toast-close-button:hover {
        background: dt('toast.info.close.button.hover.background');
    }

    .p-toast-message-success {
        background: dt('toast.success.background');
        border-color: dt('toast.success.border.color');
        color: dt('toast.success.color');
        box-shadow: dt('toast.success.shadow');
    }

    .p-toast-message-success .p-toast-detail {
        color: dt('toast.success.detail.color');
    }

    .p-toast-message-success .p-toast-close-button:focus-visible {
        outline-color: dt('toast.success.close.button.focus.ring.color');
        box-shadow: dt('toast.success.close.button.focus.ring.shadow');
    }

    .p-toast-message-success .p-toast-close-button:hover {
        background: dt('toast.success.close.button.hover.background');
    }

    .p-toast-message-warn {
        background: dt('toast.warn.background');
        border-color: dt('toast.warn.border.color');
        color: dt('toast.warn.color');
        box-shadow: dt('toast.warn.shadow');
    }

    .p-toast-message-warn .p-toast-detail {
        color: dt('toast.warn.detail.color');
    }

    .p-toast-message-warn .p-toast-close-button:focus-visible {
        outline-color: dt('toast.warn.close.button.focus.ring.color');
        box-shadow: dt('toast.warn.close.button.focus.ring.shadow');
    }

    .p-toast-message-warn .p-toast-close-button:hover {
        background: dt('toast.warn.close.button.hover.background');
    }

    .p-toast-message-error {
        background: dt('toast.error.background');
        border-color: dt('toast.error.border.color');
        color: dt('toast.error.color');
        box-shadow: dt('toast.error.shadow');
    }

    .p-toast-message-error .p-toast-detail {
        color: dt('toast.error.detail.color');
    }

    .p-toast-message-error .p-toast-close-button:focus-visible {
        outline-color: dt('toast.error.close.button.focus.ring.color');
        box-shadow: dt('toast.error.close.button.focus.ring.shadow');
    }

    .p-toast-message-error .p-toast-close-button:hover {
        background: dt('toast.error.close.button.hover.background');
    }

    .p-toast-message-secondary {
        background: dt('toast.secondary.background');
        border-color: dt('toast.secondary.border.color');
        color: dt('toast.secondary.color');
        box-shadow: dt('toast.secondary.shadow');
    }

    .p-toast-message-secondary .p-toast-detail {
        color: dt('toast.secondary.detail.color');
    }

    .p-toast-message-secondary .p-toast-close-button:focus-visible {
        outline-color: dt('toast.secondary.close.button.focus.ring.color');
        box-shadow: dt('toast.secondary.close.button.focus.ring.shadow');
    }

    .p-toast-message-secondary .p-toast-close-button:hover {
        background: dt('toast.secondary.close.button.hover.background');
    }

    .p-toast-message-contrast {
        background: dt('toast.contrast.background');
        border-color: dt('toast.contrast.border.color');
        color: dt('toast.contrast.color');
        box-shadow: dt('toast.contrast.shadow');
    }
    
    .p-toast-message-contrast .p-toast-detail {
        color: dt('toast.contrast.detail.color');
    }

    .p-toast-message-contrast .p-toast-close-button:focus-visible {
        outline-color: dt('toast.contrast.close.button.focus.ring.color');
        box-shadow: dt('toast.contrast.close.button.focus.ring.shadow');
    }

    .p-toast-message-contrast .p-toast-close-button:hover {
        background: dt('toast.contrast.close.button.hover.background');
    }

    .p-toast-top-center {
        transform: translateX(-50%);
    }

    .p-toast-bottom-center {
        transform: translateX(-50%);
    }

    .p-toast-center {
        min-width: 20vw;
        transform: translate(-50%, -50%);
    }

    .p-toast-message-enter-active {
        animation: p-animate-toast-enter 300ms ease-out;
    }

    .p-toast-message-leave-active {
        animation: p-animate-toast-leave 250ms ease-in;
    }

    .p-toast-message-leave-to .p-toast-message-content {
        padding-top: 0;
        padding-bottom: 0;
    }

    @keyframes p-animate-toast-enter {
        from {
            opacity: 0;
            transform: scale(0.6);
        }
        to {
            opacity: 1;
            grid-template-rows: 1fr;
        }
    }

     @keyframes p-animate-toast-leave {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
            margin-bottom: 0;
            grid-template-rows: 0fr;
            transform: translateY(-100%) scale(0.6);
        }
    }
`;function j(e){return j=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},j(e)}function B(e,t,o){return(t=Me(t))in e?Object.defineProperty(e,t,{value:o,enumerable:!0,configurable:!0,writable:!0}):e[t]=o,e}function Me(e){var t=Te(e,"string");return j(t)=="symbol"?t:t+""}function Te(e,t){if(j(e)!="object"||!e)return e;var o=e[Symbol.toPrimitive];if(o!==void 0){var s=o.call(e,t);if(j(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var _e={root:function(t){var o=t.position;return{position:"fixed",top:o==="top-right"||o==="top-left"||o==="top-center"?"20px":o==="center"?"50%":null,right:(o==="top-right"||o==="bottom-right")&&"20px",bottom:(o==="bottom-left"||o==="bottom-right"||o==="bottom-center")&&"20px",left:o==="top-left"||o==="bottom-left"?"20px":o==="center"||o==="top-center"||o==="bottom-center"?"50%":null}}},Oe={root:function(t){var o=t.props;return["p-toast p-component p-toast-"+o.position]},message:function(t){var o=t.props;return["p-toast-message",{"p-toast-message-info":o.message.severity==="info"||o.message.severity===void 0,"p-toast-message-warn":o.message.severity==="warn","p-toast-message-error":o.message.severity==="error","p-toast-message-success":o.message.severity==="success","p-toast-message-secondary":o.message.severity==="secondary","p-toast-message-contrast":o.message.severity==="contrast"}]},messageContent:"p-toast-message-content",messageIcon:function(t){var o=t.props;return["p-toast-message-icon",B(B(B(B({},o.infoIcon,o.message.severity==="info"),o.warnIcon,o.message.severity==="warn"),o.errorIcon,o.message.severity==="error"),o.successIcon,o.message.severity==="success")]},messageText:"p-toast-message-text",summary:"p-toast-summary",detail:"p-toast-detail",closeButton:"p-toast-close-button",closeIcon:"p-toast-close-icon"},Ne=pe.extend({name:"toast",style:Ie,classes:Oe,inlineStyles:_e}),Ae={name:"BaseToast",extends:ae,props:{group:{type:String,default:null},position:{type:String,default:"top-right"},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},breakpoints:{type:Object,default:null},closeIcon:{type:String,default:void 0},infoIcon:{type:String,default:void 0},warnIcon:{type:String,default:void 0},errorIcon:{type:String,default:void 0},successIcon:{type:String,default:void 0},closeButtonProps:{type:null,default:null},onMouseEnter:{type:Function,default:void 0},onMouseLeave:{type:Function,default:void 0},onClick:{type:Function,default:void 0}},style:Ne,provide:function(){return{$pcToast:this,$parentInstance:this}}};function P(e){return P=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},P(e)}function Ee(e,t,o){return(t=Re(t))in e?Object.defineProperty(e,t,{value:o,enumerable:!0,configurable:!0,writable:!0}):e[t]=o,e}function Re(e){var t=Be(e,"string");return P(t)=="symbol"?t:t+""}function Be(e,t){if(P(e)!="object"||!e)return e;var o=e[Symbol.toPrimitive];if(o!==void 0){var s=o.call(e,t);if(P(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var ie={name:"ToastMessage",hostName:"Toast",extends:ae,emits:["close"],closeTimeout:null,createdAt:null,lifeRemaining:null,props:{message:{type:null,default:null},templates:{type:Object,default:null},closeIcon:{type:String,default:null},infoIcon:{type:String,default:null},warnIcon:{type:String,default:null},errorIcon:{type:String,default:null},successIcon:{type:String,default:null},closeButtonProps:{type:null,default:null},onMouseEnter:{type:Function,default:void 0},onMouseLeave:{type:Function,default:void 0},onClick:{type:Function,default:void 0}},mounted:function(){this.message.life&&(this.lifeRemaining=this.message.life,this.startTimeout())},beforeUnmount:function(){this.clearCloseTimeout()},methods:{startTimeout:function(){var t=this;this.createdAt=new Date().valueOf(),this.closeTimeout=setTimeout(function(){t.close({message:t.message,type:"life-end"})},this.lifeRemaining)},close:function(t){this.$emit("close",t)},onCloseClick:function(){this.clearCloseTimeout(),this.close({message:this.message,type:"close"})},clearCloseTimeout:function(){this.closeTimeout&&(clearTimeout(this.closeTimeout),this.closeTimeout=null)},onMessageClick:function(t){var o;(o=this.onClick)===null||o===void 0||o.call(this,{originalEvent:t,message:this.message})},handleMouseEnter:function(t){if(this.onMouseEnter){if(this.onMouseEnter({originalEvent:t,message:this.message}),t.defaultPrevented)return;this.message.life&&(this.lifeRemaining=this.createdAt+this.lifeRemaining-new Date().valueOf(),this.createdAt=null,this.clearCloseTimeout())}},handleMouseLeave:function(t){if(this.onMouseLeave){if(this.onMouseLeave({originalEvent:t,message:this.message}),t.defaultPrevented)return;this.message.life&&this.startTimeout()}}},computed:{iconComponent:function(){return{info:!this.infoIcon&&W,success:!this.successIcon&&Q,warn:!this.warnIcon&&Y,error:!this.errorIcon&&ee}[this.message.severity]},closeAriaLabel:function(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.close:void 0},dataP:function(){return re(Ee({},this.message.severity,this.message.severity))}},components:{TimesIcon:Ce,InfoCircleIcon:W,CheckIcon:Q,ExclamationTriangleIcon:Y,TimesCircleIcon:ee},directives:{ripple:je}};function I(e){return I=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},I(e)}function te(e,t){var o=Object.keys(e);if(Object.getOwnPropertySymbols){var s=Object.getOwnPropertySymbols(e);t&&(s=s.filter(function(d){return Object.getOwnPropertyDescriptor(e,d).enumerable})),o.push.apply(o,s)}return o}function oe(e){for(var t=1;t<arguments.length;t++){var o=arguments[t]!=null?arguments[t]:{};t%2?te(Object(o),!0).forEach(function(s){Le(e,s,o[s])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(o)):te(Object(o)).forEach(function(s){Object.defineProperty(e,s,Object.getOwnPropertyDescriptor(o,s))})}return e}function Le(e,t,o){return(t=$e(t))in e?Object.defineProperty(e,t,{value:o,enumerable:!0,configurable:!0,writable:!0}):e[t]=o,e}function $e(e){var t=ze(e,"string");return I(t)=="symbol"?t:t+""}function ze(e,t){if(I(e)!="object"||!e)return e;var o=e[Symbol.toPrimitive];if(o!==void 0){var s=o.call(e,t);if(I(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var Ve=["data-p"],He=["data-p"],De=["data-p"],Ge=["data-p"],Ze=["aria-label","data-p"];function Ue(e,t,o,s,d,r){var b=fe("ripple");return u(),c("div",g({class:[e.cx("message"),o.message.styleClass],role:"alert","aria-live":"assertive","aria-atomic":"true","data-p":r.dataP},e.ptm("message"),{onClick:t[1]||(t[1]=function(){return r.onMessageClick&&r.onMessageClick.apply(r,arguments)}),onMouseenter:t[2]||(t[2]=function(){return r.handleMouseEnter&&r.handleMouseEnter.apply(r,arguments)}),onMouseleave:t[3]||(t[3]=function(){return r.handleMouseLeave&&r.handleMouseLeave.apply(r,arguments)})}),[o.templates.container?(u(),k(E(o.templates.container),{key:0,message:o.message,closeCallback:r.onCloseClick},null,8,["message","closeCallback"])):(u(),c("div",g({key:1,class:[e.cx("messageContent"),o.message.contentStyleClass]},e.ptm("messageContent")),[o.templates.message?(u(),k(E(o.templates.message),{key:1,message:o.message},null,8,["message"])):(u(),c(w,{key:0},[(u(),k(E(o.templates.messageicon?o.templates.messageicon:o.templates.icon?o.templates.icon:r.iconComponent&&r.iconComponent.name?r.iconComponent:"span"),g({class:e.cx("messageIcon")},e.ptm("messageIcon")),null,16,["class"])),n("div",g({class:e.cx("messageText"),"data-p":r.dataP},e.ptm("messageText")),[n("span",g({class:e.cx("summary"),"data-p":r.dataP},e.ptm("summary")),f(o.message.summary),17,De),o.message.detail?(u(),c("div",g({key:0,class:e.cx("detail"),"data-p":r.dataP},e.ptm("detail")),f(o.message.detail),17,Ge)):v("",!0)],16,He)],64)),o.message.closable!==!1?(u(),c("div",be(g({key:2},e.ptm("buttonContainer"))),[he((u(),c("button",g({class:e.cx("closeButton"),type:"button","aria-label":r.closeAriaLabel,onClick:t[0]||(t[0]=function(){return r.onCloseClick&&r.onCloseClick.apply(r,arguments)}),autofocus:"","data-p":r.dataP},oe(oe({},o.closeButtonProps),e.ptm("closeButton"))),[(u(),k(E(o.templates.closeicon||"TimesIcon"),g({class:[e.cx("closeIcon"),o.closeIcon]},e.ptm("closeIcon")),null,16,["class"]))],16,Ze)),[[b]])],16)):v("",!0)],16))],16,Ve)}ie.render=Ue;function M(e){return M=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},M(e)}function Fe(e,t,o){return(t=Ke(t))in e?Object.defineProperty(e,t,{value:o,enumerable:!0,configurable:!0,writable:!0}):e[t]=o,e}function Ke(e){var t=Xe(e,"string");return M(t)=="symbol"?t:t+""}function Xe(e,t){if(M(e)!="object"||!e)return e;var o=e[Symbol.toPrimitive];if(o!==void 0){var s=o.call(e,t);if(M(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function qe(e){return Ye(e)||We(e)||Qe(e)||Je()}function Je(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function Qe(e,t){if(e){if(typeof e=="string")return K(e,t);var o={}.toString.call(e).slice(8,-1);return o==="Object"&&e.constructor&&(o=e.constructor.name),o==="Map"||o==="Set"?Array.from(e):o==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(o)?K(e,t):void 0}}function We(e){if(typeof Symbol<"u"&&e[Symbol.iterator]!=null||e["@@iterator"]!=null)return Array.from(e)}function Ye(e){if(Array.isArray(e))return K(e)}function K(e,t){(t==null||t>e.length)&&(t=e.length);for(var o=0,s=Array(t);o<t;o++)s[o]=e[o];return s}var et=0,le={name:"Toast",extends:Ae,inheritAttrs:!1,emits:["close","life-end"],data:function(){return{messages:[]}},styleElement:null,mounted:function(){y.on("add",this.onAdd),y.on("remove",this.onRemove),y.on("remove-group",this.onRemoveGroup),y.on("remove-all-groups",this.onRemoveAllGroups),this.breakpoints&&this.createStyle()},beforeUnmount:function(){this.destroyStyle(),this.$refs.container&&this.autoZIndex&&G.clear(this.$refs.container),y.off("add",this.onAdd),y.off("remove",this.onRemove),y.off("remove-group",this.onRemoveGroup),y.off("remove-all-groups",this.onRemoveAllGroups)},methods:{add:function(t){t.id==null&&(t.id=et++),this.messages=[].concat(qe(this.messages),[t])},remove:function(t){var o=this.messages.findIndex(function(s){return s.id===t.message.id});o!==-1&&(this.messages.splice(o,1),this.$emit(t.type,{message:t.message}))},onAdd:function(t){this.group==t.group&&this.add(t)},onRemove:function(t){this.remove({message:t,type:"close"})},onRemoveGroup:function(t){this.group===t&&(this.messages=[])},onRemoveAllGroups:function(){var t=this;this.messages.forEach(function(o){return t.$emit("close",{message:o})}),this.messages=[]},onEnter:function(){this.autoZIndex&&G.set("modal",this.$refs.container,this.baseZIndex||this.$primevue.config.zIndex.modal)},onLeave:function(){var t=this;this.$refs.container&&this.autoZIndex&&me(this.messages)&&setTimeout(function(){G.clear(t.$refs.container)},200)},createStyle:function(){if(!this.styleElement&&!this.isUnstyled){var t;this.styleElement=document.createElement("style"),this.styleElement.type="text/css",ge(this.styleElement,"nonce",(t=this.$primevue)===null||t===void 0||(t=t.config)===null||t===void 0||(t=t.csp)===null||t===void 0?void 0:t.nonce),document.head.appendChild(this.styleElement);var o="";for(var s in this.breakpoints){var d="";for(var r in this.breakpoints[s])d+=r+":"+this.breakpoints[s][r]+"!important;";o+=`
                        @media screen and (max-width: `.concat(s,`) {
                            .p-toast[`).concat(this.$attrSelector,`] {
                                `).concat(d,`
                            }
                        }
                    `)}this.styleElement.innerHTML=o}},destroyStyle:function(){this.styleElement&&(document.head.removeChild(this.styleElement),this.styleElement=null)}},computed:{dataP:function(){return re(Fe({},this.position,this.position))}},components:{ToastMessage:ie,Portal:Se}};function T(e){return T=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},T(e)}function se(e,t){var o=Object.keys(e);if(Object.getOwnPropertySymbols){var s=Object.getOwnPropertySymbols(e);t&&(s=s.filter(function(d){return Object.getOwnPropertyDescriptor(e,d).enumerable})),o.push.apply(o,s)}return o}function tt(e){for(var t=1;t<arguments.length;t++){var o=arguments[t]!=null?arguments[t]:{};t%2?se(Object(o),!0).forEach(function(s){ot(e,s,o[s])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(o)):se(Object(o)).forEach(function(s){Object.defineProperty(e,s,Object.getOwnPropertyDescriptor(o,s))})}return e}function ot(e,t,o){return(t=st(t))in e?Object.defineProperty(e,t,{value:o,enumerable:!0,configurable:!0,writable:!0}):e[t]=o,e}function st(e){var t=nt(e,"string");return T(t)=="symbol"?t:t+""}function nt(e,t){if(T(e)!="object"||!e)return e;var o=e[Symbol.toPrimitive];if(o!==void 0){var s=o.call(e,t);if(T(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var rt=["data-p"];function at(e,t,o,s,d,r){var b=q("ToastMessage"),_=q("Portal");return u(),k(_,null,{default:z(function(){return[n("div",g({ref:"container",class:e.cx("root"),style:e.sx("root",!0,{position:e.position}),"data-p":r.dataP},e.ptmi("root")),[x(ye,g({name:"p-toast-message",tag:"div",onEnter:r.onEnter,onLeave:r.onLeave},tt({},e.ptm("transition"))),{default:z(function(){return[(u(!0),c(w,null,L(d.messages,function(h){return u(),k(b,{key:h.id,message:h,templates:e.$slots,closeIcon:e.closeIcon,infoIcon:e.infoIcon,warnIcon:e.warnIcon,errorIcon:e.errorIcon,successIcon:e.successIcon,closeButtonProps:e.closeButtonProps,onMouseEnter:e.onMouseEnter,onMouseLeave:e.onMouseLeave,onClick:e.onClick,unstyled:e.unstyled,onClose:t[0]||(t[0]=function(O){return r.remove(O)}),pt:e.pt},null,8,["message","templates","closeIcon","infoIcon","warnIcon","errorIcon","successIcon","closeButtonProps","onMouseEnter","onMouseLeave","onClick","unstyled","pt"])}),128))]}),_:1},16,["onEnter","onLeave"])],16,rt)]}),_:1})}le.render=at;const it={class:"max-w-screen-xl mx-auto py-2 px-3 sm:px-6 lg:px-8"},lt={class:"flex items-center justify-between flex-wrap"},ut={class:"w-0 flex-1 flex items-center min-w-0"},ct={key:0,class:"h-5 w-5 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},dt={key:1,class:"h-5 w-5 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},pt={class:"ml-3 font-medium text-sm text-white truncate"},mt={class:"shrink-0 sm:ml-3"},gt={__name:"Banner",setup(e){const t=$(!0),o=S(()=>{var d;return((d=Z().props.jetstream.flash)==null?void 0:d.bannerStyle)||"success"}),s=S(()=>{var d;return((d=Z().props.jetstream.flash)==null?void 0:d.banner)||""});return U(s,async()=>{t.value=!0}),(d,r)=>(u(),c("div",null,[t.value&&s.value?(u(),c("div",{key:0,class:p({"bg-indigo-500":o.value=="success","bg-red-700":o.value=="danger"})},[n("div",it,[n("div",lt,[n("div",ut,[n("span",{class:p(["flex p-2 rounded-lg",{"bg-indigo-600":o.value=="success","bg-red-600":o.value=="danger"}])},[o.value=="success"?(u(),c("svg",ct,[...r[1]||(r[1]=[n("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"},null,-1)])])):v("",!0),o.value=="danger"?(u(),c("svg",dt,[...r[2]||(r[2]=[n("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"},null,-1)])])):v("",!0)],2),n("p",pt,f(s.value),1)]),n("div",mt,[n("button",{type:"button",class:p(["-mr-1 flex p-2 rounded-md focus:outline-none sm:-mr-2 transition",{"hover:bg-indigo-600 focus:bg-indigo-600":o.value=="success","hover:bg-red-600 focus:bg-red-600":o.value=="danger"}]),"aria-label":"Dismiss",onClick:r[0]||(r[0]=F(b=>t.value=!1,["prevent"]))},[...r[3]||(r[3]=[n("svg",{class:"h-5 w-5 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[n("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M6 18L18 6M6 6l12 12"})],-1)])],2)])])])],2)):v("",!0)]))}},ft={props:{size:{type:String,default:"md"}},computed:{sizeClass(){if(this.size=="sm")return"w-3 h-3 border ";if(this.size=="md")return"w-6 h-6 border-2 "}}};function bt(e,t,o,s,d,r){return u(),c("div",{class:p(r.sizeClass+"inline-block animate-spin rounded-full border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]")},null,2)}const ht=Pe(ft,[["render",bt]]),ne={name:"SlideUpDown",props:{active:Boolean,duration:{type:Number,default:500},tag:{type:String,default:"div"},useHidden:{type:Boolean,default:!0}},data:()=>({style:{},initial:!1,hidden:!1}),watch:{active(){this.layout()}},render(){return ve(this.tag,{...this.attrs,style:this.style,ref:"container",onTransitionend:this.onTransitionEnd},this.$slots.default())},mounted(){this.layout(),this.initial=!0},created(){this.hidden=!this.active},computed:{el(){return this.$refs.container},attrs(){const e={"aria-hidden":!this.active,"aria-expanded":this.active};return this.useHidden&&(e.hidden=this.hidden),e}},methods:{layout(){this.active?(this.hidden=!1,this.$emit("open-start"),this.initial&&this.setHeight("0px",()=>this.el.scrollHeight+"px")):(this.$emit("close-start"),this.setHeight(this.el.scrollHeight+"px",()=>"0px"))},asap(e){this.initial?this.$nextTick(e):e()},setHeight(e,t){this.style={height:e},this.asap(()=>{this.__=this.el.scrollHeight,this.style={height:t(),overflow:"hidden","transition-property":"height","transition-duration":this.duration+"ms"}})},onTransitionEnd(e){e.target===this.el&&(this.active?(this.style={},this.$emit("open-end")):(this.style={height:"0",overflow:"hidden"},this.hidden=!0,this.$emit("close-end")))}}},yt={class:"h-full flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700"},vt={class:"flex items-center justify-between gap-2"},wt={class:"min-w-0"},kt={class:"font-semibold text-gray-900 dark:text-gray-100 text-sm truncate"},xt=["title"],St={class:"flex-1 overflow-y-auto min-h-0 px-3 py-1.5 sidebar-nav-scroll"},Ct={class:"space-y-0.5"},jt=["onClick"],Pt={key:1,class:"space-y-0.5"},It=["onClick"],Mt=["onClick"],Tt={key:2,class:"mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 space-y-0.5"},_t=["onClick"],Ot=["onClick"],Nt={class:"shrink-0 px-4 pt-3 pb-10 border-t border-gray-200 dark:border-gray-600 space-y-2"},At={key:0,class:"py-1.5"},Et=["title"],Rt={key:1,class:"w-5 h-5 shrink-0 text-gray-500 dark:text-gray-400",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},Dt={__name:"AppLayout",props:{title:String},setup(e){const t=$([{type:"link",label:"Subscriptions",routeName:"show.subscriptions",activeRouteNames:["show.subscriptions","show.subscription"],permission:"View subscriptions"},{type:"link",label:"Transactions",routeName:"show.transactions",activeRouteNames:["show.transactions","show.transaction"],permission:"View billing transactions"},{type:"link",label:"Subscribers",routeName:"show.subscribers",activeRouteNames:["show.subscribers","show.subscriber"],permission:"View subscribers"},{type:"group",label:"Schedules",children:[{label:"Auto Billing Schedules",routeName:"show.auto.billing.schedules",activeRouteNames:["show.auto.billing.schedules"],permission:"View auto billing schedules"},{label:"SMS Schedules",routeName:"show.sms.campaign.schedules",activeRouteNames:["show.sms.campaign.schedules"],permission:"View sms campaign schedules"}]},{type:"link",label:"Analytics",routeName:"show.analytics",activeRouteNames:["show.analytics"]},{type:"link",label:"Reports",routeName:"show.billing.reports",activeRouteNames:["show.billing.reports","show.billing.report"],permission:"View billing reports"},{type:"link",label:"SMS",routeName:"show.subscriber.messages",activeRouteNames:["show.subscriber.messages","show.subscriber.message"],permission:"View subscriber messages"},{type:"group",label:"More",secondary:!0,children:[{label:"About",routeName:"show.project.about",activeRouteNames:["show.project.about"]},{label:"Users",routeName:"show.users",activeRouteNames:["show.users","show.user"],permission:"View users"},{label:"Topics",routeName:"show.topics",activeRouteNames:["show.topics","show.topic"],permission:"View topics"},{label:"Messages",routeName:"show.messages",activeRouteNames:["show.messages","show.message"],permission:"View messages"},{label:"Pricing Plans",routeName:"show.pricing.plans",activeRouteNames:["show.pricing.plans","show.pricing.plan"],permission:"View pricing plans"},{label:"Sms Campaigns",routeName:"show.sms.campaigns",activeRouteNames:["show.sms.campaigns","show.sms.campaign.job.batches"],permission:"View sms campaigns"}]}]),o=$(new Set),s=S(()=>t.value.map(l=>{if(l.type==="link")return X(l.permission??null)?l:null;const a=l.children.filter(i=>{var A,m;return i.requiresSuperAdmin&&!((m=(A=h.props.auth)==null?void 0:A.user)!=null&&m.is_super_admin)?!1:X(i.permission??null)});return a.length?{...l,children:a}:null}).filter(Boolean));function d(l){return l.children.some(a=>a.activeRouteNames.some(i=>route().current(i)))}function r(l){o.value.has(l)?o.value=new Set:o.value=new Set([l])}function b(l){return o.value.has(l)}function _(){const l=route().current();if(l){for(const a of s.value)if(a.type==="group"&&a.children.some(i=>i.activeRouteNames.includes(l))){o.value=new Set([a.label]);break}}}const h=Z(),O=$(!1);we(_),U(()=>h.url,_);const ue=S(()=>route().params.hasOwnProperty("project")),V=S(()=>route().current("show.projects")),X=l=>l==null?!0:h.props.projectPermissions.includes("*")||h.props.projectPermissions.includes(l),N=l=>{for(let i=0;i<l.length;i++){var a=l[i];if(route().current(a))return"bg-gray-100 dark:bg-gray-700/50 border-l-2 border-gray-300 dark:border-gray-500"}return""},C=l=>{const a=route(l,{project:route().params.project});J.get(a)},ce=()=>{O.value=!0,J.post(route("logout"))};U(()=>h.props.project,l=>{const a=(l==null?void 0:l.secret_token)||null;if(a)localStorage.setItem("projectToken",a),window.axios.defaults.headers.common.Authorization=`Bearer ${a}`;else{localStorage.removeItem("projectToken");const i=localStorage.getItem("accessToken");i?window.axios.defaults.headers.common.Authorization=`Bearer ${i}`:delete window.axios.defaults.headers.common.Authorization}},{immediate:!0});const H=S(()=>{var l;return((l=h.props.project)==null?void 0:l.name)??null});return(l,a)=>(u(),c("div",null,[x(R(ke),{title:e.title},null,8,["title"]),x(R(le),{position:"top-right"}),x(gt),a[10]||(a[10]=n("button",{"data-drawer-target":"main-sidebar","data-drawer-toggle":"main-sidebar","aria-controls":"main-sidebar",type:"button",class:"inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"},[n("span",{class:"sr-only"},"Open sidebar"),n("svg",{class:"w-6 h-6","aria-hidden":"true",fill:"currentColor",viewBox:"0 0 20 20",xmlns:"http://www.w3.org/2000/svg"},[n("path",{"clip-rule":"evenodd","fill-rule":"evenodd",d:"M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"})])],-1)),n("aside",{id:"main-sidebar",class:p(["fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0",{"!translate-x-0":V.value}]),"aria-label":"Sidebar"},[n("div",yt,[n("button",{type:"button",onClick:a[0]||(a[0]=i=>C("profile.show")),class:"shrink-0 px-4 pt-4 pb-1.5 border-b border-gray-100 dark:border-gray-600 w-full text-left cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/40 focus:outline-none"},[n("div",vt,[n("div",wt,[n("p",kt,f(l.$page.props.auth.user.name),1),n("p",{class:"text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5",title:l.$page.props.auth.user.email},f(l.$page.props.auth.user.email),9,xt)]),a[2]||(a[2]=n("svg",{class:"w-4 h-4 text-gray-400 shrink-0","aria-hidden":"true",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[n("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 5l7 7-7 7"})],-1))])]),n("nav",St,[n("ul",Ct,[ue.value?(u(),c(w,{key:0},[a[5]||(a[5]=n("li",{class:"mt-1 pt-1.5","aria-hidden":"true"},null,-1)),(u(!0),c(w,null,L(s.value,(i,A)=>(u(),c(w,{key:A},[i.type==="link"?(u(),c("li",{key:0,onClick:m=>C(i.routeName),class:p(["w-full pl-4 pr-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 cursor-pointer rounded-r-lg border-l-2 border-transparent",N(i.activeRouteNames)])},[n("span",null,f(i.label),1)],10,jt)):i.type==="group"&&!i.secondary?(u(),c("li",Pt,[n("div",{onClick:m=>r(i.label),class:p([d(i)?"bg-gray-100 dark:bg-gray-700/50 border-l-2 border-gray-300 dark:border-gray-500":"border-l-2 border-transparent","w-full pl-4 pr-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 cursor-pointer rounded-r-lg flex items-center justify-between"])},[n("span",null,f(i.label),1),(u(),c("svg",{class:p(["w-4 h-4 transition-transform shrink-0 text-gray-500",{"rotate-180":b(i.label)}]),fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[...a[3]||(a[3]=[n("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M19 9l-7 7-7-7"},null,-1)])],2))],10,It),x(R(ne),{active:b(i.label),duration:200,tag:"ul",class:"ml-3 pl-3 space-y-0.5 border-l border-gray-200 dark:border-gray-600"},{default:z(()=>[(u(!0),c(w,null,L(i.children,(m,D)=>(u(),c("li",{key:D,onClick:F(de=>C(m.routeName),["stop"]),class:p(["w-full pl-3 pr-2 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer rounded-r-lg border-l-2 border-transparent",N(m.activeRouteNames)])},[n("span",null,f(m.label),1)],10,Mt))),128))]),_:2},1032,["active"])])):i.type==="group"&&i.secondary?(u(),c("li",Tt,[n("div",{onClick:m=>r(i.label),class:p([d(i)?"bg-gray-100 dark:bg-gray-700/50 border-l-2 border-gray-300 dark:border-gray-500":"border-l-2 border-transparent","w-full pl-4 pr-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 cursor-pointer rounded-r-lg flex items-center justify-between"])},[n("span",null,f(i.label),1),(u(),c("svg",{class:p(["w-4 h-4 transition-transform shrink-0 text-gray-500",{"rotate-180":b(i.label)}]),fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[...a[4]||(a[4]=[n("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M19 9l-7 7-7-7"},null,-1)])],2))],10,_t),x(R(ne),{active:b(i.label),duration:200,tag:"ul",class:"ml-3 pl-3 space-y-0.5 border-l border-gray-200 dark:border-gray-600"},{default:z(()=>[(u(!0),c(w,null,L(i.children,(m,D)=>(u(),c("li",{key:D,onClick:F(de=>C(m.routeName),["stop"]),class:p(["w-full pl-3 pr-2 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer rounded-r-lg border-l-2 border-transparent",N(m.activeRouteNames)])},[n("span",null,f(m.label),1)],10,Ot))),128))]),_:2},1032,["active"])])):v("",!0)],64))),128))],64)):v("",!0)])]),n("div",Nt,[H.value?(u(),c("div",At,[a[6]||(a[6]=n("p",{class:"text-[10px] font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500"},"Current project",-1)),n("p",{class:"text-sm font-medium text-gray-800 dark:text-gray-200 truncate",title:H.value},f(H.value),9,Et)])):v("",!0),n("div",{onClick:a[1]||(a[1]=i=>C("show.projects")),class:p([N(["show.projects"]),"flex items-center gap-2 w-full py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-100 cursor-pointer rounded-r-lg border-l-2 border-transparent -ml-4 pl-4"])},[...a[7]||(a[7]=[n("svg",{class:"w-4 h-4 shrink-0 text-gray-500 dark:text-gray-400",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor"},[n("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75a2.25 2.25 0 0 1 2.25-2.25H6a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 6 20.25h-.75a2.25 2.25 0 0 1-2.25-2.25v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"})],-1),n("span",null,"My Projects",-1)])],2),n("div",{onClick:ce,class:"flex items-center gap-3 w-full py-2 -ml-4 pl-4 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer rounded-r-lg border-l-2 border-transparent"},[O.value?(u(),k(ht,{key:0,class:"my-1 shrink-0"})):(u(),c("svg",Rt,[...a[8]||(a[8]=[n("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"},null,-1)])])),a[9]||(a[9]=n("span",null,"Sign Out",-1))])])])],2),n("div",{class:p(["min-h-screen bg-slate-50 dark:bg-slate-900/30",{"sm:ml-64":!V.value,"ml-64":V.value}])},[xe(l.$slots,"default")],2)]))}};export{ht as S,Dt as _};
