(()=>{"use strict";const e=window.wp.blocks,s=window.wp.blockEditor,t=(window.wp.i18n,JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"shp/gantrisch-adb-offer-keywords","title":"ADB Offer Keywords","category":"media","icon":"database","description":"The actual content will be loaded dynamically by passing an offer ID to the page on which this block has been placed. Add each keyword on a new line in the original database.","keywords":["offer","angebot","keywords"],"textdomain":"shp_gantrisch_adb","attributes":{"align":{"type":"string"}},"supports":{"align":["wide","full"],"html":false,"color":{"text":true,"background":true},"spacing":{"padding":true}},"editorScript":"file:./assets/dist/scripts/editor.js","editorStyle":"file:./assets/dist/styles/editor/index.min.css","style":["file:./assets/dist/styles/shared/index.min.css","shp-gantrisch-adb-offer-keywords-shared"]}')),{name:a}=t,i=(0,e.getBlockDefaultClassName)(a);(0,e.registerBlockType)(a,{edit:()=>{const e=(0,s.useBlockProps)();return React.createElement("div",e,React.createElement("div",{className:`${i}__entries`},["Keyword 1","Keyword 2","Keyword 3"].map(((e,s)=>React.createElement("div",{key:s,className:`${i}__entry`,dangerouslySetInnerHTML:{__html:e}})))))}})})();