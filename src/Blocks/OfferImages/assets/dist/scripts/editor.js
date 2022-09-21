!function(){"use strict";var e=window.wp.blocks,t=window.wp.blockEditor,a=window.wp.components,l=window.wp.i18n,s=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"shp/gantrisch-adb-offer-images","title":"ADB Offer Images","category":"media","icon":"format-image","description":"The actual content will be loaded dynamically by passing an offer ID to the page on which this block has been placed. Add each keyword on a new line in the original database.","keywords":["offer","angebot","keywords"],"textdomain":"shp_gantrisch_adb","attributes":{"align":{"type":"string"},"image_size":{"type":"string","default":"small"}},"supports":{"align":["wide","full"],"html":false},"editorScript":"file:./assets/dist/scripts/editor.js","viewScript":"file:./assets/dist/scripts/viewScript.js","style":["file:./assets/dist/styles/shared/index.min.css","shp-gantrisch-adb-offer-images-shared"]}'),i=[{label:(0,l._x)("Small","SelectControl option","sha"),value:"small"},{label:(0,l._x)("Medium","SelectControl option","sha"),value:"medium"},{label:(0,l._x)("Large","SelectControl option","sha"),value:"large"},{label:(0,l._x)("Full","SelectControl option","sha"),value:"full"}],n=React.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24",width:"24"},React.createElement("path",{d:"M12 11q-3.75 0-6.375-1.175T3 7q0-1.65 2.625-2.825Q8.25 3 12 3t6.375 1.175Q21 5.35 21 7q0 1.65-2.625 2.825Q15.75 11 12 11Zm0 5q-3.75 0-6.375-1.175T3 12V9.5q0 1.1 1.025 1.863 1.025.762 2.45 1.237 1.425.475 2.963.687 1.537.213 2.562.213t2.562-.213q1.538-.212 2.963-.687 1.425-.475 2.45-1.237Q21 10.6 21 9.5V12q0 1.65-2.625 2.825Q15.75 16 12 16Zm0 5q-3.75 0-6.375-1.175T3 17v-2.5q0 1.1 1.025 1.863 1.025.762 2.45 1.237 1.425.475 2.963.688 1.537.212 2.562.212t2.562-.212q1.538-.213 2.963-.688t2.45-1.237Q21 15.6 21 14.5V17q0 1.65-2.625 2.825Q15.75 21 12 21Z"}));const{name:o}=s,r=(0,e.getBlockDefaultClassName)(o);(0,e.registerBlockType)(o,{icon:n,edit:e=>{const s=(0,t.useBlockProps)(),{attributes:n,setAttributes:o}=e,{image_size:c}=n;let m="Undefined";return i&&c&&(m=i.find((e=>e.value===c)).label),React.createElement(React.Fragment,null,React.createElement(t.InspectorControls,null,React.createElement(a.PanelBody,{title:(0,l.__)("Settings")},React.createElement(a.SelectControl,{label:(0,l._x)("Select an image size","SelectControl label","shp_gantrisch_adb"),value:c,options:i,onChange:e=>{o({image_size:e})}}))),React.createElement("div",s,!!c&&React.createElement("figure",{className:`${r}__figure ${r}__figure--empty`},React.createElement("div",{className:`${r}__figcaption`,dangerouslySetInnerHTML:{__html:`${m} image size`}})),!c&&React.createElement("figure",{className:`${r}__figure ${r}__figure--empty`},React.createElement("div",{className:`${r}__figcaption`,dangerouslySetInnerHTML:{__html:(0,l._x)("No image size selected","Info text","shp_gantrisch_adb")}}))))}})}();