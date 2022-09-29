import { getBlockDefaultClassName, registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import { _x } from '@wordpress/i18n';
import block_json from '../../../../block.json';
const { name: block_name } = block_json;
const classNameBase = getBlockDefaultClassName(block_name);

import icon from './icon';

registerBlockType(block_name, {
	icon,
	edit: ({ attributes, setAttributes }) => {
		const blockProps = useBlockProps();
		const { message } = attributes;

		return (
			<>
				<InspectorControls>
					<PanelBody title={_x('Settings')} initialOpen={true}>
						<TextControl
							label={_x(
								'Text if subscription is necessary',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={message}
							onChange={(message) => setAttributes({ message })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<div
						className={`${classNameBase}__content`}
						dangerouslySetInnerHTML={{
							__html: _x(
								'Single offer subscription text.',
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
