(()=>{"use strict";var e={869:(e,t,r)=>{r.d(t,{Z:()=>i});var n=r(81),a=r.n(n),o=r(645),s=r.n(o)()(a());s.push([e.id,".shp_gantrisch_adb__message.is--hidden{display:none}.shp_gantrisch_adb__message.is--error{color:red}.shp_gantrisch_adb__message.is--success{color:green}.shp_gantrisch_adb__button-wrapper>*{margin:0}.shp_gantrisch_adb__button-wrapper>*+*{margin-top:1em}",""]);const i=s},645:e=>{e.exports=function(e){var t=[];return t.toString=function(){return this.map((function(t){var r="",n=void 0!==t[5];return t[4]&&(r+="@supports (".concat(t[4],") {")),t[2]&&(r+="@media ".concat(t[2]," {")),n&&(r+="@layer".concat(t[5].length>0?" ".concat(t[5]):""," {")),r+=e(t),n&&(r+="}"),t[2]&&(r+="}"),t[4]&&(r+="}"),r})).join("")},t.i=function(e,r,n,a,o){"string"==typeof e&&(e=[[null,e,void 0]]);var s={};if(n)for(var i=0;i<this.length;i++){var c=this[i][0];null!=c&&(s[c]=!0)}for(var d=0;d<e.length;d++){var u=[].concat(e[d]);n&&s[u[0]]||(void 0!==o&&(void 0===u[5]||(u[1]="@layer".concat(u[5].length>0?" ".concat(u[5]):""," {").concat(u[1],"}")),u[5]=o),r&&(u[2]?(u[1]="@media ".concat(u[2]," {").concat(u[1],"}"),u[2]=r):u[2]=r),a&&(u[4]?(u[1]="@supports (".concat(u[4],") {").concat(u[1],"}"),u[4]=a):u[4]="".concat(a)),t.push(u))}},t}},81:e=>{e.exports=function(e){return e[1]}},379:e=>{var t=[];function r(e){for(var r=-1,n=0;n<t.length;n++)if(t[n].identifier===e){r=n;break}return r}function n(e,n){for(var o={},s=[],i=0;i<e.length;i++){var c=e[i],d=n.base?c[0]+n.base:c[0],u=o[d]||0,l="".concat(d," ").concat(u);o[d]=u+1;var p=r(l),f={css:c[1],media:c[2],sourceMap:c[3],supports:c[4],layer:c[5]};if(-1!==p)t[p].references++,t[p].updater(f);else{var h=a(f,n);n.byIndex=i,t.splice(i,0,{identifier:l,updater:h,references:1})}s.push(l)}return s}function a(e,t){var r=t.domAPI(t);return r.update(e),function(t){if(t){if(t.css===e.css&&t.media===e.media&&t.sourceMap===e.sourceMap&&t.supports===e.supports&&t.layer===e.layer)return;r.update(e=t)}else r.remove()}}e.exports=function(e,a){var o=n(e=e||[],a=a||{});return function(e){e=e||[];for(var s=0;s<o.length;s++){var i=r(o[s]);t[i].references--}for(var c=n(e,a),d=0;d<o.length;d++){var u=r(o[d]);0===t[u].references&&(t[u].updater(),t.splice(u,1))}o=c}}},569:e=>{var t={};e.exports=function(e,r){var n=function(e){if(void 0===t[e]){var r=document.querySelector(e);if(window.HTMLIFrameElement&&r instanceof window.HTMLIFrameElement)try{r=r.contentDocument.head}catch(e){r=null}t[e]=r}return t[e]}(e);if(!n)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");n.appendChild(r)}},216:e=>{e.exports=function(e){var t=document.createElement("style");return e.setAttributes(t,e.attributes),e.insert(t,e.options),t}},565:(e,t,r)=>{e.exports=function(e){var t=r.nc;t&&e.setAttribute("nonce",t)}},795:e=>{e.exports=function(e){if("undefined"==typeof document)return{update:function(){},remove:function(){}};var t=e.insertStyleElement(e);return{update:function(r){!function(e,t,r){var n="";r.supports&&(n+="@supports (".concat(r.supports,") {")),r.media&&(n+="@media ".concat(r.media," {"));var a=void 0!==r.layer;a&&(n+="@layer".concat(r.layer.length>0?" ".concat(r.layer):""," {")),n+=r.css,a&&(n+="}"),r.media&&(n+="}"),r.supports&&(n+="}");var o=r.sourceMap;o&&"undefined"!=typeof btoa&&(n+="\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(o))))," */")),t.styleTagTransform(n,e,t.options)}(t,e,r)},remove:function(){!function(e){if(null===e.parentNode)return!1;e.parentNode.removeChild(e)}(t)}}}},589:e=>{e.exports=function(e,t){if(t.styleSheet)t.styleSheet.cssText=e;else{for(;t.firstChild;)t.removeChild(t.firstChild);t.appendChild(document.createTextNode(e))}}}},t={};function r(n){var a=t[n];if(void 0!==a)return a.exports;var o=t[n]={id:n,exports:{}};return e[n](o,o.exports,r),o.exports}r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t},r.d=(e,t)=>{for(var n in t)r.o(t,n)&&!r.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r.nc=void 0,(()=>{var e=r(379),t=r.n(e),n=r(795),a=r.n(n),o=r(569),s=r.n(o),i=r(565),c=r.n(i),d=r(216),u=r.n(d),l=r(589),p=r.n(l),f=r(869),h={};h.styleTagTransform=p(),h.setAttributes=c(),h.insert=s().bind(null,"head"),h.domAPI=a(),h.insertStyleElement=u(),t()(f.Z,h),f.Z&&f.Z.locals&&f.Z.locals;const v=document.querySelectorAll("[data-shp_gantrisch_adb-doupdate]"),m=`${shp_gantrisch_adb.url}shp_gantrisch_adb/update-from-api`,b=(e,t,r)=>{const n=e.parentNode.querySelector("[data-shp_gantrisch_adb-api-response]");n.textContent=t,n.classList.add(`is--${r}`),n.classList.remove("is--hidden"),setTimeout((()=>g(e)),5e3)},g=e=>{const t=e.parentNode.querySelector("[data-shp_gantrisch_adb-api-response]");t.classList.add("is--hidden"),t.classList.remove("is--success","is--error"),t.textContent=""};async function y(e){e.preventDefault();const t=e.currentTarget;t.setAttribute("disabled","disabled");const r=t.textContent;t.textContent=t.dataset.textWait,g(t);const n=await fetch(m,{headers:{"Content-Type":"application/json","X-WP-Nonce":shp_gantrisch_adb.nonce}});if(!n.ok)switch(n.status){case 404:throw t.removeAttribute("disabled"),t.textContent=r,b(t,"Die Schnittstelle konnte nicht erreicht werden. (404)","error"),new Error("Die Antwort von der Schnittstelle konnte nicht verarbeitet werden. (404)");case 500:throw t.removeAttribute("disabled"),t.textContent=r,b(t,"Ein unerwarteter Fehler ist aufgetreten. (500)","error"),new Error("Ein unerwarteter Fehler ist aufgetreten. (500)");default:throw t.removeAttribute("disabled"),t.textContent=r,b(t,`${n.message} (${n.status})`,"error"),new Error(`${n.message} (${n.status})`)}t.removeAttribute("disabled"),t.textContent=r,b(t,"Die ADB-Daten wurden erfolgreich aktualisiert","success")}v.length&&v.forEach((e=>{e.addEventListener("click",y),e.removeAttribute("disabled")}))})()})();