import 'jquery-match-height';
import './_polyfill';

const conditionalLoadScript = (filename, condition) => {
	if (!!condition) {
		const min = shp_gantrisch_adb.debug ? '' : '.min';
		let script = document.createElement('script');
		script.setAttribute('src', `${shp_gantrisch_adb.url}/assets/scripts/${filename}${min}.js?version=${shp_gantrisch_adb.version}`);
		document.head.appendChild(script);
	}
};

conditionalLoadScript('accordion', !!document.querySelectorAll('[data-shp-accordion-entry]').length);
conditionalLoadScript('fancybox', !!document.querySelectorAll('[data-fancybox]').length);
