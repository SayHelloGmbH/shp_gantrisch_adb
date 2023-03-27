import './index.scss';

const buttons = document.querySelectorAll('[data-shp_gantrisch_adb-doupdate]'),
	rest_url = shp_gantrisch_adb.url,
	api_url = `${rest_url}shp_gantrisch_adb/update-from-api`;

const showMessage = (button, message, status) => {
	const messageContainer = button.parentNode.querySelector(
		'[data-shp_gantrisch_adb-api-response]'
	);

	messageContainer.textContent = message;
	messageContainer.classList.add(`is--${status}`);
	messageContainer.classList.remove('is--hidden');

	setTimeout(() => resetMessage(button), 5000);
};

const resetMessage = (button) => {
	const messageContainer = button.parentNode.querySelector(
		'[data-shp_gantrisch_adb-api-response]'
	);
	messageContainer.classList.add('is--hidden');
	messageContainer.classList.remove('is--success', 'is--error');
	messageContainer.textContent = '';
};

async function clickHandler(e) {
	e.preventDefault();
	const button = e.currentTarget;
	button.setAttribute('disabled', 'disabled');

	const text_was = button.textContent;

	button.textContent = button.dataset.textWait;

	resetMessage(button);

	const response = await fetch(api_url, {
		headers: {
			'Content-Type': 'application/json',
			'X-WP-Nonce': shp_gantrisch_adb.nonce,
		},
	});

	if (!response.ok) {
		switch (response.status) {
			case 404:
				button.removeAttribute('disabled');
				button.textContent = text_was;
				showMessage(
					button,
					'Die Schnittstelle konnte nicht erreicht werden. (404)',
					'error'
				);
				throw new Error(
					'Die Antwort von der Schnittstelle konnte nicht verarbeitet werden. (404)'
				);
			case 500:
				button.removeAttribute('disabled');
				button.textContent = text_was;
				showMessage(
					button,
					'Ein unerwarteter Fehler ist aufgetreten. (500)',
					'error'
				);
				throw new Error(
					'Ein unerwarteter Fehler ist aufgetreten. (500)'
				);
			default:
				button.removeAttribute('disabled');
				button.textContent = text_was;
				showMessage(
					button,
					`${response.message} (${response.status})`,
					'error'
				);
				throw new Error(`${response.message} (${response.status})`);
		}
	}

	button.removeAttribute('disabled');
	button.textContent = text_was;
	showMessage(
		button,
		`Die ADB-Daten wurden erfolgreich aktualisiert`,
		'success'
	);
}

if (buttons.length) {
	buttons.forEach((button) => {
		button.addEventListener('click', clickHandler);
		button.removeAttribute('disabled');
	});
}
