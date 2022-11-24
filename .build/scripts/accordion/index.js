console.log('%cshp-accordion script loaded', 'color: #77adbf');

import './custom-elements';
import './index.scss';

const entries = document.querySelectorAll('[data-shp-accordion-entry]');

const toggle = (event) => {
	event.preventDefault();

	const button = event.currentTarget,
		content = event.currentTarget
			.closest('[data-shp-accordion-entry]')
			.querySelector('[data-shp-accordion-entry-content]');

	if (button.getAttribute('aria-expanded') === 'true') {
		button.setAttribute('aria-expanded', 'false');
		content.setAttribute('aria-hidden', 'true');
	} else {
		button.setAttribute('aria-expanded', 'true');
		content.setAttribute('aria-hidden', 'false');
	}
};

const close_all = () => {
	entries.forEach((entry) => {
		entry
			.querySelector('[data-shp-accordion-entry-trigger]')
			.setAttribute('aria-expanded', 'false');
		entry
			.querySelector('[data-shp-accordion-entry-content]')
			.setAttribute('aria-hidden', 'true');
	});
};

entries.forEach((entry) => {
	entry
		.querySelector('[data-shp-accordion-entry-trigger]')
		.addEventListener('click', toggle);

	const content = entry.querySelector('[data-shp-accordion-entry-content]');

	content.style.maxHeight = content.getBoundingClientRect().height;
});

close_all();

if ('customElements' in window) {
	class ListEntry extends HTMLDivElement {
		constructor() {
			super();
		}
	}

	class ListEntryTitle extends HTMLHeadingElement {
		constructor() {
			super();
		}
	}

	customElements.define('shp-accordion-list-entry', ListEntry, {
		extends: 'div',
	});

	customElements.define('shp-accordion-list-entry-title', ListEntryTitle, {
		extends: 'h3',
	});
}
