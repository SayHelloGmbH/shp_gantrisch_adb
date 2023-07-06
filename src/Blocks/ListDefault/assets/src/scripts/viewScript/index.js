// jquery-match-height is loaded as one of the main scripts as it's used in several places

// START polyfill

if (window.NodeList && !NodeList.prototype.forEach) {
	NodeList.prototype.forEach = function (callback, thisArg) {
		var i;
		var len = this.length;

		thisArg = thisArg || window;

		for (i = 0; i < len; i++) {
			callback.call(thisArg, this[i], i, this);
		}
	};
}

if (window.Element && !Element.prototype.closest) {
	Element.prototype.closest = function (s) {
		var matches = (this.document || this.ownerDocument).querySelectorAll(s),
			i,
			el = this;
		do {
			i = matches.length;
			while (--i >= 0 && matches.item(i) !== el) {}
		} while (i < 0 && (el = el.parentElement));
		return el;
	};
}

// END polyfill

const classNameBase = 'wp-block-acf-shp-adb-list-default';

const makeVisible = (button, elements) => {
	const wrapper = button.parentNode;

	if (!!wrapper && !!wrapper.parentNode) {
		wrapper.parentNode.removeChild(wrapper);
	}

	elements.forEach((element) => {
		element.classList.remove('is--hidden');
	});
};

/**
 * Apply lazy-load button to each list block
 * and unhide the first n entries.
 */
const blocks = document.querySelectorAll(`.${classNameBase}`);

// const entry_class = `.${classNameBase}__entry`; // Own development class
const entry_class = `listing_entry`; // ADB standard class
const initial_count = Math.max(parseInt(shp_gantrisch_adb_block_list_default.initial_count), 1);

blocks.forEach((block) => {
	// First remove the total count and hide all entries
	block.querySelector('#offer_total').remove();
	block.querySelectorAll(`.${entry_class}:nth-child(n+${initial_count + 1})`).forEach((element) => {
		element.classList.add('is--hidden');
	});

	// Now unhide the first n entries and add the load more button
	const elements_on = block.querySelectorAll(`.${entry_class}:nth-child(-n+${initial_count})`);
	const elements_off = block.querySelectorAll(`.${entry_class}:nth-child(n+${initial_count + 1})`);

	elements_on.forEach((element) => {
		element.classList.remove('is--hidden');
	});

	const entry_before = block.querySelector(`.${entry_class}:nth-child(${initial_count + 2})`);

	if (!entry_before) {
		return;
	}

	const button_wrapper = document.createElement('div');

	button_wrapper.classList.add(`${classNameBase}__loadbutton`, `c-adb-list__loadbutton`);

	if (!!shp_gantrisch_adb_block_list_default) {
		const button = document.createElement('button');
		button.innerHTML = shp_gantrisch_adb_block_list_default.load_more_text;
		button.addEventListener('click', (event) => {
			event.preventDefault();
			event.currentTarget.blur();
			makeVisible(event.currentTarget, elements_off);
		});

		button_wrapper.appendChild(button);

		entry_before.parentNode.insertBefore(button_wrapper, entry_before);
	}
});

const addLinkButton = (element) => {
	const link = document.createElement('a');
	const wrapper = document.createElement('div');
	const button_text = element.closest('[data-button-text]').dataset.buttonText;
	const link_text = document.createTextNode(button_text);

	wrapper.classList.add(`${classNameBase}__entry-buttowrapper`, `c-adb-list__entry-buttonwrapper`);
	link.classList.add(`${classNameBase}__entry-button`, `c-adb-list__entry-button`);

	link.appendChild(link_text);
	link.setAttribute('href', element.getAttribute('href'));

	wrapper.appendChild(link);

	element.parentNode.insertBefore(wrapper, element.nextSibling);
};

const list = document.querySelectorAll('.wp-block-acf-shp-adb-list-default');

// list.querySelectorAll('.listing_entry .entry_link').forEach((element) => {
// 	addLinkButton(element);
// });
