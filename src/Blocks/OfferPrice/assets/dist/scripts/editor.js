!function(){"use strict";var e=window.wp.blocks,t=window.wp.blockEditor,a=window.wp.i18n,r=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"shp/gantrisch-adb-offer-price","title":"ADB Offer Price","category":"media","icon":"format-image","description":"The actual content will be loaded dynamically by passing an offer ID to the page on which this block has been placed.","keywords":["offer","angebot","price"],"textdomain":"shp_gantrisch_adb","attributes":{"align":{"type":"string"},"backgroundColor":{"type":"string"},"textColor":{"type":"string"}},"supports":{"align":["wide","full"],"html":false,"color":{"text":true,"background":true}},"editorScript":"file:./assets/dist/scripts/editor.js"}'),s=React.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24",width:"24"},React.createElement("path",{d:"M12 11q-3.75 0-6.375-1.175T3 7q0-1.65 2.625-2.825Q8.25 3 12 3t6.375 1.175Q21 5.35 21 7q0 1.65-2.625 2.825Q15.75 11 12 11Zm0 5q-3.75 0-6.375-1.175T3 12V9.5q0 1.1 1.025 1.863 1.025.762 2.45 1.237 1.425.475 2.963.687 1.537.213 2.562.213t2.562-.213q1.538-.212 2.963-.687 1.425-.475 2.45-1.237Q21 10.6 21 9.5V12q0 1.65-2.625 2.825Q15.75 16 12 16Zm0 5q-3.75 0-6.375-1.175T3 17v-2.5q0 1.1 1.025 1.863 1.025.762 2.45 1.237 1.425.475 2.963.688 1.537.212 2.562.212t2.562-.212q1.538-.213 2.963-.688t2.45-1.237Q21 15.6 21 14.5V17q0 1.65-2.625 2.825Q15.75 21 12 21Z"}));const{name:i}=r,o=(0,e.getBlockDefaultClassName)(i);(0,e.registerBlockType)(i,{icon:s,edit:()=>{const e=(0,t.useBlockProps)();return React.createElement("div",e,React.createElement("div",{className:`${o}__content`,dangerouslySetInnerHTML:{__html:(0,a._x)("Single offer price.","Editor message","shp_gantrisch_adb")}}))}})}();