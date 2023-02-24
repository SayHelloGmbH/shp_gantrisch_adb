<!--
Available Tags:
OFFER_TITLE
OFFER_SHORT_INFO
OFFER_PRINT_LINK
OFFER_BACK_LINK
OFFER_CATEGORIES
OFFER_IMAGES
OFFER_ABSTRACT
OFFER_DESCRIPTION
OFFER_ADDITIONAL_INFO
OFFER_DATES
OFFER_DOCUMENTS
OFFER_EVENT_DETAIL -> contains:		OFFER_EVENT_LOCATION
									OFFER_EVENT_LOCATION_SHORT
									OFFER_EVENT_LOCATION_DETAILS
									OFFER_EVENT_TRANSPORT
									OFFER_EVENT_DATE_DETAILS
									OFFER_EVENT_PRICE
OFFER_PRODUCT_DETAIL -> contains: 	OFFER_PRODUCT_OPENING_HOURS
									OFFER_PRODUCT_PUBLIC_TRANSPORT
									OFFER_PRODUCT_PRICE
									OFFER_PRODUCT_INFRASTRUCTURE
									OFFER_ONLINE_SHOP_CHECKOUT_BUTTON
OFFER_BOOKING_DETAIL -> contains: 	OFFER_BOOKING_GROUPS
									OFFER_BOOKING_TRANSPORT
									OFFER_BOOKING_BENEFITS
									OFFER_BOOKING_REQUIREMENTS
									OFFER_BOOKING_PRICE
									OFFER_BOOKING_ACCOMMODATIONS
OFFER_ACTIVITY_DETAIL -> contains: 	OFFER_ACTIVITY_ROUTE
									OFFER_ACTIVITY_ARRIVAL
									OFFER_ACTIVITY_PRICE
									OFFER_ACTIVITY_CATERING
									OFFER_ACTIVITY_MATERIAL_RENT
									OFFER_ACTIVITY_SAFETY_INSTRUCTIONS
									OFFER_ACTIVITY_SIGNALIZATION
									OFFER_ACTIVITY_DATES
									OFFER_ACTIVITY_INFRASTRUCTURE
OFFER_PROJECT_DETAIL -> contains: 	OFFER_PROJECT_DURATION
									OFFER_PROJECT_STATUS

OFFER_LINKS
OFFER_ACCESSIBILITIES
OFFER_TARGET_GROUPS
OFFER_SUPPLIERS
OFFER_INSTITUTION
OFFER_CONTACT
OFFER_SUBSCRIPTION
OFFER_POI_LIST
OFFER_ROUTE_LIST
OFFER_MAP
OFFER_KEYWORDS

Language variables can be printed: __LANG[name]__

Conditions available: OFFER_EVENT, OFFER_PRODUCT, OFFER_BOOKING, OFFER_ACTIVITY, OFFER_PROJECT, OFFER_RESEARCH
Check condition: [OFFER_EVENT@start] This text is rendered based on the condition [OFFER_PRODUCT@stop]

