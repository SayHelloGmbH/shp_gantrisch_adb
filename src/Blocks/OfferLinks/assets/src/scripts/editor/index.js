import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __, _x } from '@wordpress/i18n';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;

registerBlockType(block_name, {
	edit: ({ attributes, setAttributes }) => {
		const blockProps = useBlockProps();
		const { title } = attributes;

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Settings')} initialOpen={true}>
						<TextControl
							label={_x(
								'Title',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title}
							onChange={(title) => setAttributes({ title })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<div
						className={`c-message c-message--info`}
						dangerouslySetInnerHTML={{
							__html: _x(
								'Placeholder for ADB offer links.',
								'Editor message',
								'shp_gantrisch_adb'
							),
						}}
					/>
				</div>
			</>
		);
	},
});
