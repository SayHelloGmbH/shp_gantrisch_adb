!function(){"use strict";var e={n:function(t){var l=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(l,{a:l}),l},d:function(t,l){for(var n in l)e.o(l,n)&&!e.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:l[n]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}},t=window.wp.blocks,l=window.wp.blockEditor,n=window.wp.components,a=window.wp.i18n,r=window.wp.serverSideRender,i=e.n(r),o=JSON.parse('{"name":"shp/gantrisch-adb-accordion-route"}');const{name:c}=o;(0,t.registerBlockType)(c,{edit:e=>{let{attributes:t,setAttributes:r}=e;const{title_block:o,title_routelength:s,title_ascent:h,title_descent:_,title_unpaved:b,title_heightdifference:d,title_time:u,title_difficulty_technical:g,title_difficulty_condition:f,title_equipmentrental:x,title_safety:C,title_signals:p}=t,m=(0,l.useBlockProps)();return React.createElement(React.Fragment,null,React.createElement(l.InspectorControls,null,React.createElement(n.PanelBody,{title:(0,a._x)("Settings"),initialOpen:!0},React.createElement(n.TextControl,{label:(0,a._x)("Block title","TextControl label","shp_gantrisch_adb"),value:o,onChange:e=>r({title_block:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Routenlänge","TextControl label","shp_gantrisch_adb"),value:s,onChange:e=>r({title_routelength:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Höhenmeter Aufstieg","TextControl label","shp_gantrisch_adb"),value:h,onChange:e=>r({title_ascent:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Höhenmeter Abstieg","TextControl label","shp_gantrisch_adb"),value:_,onChange:e=>r({title_descent:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Anteil ungeteerter Wegstrecke","TextControl label","shp_gantrisch_adb"),value:b,onChange:e=>r({title_unpaved:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Höhendifferenz","TextControl label","shp_gantrisch_adb"),value:d,onChange:e=>r({title_heightdifference:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Zeitbedarf","TextControl label","shp_gantrisch_adb"),value:u,onChange:e=>r({title_time:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Schwierigkeitsgrad Technik","TextControl label","shp_gantrisch_adb"),value:g,onChange:e=>r({title_difficulty_technical:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Schwierigkeitsgrad Kondition","TextControl label","shp_gantrisch_adb"),value:f,onChange:e=>r({title_difficulty_condition:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Materialmiete","TextControl label","shp_gantrisch_adb"),value:x,onChange:e=>r({title_equipmentrental:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Sicherheitshinweise","TextControl label","shp_gantrisch_adb"),value:C,onChange:e=>r({title_safety:e})}),React.createElement(n.TextControl,{label:(0,a._x)("Überschrift Signalisation","TextControl label","shp_gantrisch_adb"),value:p,onChange:e=>r({title_signals:e})}))),React.createElement("div",m,React.createElement(i(),{block:c,attributes:t})))}})}();