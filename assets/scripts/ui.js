(()=>{var t={655:(t,e,r)=>{var o,a,n;!function(i){"use strict";a=[r(311)],void 0===(n="function"==typeof(o=function(t){var e=-1,r=-1,o=function(t){return parseFloat(t)||0},a=function(e){var r=t(e),a=null,n=[];return r.each((function(){var e=t(this),r=e.offset().top-o(e.css("margin-top")),i=n.length>0?n[n.length-1]:null;null===i?n.push(e):Math.floor(Math.abs(a-r))<=1?n[n.length-1]=i.add(e):n.push(e),a=r})),n},n=function(e){var r={byRow:!0,property:"height",target:null,remove:!1};return"object"==typeof e?t.extend(r,e):("boolean"==typeof e?r.byRow=e:"remove"===e&&(r.remove=!0),r)},i=t.fn.matchHeight=function(e){var r=n(e);if(r.remove){var o=this;return this.css(r.property,""),t.each(i._groups,(function(t,e){e.elements=e.elements.not(o)})),this}return this.length<=1&&!r.target||(i._groups.push({elements:this,options:r}),i._apply(this,r)),this};i.version="0.7.2",i._groups=[],i._throttle=80,i._maintainScroll=!1,i._beforeUpdate=null,i._afterUpdate=null,i._rows=a,i._parse=o,i._parseOptions=n,i._apply=function(e,r){var s=n(r),c=t(e),l=[c],p=t(window).scrollTop(),h=t("html").outerHeight(!0),d=c.parents().filter(":hidden");return d.each((function(){var e=t(this);e.data("style-cache",e.attr("style"))})),d.css("display","block"),s.byRow&&!s.target&&(c.each((function(){var e=t(this),r=e.css("display");"inline-block"!==r&&"flex"!==r&&"inline-flex"!==r&&(r="block"),e.data("style-cache",e.attr("style")),e.css({display:r,"padding-top":"0","padding-bottom":"0","margin-top":"0","margin-bottom":"0","border-top-width":"0","border-bottom-width":"0",height:"100px",overflow:"hidden"})})),l=a(c),c.each((function(){var e=t(this);e.attr("style",e.data("style-cache")||"")}))),t.each(l,(function(e,r){var a=t(r),n=0;if(s.target)n=s.target.outerHeight(!1);else{if(s.byRow&&a.length<=1)return void a.css(s.property,"");a.each((function(){var e=t(this),r=e.attr("style"),o=e.css("display");"inline-block"!==o&&"flex"!==o&&"inline-flex"!==o&&(o="block");var a={display:o};a[s.property]="",e.css(a),e.outerHeight(!1)>n&&(n=e.outerHeight(!1)),r?e.attr("style",r):e.css("display","")}))}a.each((function(){var e=t(this),r=0;s.target&&e.is(s.target)||("border-box"!==e.css("box-sizing")&&(r+=o(e.css("border-top-width"))+o(e.css("border-bottom-width")),r+=o(e.css("padding-top"))+o(e.css("padding-bottom"))),e.css(s.property,n-r+"px"))}))})),d.each((function(){var e=t(this);e.attr("style",e.data("style-cache")||null)})),i._maintainScroll&&t(window).scrollTop(p/h*t("html").outerHeight(!0)),this},i._applyDataApi=function(){var e={};t("[data-match-height], [data-mh]").each((function(){var r=t(this),o=r.attr("data-mh")||r.attr("data-match-height");e[o]=o in e?e[o].add(r):r})),t.each(e,(function(){this.matchHeight(!0)}))};var s=function(e){i._beforeUpdate&&i._beforeUpdate(e,i._groups),t.each(i._groups,(function(){i._apply(this.elements,this.options)})),i._afterUpdate&&i._afterUpdate(e,i._groups)};i._update=function(o,a){if(a&&"resize"===a.type){var n=t(window).width();if(n===e)return;e=n}o?-1===r&&(r=setTimeout((function(){s(a),r=-1}),i._throttle)):s(a)},t(i._applyDataApi);var c=t.fn.on?"on":"bind";t(window)[c]("load",(function(t){i._update(!1,t)})),t(window)[c]("resize orientationchange",(function(t){i._update(!0,t)}))})?o.apply(e,a):o)||(t.exports=n)}()},311:t=>{"use strict";t.exports=jQuery}},e={};function r(o){var a=e[o];if(void 0!==a)return a.exports;var n=e[o]={exports:{}};return t[o](n,n.exports,r),n.exports}r.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return r.d(e,{a:e}),e},r.d=(t,e)=>{for(var o in e)r.o(e,o)&&!r.o(t,o)&&Object.defineProperty(t,o,{enumerable:!0,get:e[o]})},r.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),(()=>{"use strict";r(655);const t=(t,e)=>{if(e){const e=shp_gantrisch_adb.debug?"":".min";let r=document.createElement("script");r.setAttribute("src",`${shp_gantrisch_adb.url}/assets/scripts/${t}${e}.js?version=${shp_gantrisch_adb.version}`),document.head.appendChild(r)}};t("accordion",!!document.querySelectorAll("[data-shp-accordion-entry]").length),t("fancybox",!!document.querySelectorAll("[data-fancybox]").length)})()})();