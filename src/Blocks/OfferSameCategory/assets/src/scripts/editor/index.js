import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __, _x } from '@wordpress/i18n';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;

registerBlockType(block_name, {
	edit: ({ attributes, setAttributes }) => {
		const blockProps = useBlockProps();
		const { button_text, title } = attributes;

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
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<div
						className={`c-message c-message--info`}
						dangerouslySetInnerHTML={{
							__html: _x(
								'Posts from the same category.',
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
