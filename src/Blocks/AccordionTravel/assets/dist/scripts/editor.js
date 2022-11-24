!function(){"use strict";var t={n:function(e){var l=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(l,{a:l}),l},d:function(e,l){for(var r in l)t.o(l,r)&&!t.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:l[r]})},o:function(t,e){return Object.prototype.hasOwnProperty.call(t,e)}},e=window.wp.blocks,l=window.wp.blockEditor,r=window.wp.components,a=window.wp.i18n,n=window.wp.serverSideRender,o=t.n(n),i=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"shp/gantrisch-adb-accordion-travel","title":"ADB Travel (Accordion)","description":"Startort, Zielort, ÖV-Haltestellen.","category":"media","icon":"database","keywords":["offer","angebot","accordion","travel"],"textdomain":"shp_gantrisch_adb","supports":{"align":["wide","full"],"multiple":false},"attributes":{"title_block":{"type":"string","default":"Details"},"title_start":{"type":"string","default":"Startort"},"title_start_stop":{"type":"string","default":"ÖV-Haltestelle"},"title_destination":{"type":"string","default":"Zielort"},"title_destination_stop":{"type":"string","default":"ÖV-Haltestelle"}},"editorScript":"file:./assets/dist/scripts/editor.js","render":"file:./render.php"}');const{name:s}=i;(0,e.registerBlockType)(s,{edit:t=>{let{attributes:e,setAttributes:n}=t;const{title_block:i,title_start:c,title_start_stop:d,title_goal:p,title_goal_stop:_}=e,b=(0,l.useBlockProps)();return React.createElement(React.Fragment,null,React.createElement(l.InspectorControls,null,React.createElement(r.PanelBody,{title:(0,a._x)("Settings"),initialOpen:!0},React.createElement(r.TextControl,{label:(0,a._x)("Block title","TextControl label","shp_gantrisch_adb"),value:i,onChange:t=>n({title_block:t})}),React.createElement(r.TextControl,{label:(0,a._x)("Überschrift Startort","TextControl label","shp_gantrisch_adb"),value:c,onChange:t=>n({title_start:t})}),React.createElement(r.TextControl,{label:(0,a._x)("Überschrift öV Startort","TextControl label","shp_gantrisch_adb"),value:d,onChange:t=>n({title_start_stop:t})}),React.createElement(r.TextControl,{label:(0,a._x)("Überschrift Zielort","TextControl label","shp_gantrisch_adb"),value:p,onChange:t=>n({title_goal:t})}),React.createElement(r.TextControl,{label:(0,a._x)("Überschrift öV Zielort","TextControl label","shp_gantrisch_adb"),value:_,onChange:t=>n({title_goal_stop:t})}))),React.createElement("div",b,React.createElement(o(),{block:s,attributes:e})))}})}();