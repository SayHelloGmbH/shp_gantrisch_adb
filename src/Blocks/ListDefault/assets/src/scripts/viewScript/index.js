// jquery-match-height is loaded as one of the main scripts as it's used in several places

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
	const filterform = block.querySelector('.filter form'),
		block_id = block.getAttribute('id');

	if (filterform && block_id) {
		const action = filterform.getAttribute('action');
		filterform.setAttribute('action', `${action}#${block_id}`);
	}

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

if (typeof jQuery.fn !== 'undefined' && typeof jQuery.fn.matchHeight !== 'undefined') {
	jQuery.fn.matchHeight._throttle = 350;
	jQuery(`.c-adb-list__entry h3`).matchHeight({});
}
