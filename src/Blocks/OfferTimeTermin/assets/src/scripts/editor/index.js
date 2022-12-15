import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __, _x } from '@wordpress/i18n';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;

registerBlockType(block_name, {
	edit: ({ attributes, setAttributes }) => {
		const { title } = attributes;
		const blockProps = useBlockProps();

		return (
			<>
				<InspectorControls>
					<PanelBody title={_x('Settings')} initialOpen={true}>
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
					{title && (
						<RichText.Content
							tagName="h2"
							className={`${classNameBase}__title`}
							value={title}
						/>
					)}
					<div
						className={`${classNameBase}__content`}
						dangerouslySetInnerHTML={{
							__html: _x(
								'Placeholder for ADB time/date.',
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
