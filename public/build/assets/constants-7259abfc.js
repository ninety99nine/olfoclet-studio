import{e as r}from"./base-3ecad56a.js";import{k as s,u as a,S as c,A as m}from"./app-8047a45c.js";const t={prefix:Math.floor(Math.random()*1e4),current:0},f=Symbol("elIdInjection"),i=()=>c()?m(f,t):t,p=o=>{const e=i(),n=r();return s(()=>a(o)||`${n.value}-id-${e.prefix}-${e.current++}`)},l=Symbol("formContextKey"),x=Symbol("formItemContextKey");export{i as a,l as b,x as f,p as u};