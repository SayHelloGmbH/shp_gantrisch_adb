// jquery-match-height is loaded as one of the main scripts as it's used in several places

if (
	typeof jQuery.fn !== 'undefined' &&
	typeof jQuery.fn.matchHeight !== 'undefined'
) {
	jQuery.fn.matchHeight._throttle = 350;
	jQuery(`.wp-block-shp-adb-offer-same-category__entry-title`).matchHeight(
		{}
	);
	jQuery(`.wp-block-shp-adb-offer-same-category__entry-header`).matchHeight(
		{}
	);
}
