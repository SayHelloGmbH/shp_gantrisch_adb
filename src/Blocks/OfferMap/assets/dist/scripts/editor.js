!function(){"use strict";var e=window.wp.blocks,t=window.wp.blockEditor,s=window.wp.i18n,a=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"shp/gantrisch-adb-offer-map","title":"ADB Offer Map","category":"media","icon":"database","description":"The actual content will be loaded dynamically by passing an offer ID to the page on which this block has been placed.","keywords":["offer","angebot","map"],"textdomain":"shp_gantrisch_adb","attributes":{"align":{"type":"string"},"backgroundColor":{"type":"string"},"title":{"type":"string","default":"Karte"}},"supports":{"align":["wide","full"],"html":false,"multiple":false},"editorScript":"file:./assets/dist/scripts/editor.js","style":"file:./assets/dist/styles/view/index.min.css","render":"file:./render.php"}');const{name:i}=a;(0,e.registerBlockType)(i,{edit:()=>{const e=(0,t.useBlockProps)();return React.createElement("div",e,React.createElement("div",{className:"c-message c-message--info",dangerouslySetInnerHTML:{__html:(0,s._x)("ADB Single offer map.","Editor message","shp_gantrisch_adb")}}))}})}();