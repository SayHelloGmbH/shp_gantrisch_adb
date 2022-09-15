<?php
/*
|---------------------------------------------------------------
| parks.swiss API
| Netzwerk Schweizer Pärke
|---------------------------------------------------------------
|
| Import XML data into database
|
*/


class ParksImport {


	/**
	 * API
	 */
	public $api;


	/**
	 * XML object containing all data
	 */
	private $xml;


	/**
	 * Necessary Key-Value mappings for XML data
	 */
	private $mappings;


	/**
	 * Constructor
	 */
	function __construct($api) {

		// Api instance
		$this->api = $api;

		// Setup mappings necessary for XML import
		$this->mappings = array(
			'langs' => array(
				'de' => 'de',
				'fr' => 'fr',
				'it' => 'it',
				'en' => 'en'
			),
			'levels' => array(
				'' 			=> 0,
				'easy' 		=> 1,
				'medium' 	=> 2,
				'hard' 		=> 3
			)
		);
	}


	/**
	 * Method for importing xml map layers into database
	 *
	 * @param url Location of XML file
	 * @return boolean
	 */
	public function import_map_layers($url) {

		// Load xml
		$this->xml = $this->api->load_external_xml($url);

		// Check XML
		if ($this->xml !== FALSE) {

			// Remove all layers
			$this->api->db->delete('map_layer');

			// Import custom layers
			if (count($this->xml->MapLayers) > 0) {

				// Remove first all elements
				foreach ($this->xml->MapLayers->MapLayer as $layer) {
					$map_fields = array();
					$map_i18n_fields = array();
					$content = '';
					$map_fields['map_layer_id'] = intval($layer->attributes()->identifier);
					$map_fields['url'] = (string) trim($layer->URL);
					$map_fields['languages'] = (string) $layer->URL->attributes()->language;
					$map_fields['layer_position'] = (int) $layer->URL->attributes()->position;
					$map_fields['visible_by_default'] = (int) $layer->URL->attributes()->visibility;

					if (isset($layer->URL->Popup)) {
						$map_fields['popup_title'] = (string) $layer->URL->Popup->Title;

						if (isset($layer->URL->Popup->Logo)) {
							$map_fields['popup_logo'] = (string) $layer->URL->Popup->Logo;
							$map_fields['popup_logo_width'] = (int) $layer->URL->Popup->Logo->attributes()->width;
							$map_fields['popup_logo_height'] = (int) $layer->URL->Popup->Logo->attributes()->height;
						}
					}

					// Insert
					if (!$this->api->db->insert('map_layer', $map_fields)) {
						$this->api->logger->error("MySQL Error: ".$this->api->db->get_last_error());
						continue;
					}
					// If insert success
					else {

						$map_layer_id = $this->_get_last_id('map_layer_id', 'map_layer');

						if (isset($layer->URL->Popup->Content) && !empty($map_layer_id)) {
							foreach ($layer->URL->Popup->Content as $content) {
								$map_i18n_fields['map_layer_id'] = $map_layer_id;
								$map_i18n_fields['popup_content'] = (string) $content;
								$map_i18n_fields['language'] = (string) $content->attributes()->lang;

								if (!$this->api->db->insert('map_layer_i18n', $map_i18n_fields)) {
									$this->api->logger->error("MySQL Error: ".$this->api->db->get_last_error());
									continue;
								}
							}
						}
						$this->api->logger->info("Map layer i18n import finished successfully.");
					}
				}
				$this->api->logger->info("Map layer import finished successfully.");
			}
		}

		// Log error
		else {
			$this->api->logger->info("XML is not valid: ".$url);
		}
		
	}


