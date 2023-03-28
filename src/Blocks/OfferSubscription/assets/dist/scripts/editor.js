(()=>{"use strict";const e=window.wp.blocks,t=window.wp.blockEditor,a=window.wp.components,n=window.wp.i18n,l=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"shp/gantrisch-adb-offer-subscription","title":"ADB Offer Subscription Text","category":"media","icon":"database","description":"The actual content will be loaded dynamically by passing an offer ID to the page on which this block has been placed.","keywords":["offer","angebot","anmeldung"],"textdomain":"shp_gantrisch_adb","attributes":{"align":{"type":"string"},"backgroundColor":{"type":"string"},"textColor":{"type":"string"},"button_text":{"type":"string"},"message":{"type":"string"},"title_sub_required":{"type":"string"},"title_sub_at":{"type":"string"}},"supports":{"align":["wide","full"],"html":false,"color":{"text":true,"background":true},"spacing":{"padding":true}},"editorScript":"file:./assets/dist/scripts/editor.js","render":"file:./render.php"}'),{name:s}=l,r=(0,e.getBlockDefaultClassName)(s);(0,e.registerBlockType)(s,{edit:e=>{let{attributes:l,setAttributes:s}=e;const i=(0,t.useBlockProps)({className:"c-message c-message--info"}),{button_text:o,message:c,title_sub_at:b,title_sub_required:u}=l;return React.createElement(React.Fragment,null,React.createElement(t.InspectorControls,null,React.createElement(a.PanelBody,{title:(0,n._x)("Settings"),initialOpen:!0},React.createElement(a.TextControl,{label:(0,n._x)("Title","TextControl label","shp_gantrisch_adb"),value:u,onChange:e=>s({title_sub_required:e})}),React.createElement(a.TextControl,{label:(0,n._x)("Text","TextControl label","shp_gantrisch_adb"),value:c,onChange:e=>s({message:e})}),React.createElement(a.TextControl,{label:(0,n._x)("Title - where to subscribe","TextControl label","shp_gantrisch_adb"),value:b,onChange:e=>s({title_sub_at:e})}),React.createElement(a.TextControl,{label:(0,n._x)("Button text","TextControl label","shp_gantrisch_adb"),value:o,onChange:e=>s({button_text:e})}))),React.createElement("div",i,React.createElement("div",{className:`${r}__content`},u&&React.createElement(t.RichText.Content,{tagName:"h2",className:`${r}__title`,value:u}),c&&React.createElement(t.RichText.Content,{tagName:"div",className:`${r}__message`,value:c}),b&&React.createElement(t.RichText.Content,{tagName:"h2",className:`${r}__title`,value:b}),React.createElement("div",{dangerouslySetInnerHTML:{__html:(0,n._x)("Single offer subscription information.","Editor message","shp_gantrisch_adb")}}),o&&React.createElement("div",{className:"wp-block-button"},React.createElement(t.RichText.Content,{tagName:"div",className:`wp-block-button__link ${r}__button`,value:o})))))}})})();