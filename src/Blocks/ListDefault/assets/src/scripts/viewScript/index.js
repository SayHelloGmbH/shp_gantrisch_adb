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

const initial_count = Math.max(
	parseInt(shp_gantrisch_adb_block_list_default.initial_count),
	1
);

blocks.forEach((block) => {
	const elements_on = block.querySelectorAll(
		`.${classNameBase}__entry:nth-child(-n+${initial_count})`
	);

	const elements_off = block.querySelectorAll(
		`.${classNameBase}__entry:nth-child(n+${initial_count + 1})`
	);

	elements_on.forEach((element) => {
		element.classList.remove('is--hidden');
	});

	const entry_before = block.querySelector(
		`.${classNameBase}__entry:nth-child(13)`
	);

	if (!entry_before) {
		return;
	}

	const button_wrapper = document.createElement('div');

	button_wrapper.classList.add(
		`${classNameBase}__loadbutton`,
		`c-adb-list__loadbutton`
	);

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

function throttle(fn, wait) {
	var time = Date.now();
	return function () {
		if (time + wait - Date.now() < 0) {
			fn();
			time = Date.now();
		}
	};
}

const setImageSizes = () => {
	const images = document.querySelectorAll(`.${classNameBase}__entry-image`);

	if (images.length) {
		const image_width = getComputedStyle(images[0]).width;
		images.forEach((image) => {
			image.setAttribute('sizes', image_width);
		});
	}
};

setImageSizes();
window.addEventListener('load', setImageSizes);
window.addEventListener('resize', throttle(setImageSizes, 350));

jQuery.fn.matchHeight._throttle = 350;
jQuery(`.${classNameBase}__entry-header`).matchHeight({});