	/**
	 * Method for importing xml offers into database
	 *
	 * @param url Location of XML file
	 * @param force Forces import of all offers (including those who haven't changed)
	 * @return boolean
	 */
	public function import($url, $force = FALSE) {

		if (empty($url)) {
			$this->api->logger->error("No URL for XML file specified");
			die("No URL for XML file specified");
		}

		// Sync target groups
		$this->sync_target_groups();

		// Sync categories
		$this->sync_categories();

		// Add param to url with last import timestamp
		if ($force !== TRUE) {
			$api_info = mysqli_fetch_assoc($this->api->db->get('api'));
			$last_import = $api_info['last_import'];
			if (!empty($last_import)) {
				$url .= '?since='.$last_import;
			}
			else {
				$force = TRUE;
				$this->api->logger->info("Enabling FORCE mode because timestamp for last import is empty");
			}
		}
		else {
			$this->api->logger->info("FORCE mode is enabled");
		}

		// Load external data
		$this->xml = $this->api->load_external_xml($url);

		// Check xml
		if ($this->xml !== FALSE) {

			// Log start
			$this->api->logger->info("Starting XML import from ".$url);

			// Init
			$ctr_imported = 0;
			$ctr_deleted = 0;
			$offers_checklist = array();

			if (!empty($this->xml)) {

				// Import every offer
				foreach ($this->xml->Offer as $offer) {

					// Init offer
					$offer_id = intval($offer->attributes()->identifier);
					$deleted_at = $offer->attributes()->deletedAt;
					$status = $offer->attributes()->status;

					// Check if offer should be deleted
					if (!empty($deleted_at) || ($status != OFFER_STATUS_ACTIVE)) {
						if ($force == FALSE) {
							
							// Delete offer from db and continue
							$this->api->db->delete('offer', array('offer_id' => $offer_id));
							$this->api->logger->info("\tDeleted offer with ID ".$offer_id);
							$ctr_deleted++;

						}

						continue;
					}

					// Set offer data
					$fields = array();
					$fields['park_id'] = (int)$offer->ParkId;
					$fields['park'] = (string)$offer->Park;
					$fields['institution'] = $this->_address($offer->Institution);
					$fields['institution_location'] = (string)$offer->Institution->Locality;
					$fields['institution_is_park_partner'] = (int)$offer->Institution->ParkPartner;
					$fields['contact'] = $this->_address($offer->Contact);
					$fields['contact_is_park_partner'] = (int)$offer->Contact->ParkPartner;
					$fields['is_hint'] = (int)$offer->IsHint;
					$fields['barrier_free'] = (int)$offer->BarrierFree;
					$fields['learning_opportunity'] = (int)$offer->LearningOpportunity;
					$fields['child_friendly'] = (int)$offer->ChildFriendly;
					$fields['latitude'] = (float)$offer->Latitude;
					$fields['longitude'] = (float)$offer->Longitude;
					$fields['keywords'] = $offer->Keywords;
					$fields['created_at'] = $this->_datetime($offer->attributes()->createdAt);
					$fields['modified_at'] = $this->_datetime($offer->attributes()->modifiedAt);

					// Error handling
					if (!$this->_insert_or_update('offer', $fields, array('offer_id' => $offer_id))) {
						$this->api->logger->error("MySQL Error: ".$this->api->db->get_last_error());
						continue;
					}

					// Categories
					$this->api->db->delete('category_link', array('offer_id' => $offer_id));
					foreach ($offer->Categories->Category as $category) {
						$category_id = $category->attributes()->identifier;
						$this->api->db->insert('category_link', array('offer_id' => $offer_id, 'category_id' => $category_id));
					}

					// Offer i18n
					$i18n = array();
					foreach ($this->mappings['langs'] as $lang) {
						$i18n[$lang] = array();
					}

					// Clean i18n data
					$this->api->db->delete('offer_i18n', array('offer_id' => $offer_id));

					// Define i18n fields
					$i18n_fields = array(
						'Title' => 'title',
						'Abstract' => 'abstract',
						'MediumDescription' => 'description_medium',
						'LongDescription' => 'description_long',
						'AdditionalInfos' => 'additional_informations',
						'Details' => 'details',
						'PriceInfos' => 'price',
						'LocationDetails' => 'location_details',
						'OpeningHours' => 'opening_hours',
						'OtherInfrastructure' => 'other_infrastructure',
						'Benefits' => 'benefits',
						'Requirements' => 'requirements',
						'CateringInfos' => 'catering_informations',
						'MaterialRent' => 'material_rent',
						'SafetyInstructions' => 'safety_instructions',
						'Signalization' => 'signalization',
					);

					// Setup i18n values
					$available_languages = array();
					foreach	($i18n_fields as $xml_node_name => $db_column_name) {
						
						// Node is set in xml
						if (!empty($offer->{$xml_node_name})) {
							foreach ($offer->{$xml_node_name} as $node) {
								$lang = (string)$node->attributes()->language;
								$available_languages[$lang] = NULL;
								$i18n[$lang][$db_column_name] = (string)$node;
							}
						}

						// Node is not set > clean this value from db
						else {
							foreach (array_keys($available_languages) as $lang) {
								$i18n[$lang][$db_column_name] = '';
							}
						}

					}

					// Route condition
					if (!empty($offer->RouteConditions->RouteCondition)) {
						foreach ($offer->RouteConditions->RouteCondition as $node) {
							$lang = (string)$node->attributes()->language;
							$i18n[$lang]['route_condition'] = (string)$node;
						}
					}

					// Route condition details
					if (!empty($offer->RouteConditions->RouteConditionDetails)) {
						foreach ($offer->RouteConditions->RouteConditionDetails as $node) {
							$lang = (string)$node->attributes()->language;
							$i18n[$lang]['route_condition_details'] = (string)$node;
						}
					}
					
					// Offer route url
					if (isset($offer->Route->URL)) {
						foreach ($offer->Route->URL as $url) {
							$lang = (string)$url->attributes()->language;
							$i18n[$lang]['route_url'] = (string)$url;
						}
					}

					// Insert internal informations
					$i18n_fields_intern = array(
						'Costs' => 'costs',
						'Funding' => 'funding',
						'Partner' => 'partner',
						'Remarks' => 'remarks',
					);
					foreach ($i18n_fields_intern as $xml_node_name => $db_column_name) {
						
						// Node is set in xml
						if (!empty($offer->InternalInformations->{$xml_node_name})) {
							foreach ($offer->InternalInformations->{$xml_node_name} as $node) {
								$lang = (string)$node->attributes()->language;
								$available_languages[$lang] = NULL;
								$i18n[$lang][$db_column_name] = (string)$node;
							}
						}

						// Node is not set > clean this value from db
						else {
							foreach (array_keys($available_languages) as $lang) {
								$i18n[$lang][$db_column_name] = '';
							}
						}

					}

					// Insert or update i18n data for offer
					foreach ($i18n as $language => $data) {
						if (!empty($data)) {
							$this->_insert_or_update('offer_i18n', $data, array('offer_id' => $offer_id, 'language' => $language));
						}
					}

					// Dates
					$this->api->db->delete('offer_date', array('offer_id' => $offer_id));
					if (!empty($offer->Dates)) {
						foreach ($offer->Dates->Date as $date) {
							$date_from = $date->DateFrom.($date->TimeFrom ? "T".$date->TimeFrom : "");
							$date_to = NULL;
							if ($date->DateTo) {
								$date_to = $date->DateTo.($date->TimeTo ? "T".$date->TimeTo : "");
							}

							$fields = array();
							$fields['offer_id'] = $offer_id;
							$fields['date_from'] = empty($date_from) ? NULL : $this->_datetime($date_from);
							$fields['date_to'] = empty($date_to) ? NULL : $this->_datetime($date_to);

							$this->api->db->insert('offer_date', $fields);
						}
					}					

					// TargetGroups
					$this->api->db->delete('target_group_link', array('offer_id' => $offer_id));
					if ($offer->TargetGroups) {
						foreach ($offer->TargetGroups->TargetGroup as $target_group) {
							$target_group_id = $target_group->attributes()->identifier;
							$this->api->db->insert('target_group_link', array('offer_id' => $offer_id, 'target_group_id' => $target_group_id));
						}
					}

					// Images
					$this->api->db->delete('image', array('offer_id' => $offer_id));
					if ($offer->Images) {
						foreach ($offer->Images->Image as $image) {
							$fields = array();

							$fields['offer_id'] = $offer_id;
							$fields['small'] = (string)$image->Small;
							$fields['medium'] = (string)$image->Medium;
							$fields['large'] = (string)$image->Large;
							$fields['original'] = (string)$image->Original;
							$fields['copyright'] = (string)$image->Copyright;

							$this->api->db->insert('image', $fields);
						}
					}

					// Documents
					$this->api->db->delete('document', array('offer_id' => $offer_id));
					if ($offer->Documents) {
						foreach ($offer->Documents->Document as $document) {
							$fields = array();

							$fields['offer_id'] = $offer_id;
							$fields['language'] = (string)$document->attributes()->language;
							$fields['title'] = (string)$document->Title;
							$fields['url'] = (string)$document->URL;

							$this->api->db->insert('document', $fields);
						}
					}

					// Documents intern
					$this->api->db->delete('document_intern', array('offer_id' => $offer_id));
					if ($offer->InternalInformations) {
						foreach ($offer->InternalInformations->DocumentsIntern->Document as $document) {
							$fields = array();

							$fields['offer_id'] = $offer_id;
							$fields['language'] = (string)$document->attributes()->language;
							$fields['title'] = (string)$document->Title;
							$fields['url'] = (string)$document->URL;

							$this->api->db->insert('document_intern', $fields);
						}
					}

					// Hyperlinks
					$this->api->db->delete('hyperlink', array('offer_id' => $offer_id));
					if ($offer->Hyperlinks) {
						foreach ($offer->Hyperlinks->Hyperlink as $hyperlink) {
							$fields = array();

							$fields['offer_id'] = $offer_id;
							$fields['language'] = (string)$hyperlink->attributes()->language;
							$fields['title'] = (string)$hyperlink->Title;
							$fields['url'] = (string)$hyperlink->URL;

							$this->api->db->insert('hyperlink', $fields);
						}
					}

					// Hyperlinks intern
					$this->api->db->delete('hyperlink_intern', array('offer_id' => $offer_id));
					if ($offer->InternalInformations->HyperlinksIntern) {
						foreach ($offer->InternalInformations->HyperlinksIntern->Hyperlink as $hyperlink) {
							$fields = array();

							$fields['offer_id'] = $offer_id;
							$fields['language'] = (string)$hyperlink->attributes()->language;
							$fields['title'] = (string)$hyperlink->Title;
							$fields['url'] = (string)$hyperlink->URL;

							$this->api->db->insert('hyperlink_intern', $fields);
						}
					}

					// Accessibilities
					$this->api->db->delete('accessibility', array('offer_id' => $offer_id));
					if ($offer->Accessibilities) {
						foreach ($offer->Accessibilities->Accessibility as $accessibility) {

							// Get pictogram id
							$pictogram_id = (string)$accessibility->attributes()->identifier;

							// Insert or update pictogram
							$pictogram = array();
							$pictogram['accessibility_pictogram_id'] = $pictogram_id;
							$pictogram['pictogram_source'] = (string)$accessibility->PictogramSource;
							foreach ($accessibility->Name as $name) {
								$language = (string)$name->attributes()->language;
								$pictogram['name_'.$language] = (string)$name;
							}
							$pictogram['detail_link'] = (string)$accessibility->PictogramDetailLink;
							$this->_insert_or_update('accessibility_pictogram', $pictogram, array('accessibility_pictogram_id' => $pictogram_id));

							// Insert offer link
							$link = array(
								'offer_id' => $offer_id,
								'accessibility_pictogram_id' => $pictogram_id,
								'poi_detail_link' => (string)$accessibility->PoiDetailLink,
							);
							$this->api->db->insert('accessibility', $link);

						}
					}

					// Get root category id
					$root_category_id = $this->_get_root_category($offer->Categories->Category[0]->attributes()->identifier);

					// Subscription
					if (($root_category_id == CATEGORY_EVENT) || ($root_category_id == CATEGORY_BOOKING)) {
						$fields = array();

						$fields['subscription_mandatory'] = (int)$offer->Subscription->SubscriptionMandatory;
						$fields['online_subscription_enabled'] = (int)$offer->Subscription->OnlineSubscriptionEnabled;
						$fields['subscription_contact'] = $this->_address($offer->Subscription->SubscriptionContact);
						$fields['subscription_link'] = (string)$offer->Subscription->SubscriptionLink;
						$this->_insert_or_update('subscription', $fields, array('offer_id' => $offer_id));

						// Subscription i18n
						if (isset($offer->Subscription->Details)) {
							foreach ($offer->Subscription->Details as $detail) {
								$subscription_detail = array();
								$subscription_detail['offer_id'] = $offer_id;
								$subscription_detail['language'] = (string)$detail->attributes()->language;
								$subscription_detail_where = $subscription_detail;
								$subscription_detail['subscription_details'] = (string)$detail;
								$this->_insert_or_update('subscription_i18n', $subscription_detail, $subscription_detail_where);
							}
						}
						else {
							$this->api->db->delete('subscription_i18n', array('offer_id' => $offer_id));
						}
						
					}

					$fields = array();

					// Events
					if ($root_category_id == CATEGORY_EVENT) {
						$fields['is_park_event'] = (int)$offer->ParkEvent;
						$fields['is_park_partner_event'] = (int)$offer->ParkPartnerEvent;
						$fields['public_transport_stop'] = (string)$offer->PublicTransportStop;
						$fields['kind_of_event'] = (string)$offer->Kind;

						$this->_insert_or_update('event', $fields, array('offer_id' => $offer_id));
					}

					// Products
					else if ($root_category_id == CATEGORY_PRODUCT) {
						$fields['public_transport_stop'] = (string)$offer->PublicTransportStop;
						$fields['number_of_rooms'] = (int)$offer->NumberOfRooms;
						$fields['has_conference_room'] = (int)$offer->HasConferenceRoom;
						$fields['has_playground'] = (int)$offer->HasPlayground;
						$fields['has_picnic_place'] = (int)$offer->HasPicnicPlace;
						$fields['has_fireplace'] = (int)$offer->HasFireplace;
						$fields['has_washrooms'] = (int)$offer->HasWashrooms;

						// Season months
						$fields['season_months'] = '';
						if (!empty($offer->SeasonMonths)) {
							$months = array();
							foreach ($offer->SeasonMonths->Month as $month) {
								$months[] = (int)$month;
							}
							$fields['season_months'] = implode(',', $months);
						}

						// Online shop
						if ($offer->OnlineShop) {
							
							// Online shop enabled
							$fields['online_shop_enabled'] = 1;

							// Online shop price
							$fields['online_shop_price'] = (float)$offer->OnlineShop->Price;

						}

						$this->_insert_or_update('product', $fields, array('offer_id' => $offer_id));

						// Suppliers
						$this->api->db->delete('supplier', array('offer_id' => $offer_id));
						if ($offer->Suppliers) {
							foreach ($offer->Suppliers->Supplier as $supplier) {
								$this->api->db->insert('supplier', array('offer_id' => $offer_id, 'contact' => $this->_address($supplier), 'is_park_partner' => (int)$supplier->ParkPartner));
							}
						}

						// Online shop: i18n
						if ($offer->OnlineShop) {
							
							// PaymentTerms
							if (isset($offer->OnlineShop->PaymentTerms)) {
								foreach ($offer->OnlineShop->PaymentTerms as $payment_term) {
									$offer_i18n = array();
									$offer_i18n['offer_id'] = $offer_id;
									$offer_i18n['language'] = (string)$payment_term->attributes()->language;
									$offer_i18n_where = $offer_i18n;
									$offer_i18n['online_shop_payment_terms'] = (string)$payment_term;
									$this->_insert_or_update('offer_i18n', $offer_i18n, $offer_i18n_where);
								}
							}
							
							// DeliveryConditions
							if (isset($offer->OnlineShop->DeliveryConditions)) {
								foreach ($offer->OnlineShop->DeliveryConditions as $delivery_condition) {
									$offer_i18n = array();
									$offer_i18n['offer_id'] = $offer_id;
									$offer_i18n['language'] = (string)$delivery_condition->attributes()->language;
									$offer_i18n_where = $offer_i18n;
									$offer_i18n['online_shop_delivery_conditions'] = (string)$delivery_condition;
									$this->_insert_or_update('offer_i18n', $offer_i18n, $offer_i18n_where);
								}
							}

							// Online shop: Article
							$this->api->db->delete('product_article', array('offer_id' => $offer_id));
							if ($offer->OnlineShop->Articles->Article) {
								foreach ($offer->OnlineShop->Articles->Article as $article) {
									
									// Set article id
									$article_id = $article->attributes()->identifier;

									// Insert article
									$this->api->db->insert('product_article', array(
										'product_article_id' => $article_id, 
										'offer_id' => $offer_id, 
										'supplier_contact' => $this->_address($article->Supplier),
									));

									// Article i18n: Title
									if (isset($article->ArticleTitle)) {
										foreach ($article->ArticleTitle as $title) {
											$article_i18n = array();
											$article_i18n['product_article_id'] = $article_id;
											$article_i18n['language'] = (string)$title->attributes()->language;
											$article_i18n_where = $article_i18n;
											$article_i18n['article_title'] = (string)$title;
											$this->_insert_or_update('product_article_i18n', $article_i18n, $article_i18n_where);
										}
									}

									// Article i18n: Description
									if (isset($article->ArticleDescription)) {
										foreach ($article->ArticleDescription as $description) {
											$article_i18n = array();
											$article_i18n['product_article_id'] = $article_id;
											$article_i18n['language'] = (string)$description->attributes()->language;
											$article_i18n_where = $article_i18n;
											$article_i18n['article_description'] = (string)$description;
											$this->_insert_or_update('product_article_i18n', $article_i18n, $article_i18n_where);
										}
									}

									// Article i18n: Ingredients
									if (isset($article->ArticleIngredients)) {
										foreach ($article->ArticleIngredients as $ingredients) {
											$article_i18n = array();
											$article_i18n['product_article_id'] = $article_id;
											$article_i18n['language'] = (string)$ingredients->attributes()->language;
											$article_i18n_where = $article_i18n;
											$article_i18n['article_ingredients'] = (string)$ingredients;
											$this->_insert_or_update('product_article_i18n', $article_i18n, $article_i18n_where);
										}
									}

									// Import article labels
									$this->api->db->delete('product_article_label', array('product_article_id' => $article_id));
									if (isset($article->ArticleLabels->ArticleLabel)) {

										// Iterate all article labels
										foreach ($article->ArticleLabels->ArticleLabel as $label) {

											// Set label id
											$label_id = $label->attributes()->identifier;

											// Iterate all labels
											foreach ($label->Label as $single_label) {

												// Insert article label
												$this->api->db->insert('product_article_label', array(
													'product_article_id' => $article_id, 
													'label_id' => $label_id, 
													'language' => $single_label->attributes()->language, 
													'label_title' => (string)$single_label, 
													'label_url' => $single_label->attributes()->url, 
													'label_icon' => $single_label->attributes()->icon, 
												));

											}
										}
									}

								}
							}

						}

					}

					// Bookings
					else if ($root_category_id == CATEGORY_BOOKING) {
						$fields['is_park_partner'] = (int)$offer->IsParkPartner;
						$fields['min_group_subscriber'] = (int)$offer->MinGroupSubscribers;
						$fields['max_group_subscriber'] = (int)$offer->MaxGroupSubscribers;
						$fields['min_individual_subscriber'] = (int)$offer->MinIndividualSubscribers;
						$fields['max_individual_subscriber'] = (int)$offer->MaxIndividualSubscribers;
						$fields['public_transport_stop'] = (string)$offer->PublicTransportStop;

						// Season months
						$fields['season_months'] = '';
						if (!empty($offer->SeasonMonths)) {
							$months = array();
							foreach ($offer->SeasonMonths->Month as $month) {
								$months[] = (int)$month;
							}
							$fields['season_months'] = implode(',', $months);
						}

						$this->_insert_or_update('booking', $fields, array('offer_id' => $offer_id));

						// Accommodations
						$this->api->db->delete('accommodation', array('offer_id' => $offer_id));
						if ($offer->Accommodations) {
							foreach ($offer->Accommodations->Accommodation as $accommodation) {
								$this->api->db->insert('accommodation', array('offer_id' => $offer_id, 'contact' => $this->_address($accommodation), 'is_park_partner' => (int)$accommodation->ParkPartner));
							}
						}
					}

					// Activities
					else if ($root_category_id == CATEGORY_ACTIVITY) {
						$fields['start_place_info'] = (string)$offer->StartPlaceInfo;
						$fields['start_place_altitude'] = (int)$offer->StartPlaceAltitude;
						$fields['goal_place_info'] = (string)$offer->GoalPlaceInfo;
						$fields['goal_place_altitude'] = (int)$offer->GoalPlaceAltitude;
						$fields['route_length'] = (float)$offer->RouteLength;
						$fields['untarred_route_length'] = (float)$offer->UntarredRouteLength;
						$fields['public_transport_start'] = (string)$offer->PublicTransportStart;
						$fields['public_transport_stop'] = (string)$offer->PublicTransportStop;
						$fields['altitude_differential'] = (int)$offer->AltitudeDifferential;
						$fields['altitude_ascent'] = (int)$offer->AltitudeAscent;
						$fields['altitude_descent'] = (int)$offer->AltitudeDescent;
						$fields['time_required'] = (string)$offer->TimeRequired;
						$fields['time_required_minutes'] = (int)$offer->TimeRequiredMinutes;
						$fields['level_technics'] = $this->mappings['levels'][(string)$offer->LevelTechnics];
						$fields['level_condition'] = $this->mappings['levels'][(string)$offer->LevelCondition];
						$fields['has_playground'] = (int)$offer->HasPlayground;
						$fields['has_picnic_place'] = (int)$offer->HasPicnicPlace;
						$fields['has_fireplace'] = (int)$offer->HasFireplace;
						$fields['has_washrooms'] = (int)$offer->HasWashrooms;

						// Route condition ID and color
						if (!empty($offer->RouteConditions)) {
							$fields['route_condition_id'] = $lang = intval($offer->RouteConditions->attributes()->identifier);
							$fields['route_condition_color'] = $lang = (string)$offer->RouteConditions->attributes()->color;
						}

						// Season months
						$fields['season_months'] = '';
						if (!empty($offer->SeasonMonths)) {
							$months = array();
							foreach ($offer->SeasonMonths->Month as $month) {
								$months[] = (int)$month;
							}
							$fields['season_months'] = implode(',', $months);
						}

						// POI
						$fields['poi'] = NULL;
						if (isset($offer->POI) && !empty($offer->POI->OfferId)) {
							foreach ($offer->POI->OfferId as $poi) {
								$fields['poi'][] = $poi;
							}
							$fields['poi'] = implode(',', $fields['poi']);
						}

						$this->_insert_or_update('activity', $fields, array('offer_id' => $offer_id));

						// Route
						$this->api->db->delete('offer_route', array('offer_id' => $offer_id));
						if (isset($offer->Route->Point)) {
							foreach ($offer->Route->Point as $point) {

								$fields = array();
								$fields['offer_id'] = $offer_id;
								$fields['latitude'] = (float)$point->Latitude;
								$fields['longitude'] = (float)$point->Longitude;
								$fields['sort'] = (int)$point->Index;

								$this->api->db->insert('offer_route', $fields);
							}
						}
					}

					// Projects
					else if ($root_category_id == CATEGORY_PROJECT) {
						$fields['duration_from'] = (int)$offer->DurationFrom;
						$fields['duration_to'] = (int)$offer->DurationTo;
						$fields['status'] = intval($offer->Status->attributes()->identifier);

						// POI
						$fields['poi'] = NULL;
						if (isset($offer->POI) && !empty($offer->POI->OfferId)) {
							foreach ($offer->POI->OfferId as $poi) {
								$fields['poi'][] = $poi;
							}
							$fields['poi'] = implode(',', $fields['poi']);
						}

						$this->_insert_or_update('project', $fields, array('offer_id' => $offer_id));

					}

					// Check off this offer
					array_push($offers_checklist, $offer_id);
					$ctr_imported++;

					$this->api->logger->info("\tImported offer with ID ".$offer_id);
				}
			}

			$this->api->logger->info("Successfully imported ".$ctr_imported." offers.");

			// Delete offers which did not get updated
			if ($force === TRUE) {
				$this->api->logger->info("Deleting offers which were not updated...");

				// Iterate all existing offers
				$all_offers = $this->api->db->get('offer', NULL, NULL, array('offer_id'));
				while ($offer = mysqli_fetch_assoc($all_offers)) {
					if ( ! in_array($offer['offer_id'], $offers_checklist)) {
						$this->api->db->delete('offer', array('offer_id' => $offer['offer_id']));
						$this->api->logger->info("\tDeleted offer with ID ".$offer['offer_id']);
						$ctr_deleted++;
					}
				}

				if ($ctr_deleted == 0) {
					$this->api->logger->info("\tNothing to delete");
				}
			}
			else {
				$this->api->logger->info("Deleted ".$ctr_deleted." offers.");
			}

			$this->api->db->update('api', array('last_import' => time()));

			// Set message
			$message = 'Import finished successfully.';

			// Log message
			$this->api->logger->info($message);

		}

		// XML error
		else {

			// Log error
			$this->api->logger->info("XML is not valid: ".$url);

		}

	}


