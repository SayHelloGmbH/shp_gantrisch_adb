!function(){"use strict";var e=window.wp.blocks,t=window.wp.blockEditor,s=window.wp.components,a=window.wp.i18n,n=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"shp/gantrisch-adb-offer-route-length","title":"ADB Offer Route Length","category":"media","icon":"database","description":"The actual content will be loaded dynamically by passing an offer ID to the page on which this block has been placed.","keywords":["offer","angebot","route length"],"textdomain":"shp_gantrisch_adb","attributes":{"align":{"type":"string"},"prefix":{"type":"string","default":"Routenlänge:"}},"supports":{"align":false,"html":false},"editorScript":"file:./assets/dist/scripts/editor.js","style":["file:./assets/dist/styles/shared/index.min.css","shp-gantrisch-adb-offer-route-length"],"render":"file:./render.php"}');const{name:r}=n;(0,e.registerBlockType)(r,{edit:e=>{let{attributes:n,setAttributes:r}=e;const i=(0,t.useBlockProps)(),{prefix:l}=n;return React.createElement(React.Fragment,null,React.createElement(t.InspectorControls,null,React.createElement(s.PanelBody,{title:(0,a.__)("Settings"),initialOpen:!0},React.createElement(s.TextControl,{label:(0,a._x)("Text prefix","TextControl label","shp_gantrisch_adb"),value:l,onChange:e=>r({prefix:e})}))),React.createElement("div",i,React.createElement("div",{className:"c-message c-message--info",dangerouslySetInnerHTML:{__html:(0,a._x)("Placeholder for ADB route length.","Editor message","shp_gantrisch_adb")}})))}})}();