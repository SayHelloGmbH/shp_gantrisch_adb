(()=>{window.NodeList&&!NodeList.prototype.forEach&&(NodeList.prototype.forEach=function(t,e){var n,o=this.length;for(e=e||window,n=0;n<o;n++)t.call(e,this[n],n,this)}),window.Element&&!Element.prototype.closest&&(Element.prototype.closest=function(t){var e,n=(this.document||this.ownerDocument).querySelectorAll(t),o=this;do{for(e=n.length;--e>=0&&n.item(e)!==o;);}while(e<0&&(o=o.parentElement));return o});const t="wp-block-acf-shp-adb-list-default",e=document.querySelectorAll(`.${t}`),n=Math.max(parseInt(shp_gantrisch_adb_block_list_default.initial_count),1);e.forEach((e=>{const o=e.querySelectorAll(`.${t}__entry:nth-child(-n+${n})`),r=e.querySelectorAll(`.${t}__entry:nth-child(n+${n+1})`);o.forEach((t=>{t.classList.remove("is--hidden")}));const l=e.querySelector(`.${t}__entry:nth-child(13)`);if(!l)return;const i=document.createElement("div");if(i.classList.add(`${t}__loadbutton`,"c-adb-list__loadbutton"),shp_gantrisch_adb_block_list_default){const t=document.createElement("button");t.innerHTML=shp_gantrisch_adb_block_list_default.load_more_text,t.addEventListener("click",(t=>{t.preventDefault(),t.currentTarget.blur(),((t,e)=>{const n=t.parentNode;n&&n.parentNode&&n.parentNode.removeChild(n),e.forEach((t=>{t.classList.remove("is--hidden")}))})(t.currentTarget,r)})),i.appendChild(t),l.parentNode.insertBefore(i,l)}}));const o=()=>{const e=document.querySelectorAll(`.${t}__entry-image`);if(e.length){const t=getComputedStyle(e[0]).width;e.forEach((e=>{e.setAttribute("sizes",t)}))}};var r,l;o(),window.addEventListener("load",o),window.addEventListener("resize",(r=o,350,l=Date.now(),function(){l+350-Date.now()<0&&(r(),l=Date.now())})),jQuery.fn.matchHeight._throttle=350,jQuery(`.${t}__entry-header`).matchHeight({})})();