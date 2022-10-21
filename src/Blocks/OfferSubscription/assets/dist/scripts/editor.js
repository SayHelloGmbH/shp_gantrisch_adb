!function(){"use strict";var e=window.wp.blocks,t=window.wp.blockEditor,a=window.wp.components,n=window.wp.i18n,l=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"shp/gantrisch-adb-offer-subscription","title":"ADB Offer Subscription Text","category":"media","icon":"format-image","description":"The actual content will be loaded dynamically by passing an offer ID to the page on which this block has been placed.","keywords":["offer","angebot","anmeldung"],"textdomain":"shp_gantrisch_adb","attributes":{"align":{"type":"string"},"backgroundColor":{"type":"string"},"textColor":{"type":"string"},"button_text":{"type":"string"},"message":{"type":"string"},"title_sub_required":{"type":"string"},"title_sub_at":{"type":"string"}},"supports":{"align":["wide","full"],"html":false,"color":{"text":true,"background":true},"spacing":{"padding":true}},"editorScript":"file:./assets/dist/scripts/editor.js"}'),s=React.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24",width:"24"},React.createElement("path",{d:"M12 11q-3.75 0-6.375-1.175T3 7q0-1.65 2.625-2.825Q8.25 3 12 3t6.375 1.175Q21 5.35 21 7q0 1.65-2.625 2.825Q15.75 11 12 11Zm0 5q-3.75 0-6.375-1.175T3 12V9.5q0 1.1 1.025 1.863 1.025.762 2.45 1.237 1.425.475 2.963.687 1.537.213 2.562.213t2.562-.213q1.538-.212 2.963-.687 1.425-.475 2.45-1.237Q21 10.6 21 9.5V12q0 1.65-2.625 2.825Q15.75 16 12 16Zm0 5q-3.75 0-6.375-1.175T3 17v-2.5q0 1.1 1.025 1.863 1.025.762 2.45 1.237 1.425.475 2.963.688 1.537.212 2.562.212t2.562-.212q1.538-.213 2.963-.688t2.45-1.237Q21 15.6 21 14.5V17q0 1.65-2.625 2.825Q15.75 21 12 21Z"}));const{name:i}=l,r=(0,e.getBlockDefaultClassName)(i);(0,e.registerBlockType)(i,{icon:s,edit:e=>{let{attributes:l,setAttributes:s}=e;const i=(0,t.useBlockProps)(),{button_text:o,message:c,title_sub_at:u,title_sub_required:m}=l;return React.createElement(React.Fragment,null,React.createElement(t.InspectorControls,null,React.createElement(a.PanelBody,{title:(0,n._x)("Settings"),initialOpen:!0},React.createElement(a.TextControl,{label:(0,n._x)("Title (only if subscription required)","TextControl label","shp_gantrisch_adb"),value:m,onChange:e=>s({title_sub_required:e})}),React.createElement(a.TextControl,{label:(0,n._x)("Text (if subscription is required)","TextControl label","shp_gantrisch_adb"),value:c,onChange:e=>s({message:e})}),React.createElement(a.TextControl,{label:(0,n._x)("Title - where to subscribe","TextControl label","shp_gantrisch_adb"),value:u,onChange:e=>s({title_sub_at:e})}),React.createElement(a.TextControl,{label:(0,n._x)("Button text","TextControl label","shp_gantrisch_adb"),value:o,onChange:e=>s({button_text:e})}))),React.createElement("div",i,React.createElement("div",{className:`${r}__content`},m&&React.createElement(t.RichText.Content,{tagName:"h2",className:`${r}__title`,value:m}),c&&React.createElement(t.RichText.Content,{tagName:"div",className:`${r}__message`,value:c}),u&&React.createElement(t.RichText.Content,{tagName:"h2",className:`${r}__title`,value:u}),React.createElement("div",{dangerouslySetInnerHTML:{__html:(0,n._x)("Single offer subscription information.","Editor message","shp_gantrisch_adb")}}),o&&React.createElement("div",{className:"wp-block-button"},React.createElement(t.RichText.Content,{tagName:"div",className:`wp-block-button__link ${r}__button`,value:o})))))}})}();