	/**
	 * Clean up offers (remove inactive offers with active id)
	 *
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	public function clean_up_offers($url) {

		// Load xml
		$this->xml = $this->api->load_external_xml($url);

		// Init
		$active_offers = array();
		$ctr_deleted = 0;

		// Check xml
		if ($this->xml !== FALSE) {

			// Get active offers
			if (!empty($this->xml)) {
				foreach ($this->xml->Offer as $active_offer) {
					$active_offers[] = intval($active_offer->attributes()->identifier);
				}
			}

			// Remove inactive offers
			$this->api->logger->info("Clean up offers");
			$all_offers = $this->api->db->get('offer', NULL, NULL, array('offer_id'));

			// Iterate all existing offers
			while ($offer = mysqli_fetch_assoc($all_offers)) {

				// Delete inactive offer
				if ( ! in_array($offer['offer_id'], $active_offers)) {
					$this->api->db->delete('offer', array('offer_id' => $offer['offer_id']));
					$this->api->logger->info("\tDeleted inactive offer with ID ".$offer['offer_id']);
					$ctr_deleted++;
				}

			}

			// Log result
			if ($ctr_deleted == 0) {
				$this->api->logger->info("\tNo inactive offers");
			} 
			else {
				$this->api->logger->info("Deleted ".$ctr_deleted." inactive offer(s).");
			}


		}

		// Log error and exit
		else {
			$this->api->logger->info("XML is not valid: ".$url);
		}

	}


	/**
	 * Synchronise target groups
	 *
	 * @access private
	 * @return void
	 */
	public function sync_target_groups() {

		// Get and parse json
		$json = file_get_contents($this->api->config['json_export_target_groups']);
		$target_groups = json_decode($json, TRUE);

		// Sync target groups
		if (!empty($target_groups) && is_array($target_groups)) {
			$this->api->model->sync_target_groups($target_groups);
		}

	}


