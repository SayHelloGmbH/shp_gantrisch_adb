!function(){window.NodeList&&!NodeList.prototype.forEach&&(NodeList.prototype.forEach=function(t,e){var n,r=this.length;for(e=e||window,n=0;n<r;n++)t.call(e,this[n],n,this)}),window.Element&&!Element.prototype.closest&&(Element.prototype.closest=function(t){var e,n=(this.document||this.ownerDocument).querySelectorAll(t),r=this;do{for(e=n.length;--e>=0&&n.item(e)!==r;);}while(e<0&&(r=r.parentElement));return r});const t="wp-block-acf-shp-adb-list-default",e=document.querySelectorAll(`.${t}`),n=Math.max(parseInt(shp_gantrisch_adb_block_list_default.initial_count),1);e.forEach((e=>{const r=e.querySelectorAll(`.${t}__entry:nth-child(-n+${n})`),o=e.querySelectorAll(`.${t}__entry:nth-child(n+${n+1})`);r.forEach((t=>{t.classList.remove("is--hidden")}));const a=e.querySelector(`.${t}__entry:nth-child(13)`);if(!a)return;const l=document.createElement("div");if(l.classList.add(`${t}__loadbutton`,"c-adb-list__loadbutton"),shp_gantrisch_adb_block_list_default){const t=document.createElement("button");t.innerHTML=shp_gantrisch_adb_block_list_default.load_more_text,t.addEventListener("click",(t=>{t.preventDefault(),t.currentTarget.blur(),((t,e)=>{const n=t.parentNode;n&&n.parentNode&&n.parentNode.removeChild(n),e.forEach((t=>{t.classList.remove("is--hidden")}))})(t.currentTarget,o)})),l.appendChild(t),a.parentNode.insertBefore(l,a)}})),document.querySelectorAll(".wp-block-acf-shp-adb-list-default .listing_entry .entry_link").forEach((t=>{(t=>{const e=document.createElement("a"),n=document.createElement("div"),r=t.closest("[data-class-name-base]").dataset.classNameBase,o=t.closest("[data-button-text]").dataset.buttonText,a=document.createTextNode(o);n.classList.add(`${r}__entry-buttowrapper`,"c-adb-list__entry-buttonwrapper"),e.classList.add(`${r}__entry-button`,"c-adb-list__entry-button"),e.appendChild(a),e.setAttribute("href",t.getAttribute("href")),n.appendChild(e),t.parentNode.insertBefore(n,t.nextSibling)})(t)})),document.querySelectorAll(".wp-block-acf-shp-adb-list-default .listing_entry .tipp.parkpartner").forEach((t=>{t.closest(".listing_entry").classList.add("is--parkpartner")})),document.querySelectorAll(".wp-block-acf-shp-adb-list-default .listing_entry .tipp:not(.parkpartner)").forEach((t=>{t.closest(".listing_entry").classList.add("is--tipp")}));const r=()=>{const e=document.querySelectorAll(`.${t}__entry-image`);if(e.length){const t=getComputedStyle(e[0]).width;e.forEach((e=>{e.setAttribute("sizes",t)}))}};var o,a;r(),window.addEventListener("load",r),window.addEventListener("resize",(o=r,350,a=Date.now(),function(){a+350-Date.now()<0&&(o(),a=Date.now())})),jQuery.fn.matchHeight._throttle=350,jQuery(`.${t}__entry-header`).matchHeight({})}();