!function(){"use strict";var e=window.wp.blocks,t=window.wp.blockEditor,s=window.wp.i18n,a=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"shp/gantrisch-adb-offer-event-location","title":"ADB Offer Event Location","category":"media","icon":"database","description":"The actual content will be loaded dynamically by passing an offer ID to the page on which this block has been placed.","keywords":["offer","angebot","event","veranstaltungsort"],"textdomain":"shp_gantrisch_adb","attributes":{"align":{"type":"string"}},"supports":{"align":false,"html":false},"editorScript":"file:./assets/dist/scripts/editor.js","style":["file:./assets/dist/styles/shared/index.min.css","shp-gantrisch-adb-offer-event-location"],"render":"file:./render.php"}');const{name:n}=a;(0,e.registerBlockType)(n,{edit:()=>{const e=(0,t.useBlockProps)();return React.createElement("div",e,React.createElement("div",{className:"c-message c-message--info",dangerouslySetInnerHTML:{__html:(0,s._x)("Placeholder for ADB event location","Editor message","shp_gantrisch_adb")}}))}})}();