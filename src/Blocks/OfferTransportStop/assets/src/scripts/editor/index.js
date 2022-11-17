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
		const { link_text, title } = attributes;

		return (
			<>
				<InspectorControls>
					<PanelBody title={_x('Settings')} initialOpen={true}>
						<TextControl
							label={_x(
								'Title (only if stop available)',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title}
							onChange={(title) => setAttributes({ title })}
						/>
						<TextControl
							label={_x(
								'Link text (%s will be replaced with the transport stop)',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={link_text}
							onChange={(link_text) =>
								setAttributes({ link_text })
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
						{link_text && (
							<RichText.Content
								tagName="div"
								className={`${classNameBase}__link-text`}
								value={link_text}
							/>
						)}
					</div>
				</div>
			</>
		);
	},
});
