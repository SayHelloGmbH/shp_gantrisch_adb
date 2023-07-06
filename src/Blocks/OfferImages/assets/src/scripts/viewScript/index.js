/**
 * Uses own instance of SwiperJS because the Elementor version
 * seems to have problems updating the pagination bullets
 */

import Swiper, { Pagination, Navigation } from 'swiper';

const elements = document.querySelectorAll('.wp-block-shp-gantrisch-adb-offer-images .swiper-container');

if (elements.length) {
	const config = {
		loop: true,
		autoHeight: false,
		simulateTouch: false,
		modules: [Navigation, Pagination],
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
	};

	elements.forEach((element) => {
		new Swiper(element, config);
	});
}
