import { getBlockDefaultClassName, registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __, _x } from '@wordpress/i18n';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;
const classNameBase = getBlockDefaultClassName(block_name);

import image_sizes from '../../../../../../../.build/gutenberg/_components/image-sizes';

registerBlockType(block_name, {
	edit: (props) => {
		const blockProps = useBlockProps();

		const { attributes, setAttributes } = props;
		const { image_size } = attributes;

		let image_label = 'Undefined';

		if (!!image_sizes && !!image_size) {
			image_label = image_sizes.find(
				(entry) => entry.value === image_size
			).label;
		}

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Settings')}>
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
				<div {...blockProps}>
					{!!image_size && (
						<figure
							className={`${classNameBase}__figure ${classNameBase}__figure--empty`}
						>
							<div
								className={`${classNameBase}__figcaption`}
								dangerouslySetInnerHTML={{
									__html: `${image_label} image size`,
								}}
							/>
						</figure>
					)}
					{!image_size && (
						<figure
							className={`${classNameBase}__figure ${classNameBase}__figure--empty`}
						>
							<div
								className={`${classNameBase}__figcaption`}
								dangerouslySetInnerHTML={{
									__html: _x(
										'No image size selected',
										'Info text',
										'shp_gantrisch_adb'
									),
								}}
							/>
						</figure>
					)}
				</div>
			</>
		);
	},
});