	/**
	 * Synchronise categories
	 *
	 * @access private
	 * @return void
	 */
	public function sync_categories() {

		// Get and parse json
		$json = file_get_contents($this->api->config['json_export_categories']);
		$categories = json_decode($json, TRUE);

		// Sync target groups
		if (!empty($categories) && is_array($categories)) {
			$this->api->model->sync_categories($categories);
		}

	}


	/**
	 * Private method for retrieving the root category
	 *
	 * @param category_id
	 * @return integer|boolean
	 */
	private function _get_root_category($category_id) {
		if (!empty($category_id)) {

			while($category_id > 0) {
				$q_category = $this->api->db->get('category', array('category_id' => $category_id));
				if (mysqli_num_rows($q_category) > 0) {
					$category = mysqli_fetch_object($q_category);
					$category_id = $category->parent_id;
					$last_category_id = $category->category_id;
				}
			}

			return $last_category_id;
		}

		return FALSE;
	}


	/**
	 * Private method for inserting or updating db record
	 *
	 * @param table Table name
	 * @param fields Array of fields with values
	 * @param where Array of fields with values
	 * @return resource
	 */
	private function _insert_or_update($table, $fields, $where) {
		if (!empty($table) && !empty($fields) && !empty($where)) {
			// Check if offer exists in database
			$exists = $this->api->db->get($table, $where);

			if (mysqli_num_rows($exists) > 0) {
				// Update db record
				return $this->api->db->update($table, $fields, $where);
			}
			else {
				// Insert new db record
				$fields = array_merge($fields, $where);
				return $this->api->db->insert($table, $fields);
			}
		}
	}


