import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __, _x } from '@wordpress/i18n';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;

registerBlockType(block_name, {
	edit: ({ attributes, setAttributes }) => {
		const blockProps = useBlockProps();
		const { prefix } = attributes;

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Settings')} initialOpen={true}>
						<TextControl
							label={_x(
								'Text prefix',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={prefix}
							onChange={(prefix) => setAttributes({ prefix })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<div
						className={`c-message c-message--info`}
						dangerouslySetInnerHTML={{
							__html: _x(
								'Placeholder for ADB route length.',
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
