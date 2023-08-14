/**
 * Uses own instance of SwiperJS because the Elementor version
 * seems to have problems updating the pagination bullets
 */

import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

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
		const swiper = new Swiper(element, config);
		swiper.on('slideChange', function () {
			swiper.pagination.render();
			swiper.pagination.update();
		});
	});
}
