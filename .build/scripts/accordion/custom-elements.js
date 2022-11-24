if ('customElements' in window) {
	class AccordionListEntry extends HTMLDivElement {
		constructor() {
			super();
		}
	}

	class AccordionListEntries extends HTMLDivElement {
		constructor() {
			super();
		}
	}

	class AccordionListEntryTitle extends HTMLHeadingElement {
		constructor() {
			super();
		}
	}

	customElements.define('shp-accordion-list-entry', AccordionListEntry, {
		extends: 'div',
	});

	customElements.define('shp-accordion-list-entries', AccordionListEntries, {
		extends: 'div',
	});

	customElements.define(
		'shp-accordion-list-entry-title',
		AccordionListEntryTitle,
		{
			extends: 'h3',
		}
	);
}
