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
		const blockProps = useBlockProps({
			className: 'c-message c-message--info',
		});
		const { button_text, message, title_sub_at, title_sub_required } =
			attributes;

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
							value={title_sub_required}
							onChange={(title_sub_required) =>
								setAttributes({ title_sub_required })
							}
						/>
						<TextControl
							label={_x(
								'Text',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={message}
							onChange={(message) => setAttributes({ message })}
						/>
						<TextControl
							label={_x(
								'Title - where to subscribe',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_sub_at}
							onChange={(title_sub_at) =>
								setAttributes({ title_sub_at })
							}
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
					<div className={`${classNameBase}__content`}>
						{title_sub_required && (
							<RichText.Content
								tagName="h2"
								className={`${classNameBase}__title`}
								value={title_sub_required}
							/>
						)}
						{message && (
							<RichText.Content
								tagName="div"
								className={`${classNameBase}__message`}
								value={message}
							/>
						)}
						{title_sub_at && (
							<RichText.Content
								tagName="h2"
								className={`${classNameBase}__title`}
								value={title_sub_at}
							/>
						)}
						<div
							dangerouslySetInnerHTML={{
								__html: _x(
									'Single offer subscription information.',
									'Editor message',
									'shp_gantrisch_adb'
								),
							}}
						/>
						{button_text && (
							<div className={'wp-block-button'}>
								<RichText.Content
									tagName="div"
									className={`wp-block-button__link ${classNameBase}__button`}
									value={button_text}
								/>
							</div>
						)}
					</div>
				</div>
			</>
		);
	},
});
