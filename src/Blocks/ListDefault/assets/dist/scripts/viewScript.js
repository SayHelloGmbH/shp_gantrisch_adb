!function(){var t={92:function(t,e,n){var o,r,i;!function(a){"use strict";r=[n(567)],void 0===(i="function"==typeof(o=function(t){var e=-1,n=-1,o=function(t){return parseFloat(t)||0},r=function(e){var n=t(e),r=null,i=[];return n.each((function(){var e=t(this),n=e.offset().top-o(e.css("margin-top")),a=i.length>0?i[i.length-1]:null;null===a?i.push(e):Math.floor(Math.abs(r-n))<=1?i[i.length-1]=a.add(e):i.push(e),r=n})),i},i=function(e){var n={byRow:!0,property:"height",target:null,remove:!1};return"object"==typeof e?t.extend(n,e):("boolean"==typeof e?n.byRow=e:"remove"===e&&(n.remove=!0),n)},a=t.fn.matchHeight=function(e){var n=i(e);if(n.remove){var o=this;return this.css(n.property,""),t.each(a._groups,(function(t,e){e.elements=e.elements.not(o)})),this}return this.length<=1&&!n.target||(a._groups.push({elements:this,options:n}),a._apply(this,n)),this};a.version="0.7.2",a._groups=[],a._throttle=80,a._maintainScroll=!1,a._beforeUpdate=null,a._afterUpdate=null,a._rows=r,a._parse=o,a._parseOptions=i,a._apply=function(e,n){var s=i(n),c=t(e),l=[c],h=t(window).scrollTop(),u=t("html").outerHeight(!0),d=c.parents().filter(":hidden");return d.each((function(){var e=t(this);e.data("style-cache",e.attr("style"))})),d.css("display","block"),s.byRow&&!s.target&&(c.each((function(){var e=t(this),n=e.css("display");"inline-block"!==n&&"flex"!==n&&"inline-flex"!==n&&(n="block"),e.data("style-cache",e.attr("style")),e.css({display:n,"padding-top":"0","padding-bottom":"0","margin-top":"0","margin-bottom":"0","border-top-width":"0","border-bottom-width":"0",height:"100px",overflow:"hidden"})})),l=r(c),c.each((function(){var e=t(this);e.attr("style",e.data("style-cache")||"")}))),t.each(l,(function(e,n){var r=t(n),i=0;if(s.target)i=s.target.outerHeight(!1);else{if(s.byRow&&r.length<=1)return void r.css(s.property,"");r.each((function(){var e=t(this),n=e.attr("style"),o=e.css("display");"inline-block"!==o&&"flex"!==o&&"inline-flex"!==o&&(o="block");var r={display:o};r[s.property]="",e.css(r),e.outerHeight(!1)>i&&(i=e.outerHeight(!1)),n?e.attr("style",n):e.css("display","")}))}r.each((function(){var e=t(this),n=0;s.target&&e.is(s.target)||("border-box"!==e.css("box-sizing")&&(n+=o(e.css("border-top-width"))+o(e.css("border-bottom-width")),n+=o(e.css("padding-top"))+o(e.css("padding-bottom"))),e.css(s.property,i-n+"px"))}))})),d.each((function(){var e=t(this);e.attr("style",e.data("style-cache")||null)})),a._maintainScroll&&t(window).scrollTop(h/u*t("html").outerHeight(!0)),this},a._applyDataApi=function(){var e={};t("[data-match-height], [data-mh]").each((function(){var n=t(this),o=n.attr("data-mh")||n.attr("data-match-height");e[o]=o in e?e[o].add(n):n})),t.each(e,(function(){this.matchHeight(!0)}))};var s=function(e){a._beforeUpdate&&a._beforeUpdate(e,a._groups),t.each(a._groups,(function(){a._apply(this.elements,this.options)})),a._afterUpdate&&a._afterUpdate(e,a._groups)};a._update=function(o,r){if(r&&"resize"===r.type){var i=t(window).width();if(i===e)return;e=i}o?-1===n&&(n=setTimeout((function(){s(r),n=-1}),a._throttle)):s(r)},t(a._applyDataApi);var c=t.fn.on?"on":"bind";t(window)[c]("load",(function(t){a._update(!1,t)})),t(window)[c]("resize orientationchange",(function(t){a._update(!0,t)}))})?o.apply(e,r):o)||(t.exports=i)}()},567:function(t){"use strict";t.exports=window.jQuery}},e={};function n(o){var r=e[o];if(void 0!==r)return r.exports;var i=e[o]={exports:{}};return t[o](i,i.exports,n),i.exports}n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,{a:e}),e},n.d=function(t,e){for(var o in e)n.o(e,o)&&!n.o(t,o)&&Object.defineProperty(t,o,{enumerable:!0,get:e[o]})},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},function(){"use strict";n(92),window.NodeList&&!NodeList.prototype.forEach&&(NodeList.prototype.forEach=function(t,e){var n,o=this.length;for(e=e||window,n=0;n<o;n++)t.call(e,this[n],n,this)}),window.Element&&!Element.prototype.closest&&(Element.prototype.closest=function(t){var e,n=(this.document||this.ownerDocument).querySelectorAll(t),o=this;do{for(e=n.length;--e>=0&&n.item(e)!==o;);}while(e<0&&(o=o.parentElement));return o});const t="wp-block-acf-shp-adb-list-default",e=document.querySelectorAll(`.${t}`),o=Math.max(parseInt(shp_gantrisch_adb_block_list_default.initial_count),1);console.log(o),e.forEach((e=>{const n=e.querySelectorAll(`.${t}__entry:nth-child(-n+${o})`),r=e.querySelectorAll(`.${t}__entry:nth-child(n+${o+1})`);n.forEach((t=>{t.classList.remove("is--hidden")}));const i=e.querySelector(`.${t}__entry:nth-child(13)`),a=document.createElement("div");if(a.classList.add(`${t}__loadbutton`),shp_gantrisch_adb_block_list_default){const t=document.createElement("button");t.innerHTML=shp_gantrisch_adb_block_list_default.load_more_text,t.addEventListener("click",(t=>{t.preventDefault(),t.currentTarget.blur(),((t,e)=>{const n=t.parentNode;n&&n.parentNode&&n.parentNode.removeChild(n),e.forEach((t=>{t.classList.remove("is--hidden")}))})(t.currentTarget,r)})),a.appendChild(t),i.parentNode.insertBefore(a,i)}})),jQuery.fn.matchHeight._throttle=350,jQuery(`.${t}__entry-header`).matchHeight({})}()}();