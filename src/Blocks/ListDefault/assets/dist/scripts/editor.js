!function(){"use strict";var e={n:function(t){var l=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(l,{a:l}),l},d:function(t,l){for(var n in l)e.o(l,n)&&!e.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:l[n]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}},t=window.wp.blocks,l=JSON.parse('{"u2":"shp/gantrisch-adb-offer-list"}'),n=window.wp.blockEditor,a=window.wp.components,o=window.wp.data,r=window.wp.i18n,i=window.wp.serverSideRender,c=e.n(i),s=[{label:(0,r._x)("Small","SelectControl option","sha"),value:"small"},{label:(0,r._x)("Medium","SelectControl option","sha"),value:"medium"},{label:(0,r._x)("Large","SelectControl option","sha"),value:"large"},{label:(0,r._x)("Full","SelectControl option","sha"),value:"full"}],u=(0,o.withSelect)(((e,t)=>{let l=[{id:"0",name:(0,r.__)("No selection")}],n=e("shp_gantrisch_adb/categories_for_select").getCategories();return n&&Object.values(n).map((e=>{const t={id:e.id,name:e.name,children:e.children};return l.push(t),e})),{...t,api_categories:l}}))((e=>{let{attributes:t,setAttributes:o,api_categories:i}=e;const{category:u,button_text:d,load_more_text:_,image_size:b,initial_count:m}=t;let p="Undefined";return s&&b&&s.find((e=>e.value===b)).label,React.createElement(React.Fragment,null,React.createElement(n.InspectorControls,null,React.createElement(a.PanelBody,{title:(0,r._x)("Settings"),initialOpen:!0},(!i||!i.length)&&React.createElement(a.Spinner,null),!!i.length&&React.createElement(a.TreeSelect,{label:(0,r._x)("Select a category","SelectControl label","shp_gantrisch_adb"),selectedId:u,onChange:e=>{o({category:e})},tree:i}),React.createElement(a.__experimentalNumberControl,{label:(0,r._x)("Number of entries in initial view","SelectControl label","shp_gantrisch_adb"),isShiftStepEnabled:!0,shiftStep:3,min:1,value:m,onChange:e=>o({initial_count:e})}),React.createElement(a.TextControl,{label:(0,r._x)("Button text","TextControl label","shp_gantrisch_adb"),value:d,onChange:e=>o({button_text:e})}),React.createElement(a.SelectControl,{label:(0,r._x)("Select an image size","SelectControl label","shp_gantrisch_adb"),value:b,options:s,onChange:e=>{o({image_size:e})}}),React.createElement(a.TextControl,{label:(0,r._x)("Load more button text","TextControl label","shp_gantrisch_adb"),value:_,onChange:e=>o({load_more_text:e})}))),React.createElement("div",(0,n.useBlockProps)(),React.createElement(a.Disabled,null,React.createElement(c(),{block:l.u2,attributes:{category:u}}))))})),d=React.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24",width:"24"},React.createElement("path",{d:"M12 11q-3.75 0-6.375-1.175T3 7q0-1.65 2.625-2.825Q8.25 3 12 3t6.375 1.175Q21 5.35 21 7q0 1.65-2.625 2.825Q15.75 11 12 11Zm0 5q-3.75 0-6.375-1.175T3 12V9.5q0 1.1 1.025 1.863 1.025.762 2.45 1.237 1.425.475 2.963.687 1.537.213 2.562.213t2.562-.213q1.538-.212 2.963-.687 1.425-.475 2.45-1.237Q21 10.6 21 9.5V12q0 1.65-2.625 2.825Q15.75 16 12 16Zm0 5q-3.75 0-6.375-1.175T3 17v-2.5q0 1.1 1.025 1.863 1.025.762 2.45 1.237 1.425.475 2.963.688 1.537.212 2.562.212t2.562-.212q1.538-.213 2.963-.688t2.45-1.237Q21 15.6 21 14.5V17q0 1.65-2.625 2.825Q15.75 21 12 21Z"}));(0,t.registerBlockType)(l.u2,{icon:d,edit:u})}();