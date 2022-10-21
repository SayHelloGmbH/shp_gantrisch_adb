import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	Disabled,
	PanelBody,
	TreeSelect,
	Spinner,
	SelectControl,
	TextControl,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';
import { withSelect } from '@wordpress/data';
import { __, _x } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

import metadata from '../../../../block.json';
import image_sizes from '../../../../../../../.build/gutenberg/_components/image-sizes';

const Edit = ({ attributes, setAttributes, api_categories }) => {
	const { category, button_text, load_more_text, image_size, initial_count } =
		attributes;

	let image_label = 'Undefined';

	if (!!image_sizes && !!image_size) {
		image_label = image_sizes.find(
			(entry) => entry.value === image_size
		).label;
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={_x('Settings')} initialOpen={true}>
					{(!api_categories || !api_categories.length) && <Spinner />}
					{!!api_categories.length && (
						<TreeSelect
							label={_x(
								'Select a category',
								'SelectControl label',
								'shp_gantrisch_adb'
							)}
							selectedId={category}
							onChange={(category) => {
								setAttributes({ category });
							}}
							tree={api_categories}
						/>
					)}
					<NumberControl
						label={_x(
							'Number of entries in initial view',
							'SelectControl label',
							'shp_gantrisch_adb'
						)}
						isShiftStepEnabled={true}
						shiftStep={3}
						min={1}
						value={initial_count}
						onChange={(initial_count) =>
							setAttributes({ initial_count })
						}
					/>
					<TextControl
						label={_x(
							'Button text',
							'TextControl label',
							'shp_gantrisch_adb'
						)}
						value={button_text}
						onChange={(button_text) =>
							setAttributes({ button_text })
						}
					/>
					<SelectControl
						label={_x(
							'Select an image size',
							'SelectControl label',
							'shp_gantrisch_adb'
						)}
						value={image_size}
						options={image_sizes}
						onChange={(image_size) => {
							setAttributes({ image_size });
						}}
					/>
					<TextControl
						label={_x(
							'Load more button text',
							'TextControl label',
							'shp_gantrisch_adb'
						)}
						value={load_more_text}
						onChange={(load_more_text) =>
							setAttributes({ load_more_text })
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...useBlockProps()}>
				<Disabled>
					<ServerSideRender
						block={metadata.name}
						attributes={{ category }}
					/>
				</Disabled>
			</div>
		</>
	);
};

export default withSelect((select, props) => {
	let api_categories = [
		{
			id: '0',
			name: __('No selection'),
		},
	];

	let categoryEntries = select(
		'shp_gantrisch_adb/categories_for_select'
	).getCategories();

	if (categoryEntries) {
		Object.values(categoryEntries).map((category) => {
			const entry = {
				id: category.id,
				name: category.name,
				children: category.children,
			};

			api_categories.push(entry);

			return category;
		});
	}

	return { ...props, api_categories: api_categories };
})(Edit);
