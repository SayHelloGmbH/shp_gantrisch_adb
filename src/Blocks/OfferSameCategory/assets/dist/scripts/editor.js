!function(){"use strict";var e=window.wp.blocks,t=window.wp.blockEditor,s=window.wp.components,r=window.wp.i18n,a=JSON.parse('{"name":"shp/adb-offer-same-category","title":"ADB More Offers","description":"List view of all offers in the same category as the current single offer.","category":"media","icon":"database","apiVersion":2,"keywords":["offer","angebot","list","more"],"supports":{"align":true,"color":{"text":false,"background":true,"link":false}},"attributes":{"title":{"type":"string","default":"Weitere Angebote"}},"editorScript":"file:./assets/dist/scripts/editor.js","viewScript":"file:./assets/dist/scripts/view.js","style":["file:./assets/dist/styles/shared/index.min.css","shp-gantrisch-adb-offer-same-category"],"render":"file:./render.php"}');const{name:i}=a;(0,e.registerBlockType)(i,{edit:e=>{let{attributes:a,setAttributes:i}=e;const n=(0,t.useBlockProps)(),{title:o}=a;return React.createElement(React.Fragment,null,React.createElement(t.InspectorControls,null,React.createElement(s.PanelBody,{title:(0,r.__)("Settings"),initialOpen:!0},React.createElement(s.TextControl,{label:(0,r._x)("Title","TextControl label","shp_gantrisch_adb"),value:o,onChange:e=>i({title:e})}))),React.createElement("div",n,React.createElement("div",{className:"c-message c-message--info",dangerouslySetInnerHTML:{__html:(0,r._x)("Posts from the same category.","Editor message","shp_gantrisch_adb")}})))}})}();