	/**
	 * Private method for parsing Date/Time
	 *
	 * @param value Date/Time string
	 * @param format Desired format
	 * @return string
	 */
	private function _datetime($value, $format = "Y-m-d H:i:s") {
		$datetime = new DateTime($value);
		return $datetime->format($format);
	}


	/**
	 * Private method for parsing Address from XML
	 *
	 * @param contact Element with contact info
	 * @return string
	 */
	private function _address($contact) {
		$address = '';
		if (!empty($contact->Company)) {
			$address .= $contact->Company."\n";
		}
		if (!empty($contact->FirstName) || !empty($contact->LastName)) {
			if (!empty($contact->FirstName)) {
				$address .= $contact->FirstName.' ';
			}
			if (!empty($contact->LastName)) {
				$address .= $contact->LastName;
			}
			$address .= "\n";
		}
		if (isset($contact->Address) && !empty($contact->Address)) {
			$address .= $contact->Address."\n";
		}
		$address .= ($contact->ZipCode > 0 ? $contact->ZipCode." " : "").$contact->Locality."\n";
		if (isset($contact->Phone) && !empty($contact->Phone)) {
			$address .= 'Tel. '.$contact->Phone."\n";
		}
		if (isset($contact->Mobile) && !empty($contact->Mobile)) {
			$address .= 'Mobile '.$contact->Mobile."\n";
		}
		if (isset($contact->Fax) && !empty($contact->Fax)) {
			$address .= 'Fax '.$contact->Fax."\n";
		}
		$address .= $contact->Email."\n";
		$address .= $contact->URL."\n";

		return (string)trim($address);
	}


	/**
	 * get Last insert.
	 *
	 * @access private
	 * @param mixed $table
	 * @return void
	 */
	private function _get_last_id($name_id, $table) {
		$query =  $this->api->db->query('select MAX('. $name_id. ') as id FROM '. $table);
		$result = mysqli_fetch_object($query);

		return $result->id;
	}


}