Check if placeholder is set [ISSET(OFFER_PRODUCT)@start] [ISSET(OFFER_PRODUCT)@stop]
-->
<div class="detail">
	<div class="links_wrap">
		__OFFER_PRINT_LINK__
		__OFFER_BACK_LINK__
		<div class="cf"></div>
	</div>
	<div class="heading">
		<h1>__OFFER_TITLE__</h1>
		<div class="categories">__OFFER_CATEGORIES__</div>
		<div class="introduction">__OFFER_SHORT_INFO__</div>
	</div>
	<div class="cf"></div>
	<div class="mix_container">
		<div class="tags_content">
			<div class="content_wrapper content_1" style="display: block;">
				<div class="content_left">

					[ISSET(OFFER_IMAGES)@start]
						<div class="portlet">
							__OFFER_IMAGES__
						</div>
					[ISSET(OFFER_IMAGES)@stop]

					[OFFER_PRODUCT@start]
						[ISSET(OFFER_PRODUCT_DETAIL)@start]
						<div class="portlet">
							__OFFER_PRODUCT_PRICE__
						</div>
						[ISSET(OFFER_PRODUCT_DETAIL)@stop]
					[OFFER_PRODUCT@stop]

					[ISSET(OFFER_DATES)@start]
						<div class="portlet">
							__OFFER_DATES__
						</div>
					[ISSET(OFFER_DATES)@stop]

					[OFFER_EVENT@start]
						<div class="portlet">
							__OFFER_EVENT_LOCATION__
						</div>
					[OFFER_EVENT@stop]

					[ISSET(OFFER_SUPPLIERS)@start]
						<div class="portlet">
							__OFFER_SUPPLIERS__
						</div>
					[ISSET(OFFER_SUPPLIERS)@stop]

					[ISSET(OFFER_INSTITUTION)@start]
						<div class="portlet">
							__OFFER_INSTITUTION__
						</div>
					[ISSET(OFFER_INSTITUTION)@stop]

					[ISSET(OFFER_CONTACT)@start]
						<div class="portlet">
							__OFFER_CONTACT__
						</div>
					[ISSET(OFFER_CONTACT)@stop]

					[ISSET(OFFER_DOCUMENTS)@start]
						<div class="portlet">
							__OFFER_DOCUMENTS__
						</div>
					[ISSET(OFFER_DOCUMENTS)@stop]

					[ISSET(OFFER_ACCESSIBILITIES)@start]
						<div class="portlet">
							__OFFER_ACCESSIBILITIES__
						</div>
					[ISSET(OFFER_ACCESSIBILITIES)@stop]

					[ISSET(OFFER_LINKS)@start]
						<div class="portlet">
							__OFFER_LINKS__
						</div>
					[ISSET(OFFER_LINKS)@stop]

				</div>
				<div class="content_right">
					__OFFER_DESCRIPTION__

					<div class="accordion_wrap">
						[OFFER_EVENT@start]
							[ISSET(OFFER_EVENT_DETAIL)@start]
							<div class="accordion_element">
								<h2 class="accordion_title">
								__LANG[offer_detail]__
								<span class="icon" aria-hidden="true"><span></span><span></span></span>
								</h2>
								<div class="accordion_content">
									__OFFER_EVENT_DETAIL__
								</div>
							</div>
							[ISSET(OFFER_EVENT_DETAIL)@stop]
						[OFFER_EVENT@stop]

						[OFFER_PRODUCT@start]
							[ISSET(OFFER_PRODUCT_DETAIL)@start]
							<div class="accordion_element">
								<h2 class="accordion_title">
								__LANG[offer_detail]__
								<span class="icon" aria-hidden="true"><span></span><span></span></span>
								</h2>
								<div class="accordion_content">
									__OFFER_PRODUCT_DETAIL__
								</div>
							</div>
							[ISSET(OFFER_PRODUCT_DETAIL)@stop]
						[OFFER_PRODUCT@stop]

						[OFFER_BOOKING@start]
							[ISSET(OFFER_BOOKING_DETAIL)@start]
							<div class="accordion_element">
								<h2 class="accordion_title">
								__LANG[offer_detail]__
								<span class="icon" aria-hidden="true"><span></span><span></span></span>
								</h2>
								<div class="accordion_content">
									__OFFER_BOOKING_DETAIL__
								</div>
							</div>
							[ISSET(OFFER_BOOKING_DETAIL)@stop]
						[OFFER_BOOKING@stop]

						[OFFER_ACTIVITY@start]
							[ISSET(OFFER_ACTIVITY_ROUTE)@start]
							<div class="accordion_element">
								<h2 class="accordion_title">
									__LANG[offer_route_info]__
									<span class="icon" aria-hidden="true"><span></span><span></span></span>
								</h2>
								<div class="accordion_content">
									__OFFER_ACTIVITY_ROUTE__
									<div>__LANG[offer_elevation_profile]__</div>
								</div>
							</div>
							[ISSET(OFFER_ACTIVITY_ROUTE)@stop]
							[ISSET(OFFER_ACTIVITY_DETAIL)@start]
							<div class="accordion_element">
								<h2 class="accordion_title">
									__LANG[offer_details]__
									<span class="icon" aria-hidden="true"><span></span><span></span></span>
								</h2>
								<div class="accordion_content">
									__OFFER_ACTIVITY_DETAIL__
								</div>
							</div>
							[ISSET(OFFER_ACTIVITY_DETAIL)@stop]
							[ISSET(OFFER_ACTIVITY_ARRIVAL)@start]
							<div class="accordion_element">
								<h2 class="accordion_title">
									__LANG[offer_arrival]__
									<span class="icon" aria-hidden="true"><span></span><span></span></span>
								</h2>
								<div class="accordion_content">
									__OFFER_ACTIVITY_ARRIVAL__
								</div>
							</div>
							[ISSET(OFFER_ACTIVITY_ARRIVAL)@stop]
						[OFFER_ACTIVITY@stop]

						[OFFER_RESEARCH@start]
							__OFFER_RESEARCH_DETAIL__
						[OFFER_RESEARCH@stop]

						[ISSET(OFFER_SUBSCRIPTION)@start]
						<div class="accordion_element">
							<h2 class="accordion_title">
							__LANG[offer_subscription]__
							<span class="icon" aria-hidden="true"><span></span><span></span></span>
							</h2>
							<div class="accordion_content">
								__OFFER_SUBSCRIPTION__
							</div>
						</div>
						[ISSET(OFFER_SUBSCRIPTION)@stop]

						[ISSET(OFFER_TARGET_GROUPS)@start]
						<div class="accordion_element">
							<h2 class="accordion_title">
								__LANG[offer_target_group]__
								<span class="icon" aria-hidden="true"><span></span><span></span></span>
							</h2>
							<div class="accordion_content">
								__OFFER_TARGET_GROUPS__
							</div>
						</div>
						[ISSET(OFFER_TARGET_GROUPS)@stop]

						[ISSET(OFFER_POI_LIST)@start]
						<div class="accordion_element">
							<h2 class="accordion_title">
								[OFFER_ACTIVITY@start]
									__LANG[offer_poi_activity]__
								[OFFER_ACTIVITY@stop]

								[OFFER_PROJECT@start]
									__LANG[offer_project_links]__
								[OFFER_PROJECT@stop]
								<span class="icon" aria-hidden="true"><span></span><span></span></span>
							</h2>
							<div class="accordion_content">
								__OFFER_POI_LIST__
							</div>
						</div>
						[ISSET(OFFER_POI_LIST)@stop]

						[ISSET(OFFER_ROUTE_LIST)@start]
						<div class="accordion_element">
							<h2 class="accordion_title">
								__LANG[offer_route_links]__
								<span class="icon" aria-hidden="true"><span></span><span></span></span>
							</h2>
							<div class="accordion_content">
								__OFFER_ROUTE_LIST__
							</div>
						</div>
						[ISSET(OFFER_ROUTE_LIST)@stop]

						[OFFER_PROJECT@start]
						<div class="accordion_element">
							<h2 class="accordion_title">
								__LANG[offer_detail]__
								<span class="icon" aria-hidden="true"><span></span><span></span></span>
							</h2>
							<div class="accordion_content">
								__OFFER_PROJECT_DETAIL__
							</div>
						</div>
						[OFFER_PROJECT@stop]
					</div>
				</div>
			</div>
			<div class="content_wrapper content_wrapper_map content_2" style="display: block;">
				<h2 class="map_wrap_title">__LANG[offer_map]__</h2>
				__OFFER_MAP__
			</div>
			<div class="cf"></div>
		</div>
	</div>
</div>