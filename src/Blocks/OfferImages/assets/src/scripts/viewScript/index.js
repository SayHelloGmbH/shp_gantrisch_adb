/**
 * Uses own instance of SwiperJS because the Elementor version
 * seems to have problems updating the pagination bullets
 */

import Swiper, { Pagination } from 'swiper';
Swiper.use([Pagination]);

const elements = document.querySelectorAll(
	'.wp-block-shp-gantrisch-adb-offer-images .swiper-container'
);

if (elements.length) {
	const config = {
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
	};

	elements.forEach((element) => {
		new Swiper(element, config);
	});
}
