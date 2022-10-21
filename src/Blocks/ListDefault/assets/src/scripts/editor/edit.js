import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	Disabled,
	PanelBody,
	TreeSelect,
	Spinner,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { withSelect } from '@wordpress/data';
import { __, _x } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

import metadata from '../../../../block.json';
import image_sizes from '../../../../../../../.build/gutenberg/_components/image-sizes';

const Edit = ({ attributes, setAttributes, api_categories }) => {
	const { category, buttonText, image_size } = attributes;

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
					<TextControl
						label={_x(
							'Button text',
							'TextControl label',
							'shp_gantrisch_adb'
						)}
						value={buttonText}
						onChange={(buttonText) => setAttributes({ buttonText })}
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
