import apiFetch from '@wordpress/api-fetch';
import { createReduxStore, register } from '@wordpress/data';

// Some default values
const DEFAULT_STATE = {
		categories: null,
	},
	DEFAULT_ACTION = {};

// Actions which can be carried out on the data store
const actions = {
	setState(item, categories) {
		return {
			type: 'SET_GET_CATEGORIES',
			item,
			categories,
		};
	},
	fetchFromAPI(path) {
		return {
			type: 'FETCH_FROM_API',
			path,
		};
	},
};

// Create a store which we can use via wp.data.select("shp_gantrisch_adb/categories_for_select")
const store = createReduxStore('shp_gantrisch_adb/categories_for_select', {
	reducer(state = DEFAULT_STATE, action = DEFAULT_ACTION) {
		// Update the state with the fetched value
		switch (action.type) {
			case 'SET_GET_CATEGORIES':
				const updated_state = {
					...state,
					categories: action.categories,
				};
				return updated_state;
		}

		return state;
	},

	actions,

	selectors: {
		getCategories(state, item) {
			// Get the value from the state object
			const { categories } = state;
			return categories;
		},
	},

	controls: {
		FETCH_FROM_API(action) {
			// Get the data from the API route
			return apiFetch({ path: action.path });
		},
	},

	resolvers: {
		*getCategories(item) {
			// Get the results from the API and update the state object.
			const path = '/shp_gantrisch_adb/categories_for_select/';
			const categories = yield actions.fetchFromAPI(path);
			return actions.setState(item, categories);
		},
	},
});

register(store);
