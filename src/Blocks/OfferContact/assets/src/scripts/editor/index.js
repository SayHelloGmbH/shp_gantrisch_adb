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

registerBlockType(block_name, {
	edit: ({ attributes, setAttributes }) => {
		const blockProps = useBlockProps();
		const { partner_label, title } = attributes;

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
						<TextControl
							label={_x(
								'Partner label (only if offer contact is a partner)',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={partner_label}
							onChange={(partner_label) =>
								setAttributes({ partner_label })
							}
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
						{partner_label && (
							<RichText.Content
								tagName="p"
								className={`${classNameBase}__partner_label`}
								value={partner_label}
							/>
						)}
						<div
							className={`${classNameBase}__title`}
							dangerouslySetInnerHTML={{
								__html: _x(
									'Single offer contact information.',
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
