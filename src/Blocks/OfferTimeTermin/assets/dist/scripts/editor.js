(()=>{"use strict";const e=window.wp.blocks,t=window.wp.blockEditor,a=window.wp.components,n=window.wp.i18n,l=JSON.parse('{"name":"shp/gantrisch-adb-offer-time-termin"}'),{name:c}=l,s=(0,e.getBlockDefaultClassName)(c);(0,e.registerBlockType)(c,{edit:e=>{let{attributes:l,setAttributes:c}=e;const{title:r}=l,o=(0,t.useBlockProps)({className:"c-message"});return React.createElement(React.Fragment,null,React.createElement(t.InspectorControls,null,React.createElement(a.PanelBody,{title:(0,n._x)("Settings"),initialOpen:!0},React.createElement(a.TextControl,{label:(0,n._x)("Title","TextControl label","shp_gantrisch_adb"),value:r,onChange:e=>c({title:e})}))),React.createElement("div",o,r&&React.createElement(t.RichText.Content,{tagName:"h2",className:`${s}__title`,value:r}),React.createElement("div",{className:`${s}__content`,dangerouslySetInnerHTML:{__html:(0,n._x)("Placeholder for ADB time/date.","Editor message","shp_gantrisch_adb")}})))}})})();