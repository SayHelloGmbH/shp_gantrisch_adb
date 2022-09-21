/**
 * Put together based on the example code at https://wordpress.org/support/topic/swiper-is-not-defined-elementor-3-5/
 * Wait until the elementor frontend init hook fires, then add
 * a new handler to the frontend/element_ready/global hook.
 *
 */

(function ($) {
	$(window).on('elementor/frontend/init', () => {
		const addHandler = () => {
			const config = {
					pagination: {
						el: '.swiper-pagination',
						clickable: true,
					},
				},
				elements = document.querySelectorAll(
					'.wp-block-shp-gantrisch-adb-offer-images .swiper-container'
				);

			if ('undefined' === typeof Swiper) {
				const AsyncSwiper = elementorFrontend.utils.swiper;

				new AsyncSwiper(swiperElement, config).then(() => {
					elements.forEach((element) => {
						new Swiper(element, config);
					});
				});
			} else {
				elements.forEach((element) => {
					new Swiper(element, config);
				});
			}
		};

		// https://developers.elementor.com/docs/hooks/js/#frontend-element-ready-global
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/global',
			addHandler
		);
	});
})(jQuery);
