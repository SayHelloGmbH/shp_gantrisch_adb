console.log('%cshp-accordion script loaded', 'color: #77adbf');

import './index.scss';

const blocks = document.querySelectorAll('[data-shp-accordion-entry]');

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
	blocks.forEach((block) => {
		block
			.querySelector('[data-shp-accordion-entry-trigger]')
			.setAttribute('aria-expanded', 'false');

		block
			.querySelector('[data-shp-accordion-entry-content]')
			.setAttribute('aria-hidden', 'true');
	});
};

blocks.forEach((block) => {
	block
		.querySelector('[data-shp-accordion-entry-trigger]')
		.addEventListener('click', toggle);

	const content = block.querySelector('[data-shp-accordion-entry-content]');

	if (content) {
		content.style.maxHeight = content.getBoundingClientRect().height;
	}
});

close_all();
