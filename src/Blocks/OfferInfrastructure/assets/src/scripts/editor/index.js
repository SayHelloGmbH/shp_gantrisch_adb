import { getBlockDefaultClassName, registerBlockType } from '@wordpress/blocks';
import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import { __, _x } from '@wordpress/i18n';
import block_json from '../../../../block.json';
const { name: block_name } = block_json;
const classNameBase = getBlockDefaultClassName(block_name);

import icon from './icon';

registerBlockType(block_name, {
	icon,
	edit: ({ attributes, setAttributes }) => {
		const blockProps = useBlockProps();
		const { title } = attributes;

		return (
			<>
				<InspectorControls>
					<PanelBody title={_x('Settings')} initialOpen={true}>
						<TextControl
							label={_x(
								'Title (only output if content available)',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title}
							onChange={(title) => setAttributes({ title })}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<div className={`${classNameBase}__content`}>
						{title && (
							<RichText.Content
								tagName="h2"
								className={`${classNameBase}__title`}
								value={title}
							/>
						)}
						<div
							className={`${classNameBase}__title`}
							dangerouslySetInnerHTML={{
								__html: _x(
									'Single offer infrastructure. Single offer infrastructure. Single offer infrastructure. Single offer infrastructure. Single offer infrastructure. Single offer infrastructure. Single offer infrastructure. Single offer infrastructure. Single offer infrastructure. Single offer infrastructure. Single offer infrastructure. Single offer infrastructure. Single offer infrastructure.',
									'Editor message',
									'shp_gantrisch_adb'
								),
							}}
						/>
					</div>
				</div>
			</>
		);
	},
});
