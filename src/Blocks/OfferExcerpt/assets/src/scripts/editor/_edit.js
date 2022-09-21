import classnames from 'classnames';

import {
	InspectorControls,
	withColors,
	ContrastChecker,
	getColorClassName,
	PanelColorSettings,
	useBlockProps,
} from '@wordpress/block-editor';
import { getBlockDefaultClassName } from '@wordpress/blocks';
import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';
import { Fragment } from '@wordpress/element';
import { __, _x } from '@wordpress/i18n';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;

/**
 * This is a complete panel with two colour selectors. The functions
 * setTextColor and setBackgroundColor and the objects textColor
 * and backgroundColor are provided by the withColors functionality.
 */
const ColorPicker = ({ props }) => {
	const { backgroundColor, textColor, setBackgroundColor, setTextColor } =
		props;

	return (
		<PanelColorSettings
			title={__('Styles')}
			initialOpen={false}
			disableCustomColors={true}
			colorSettings={[
				{
					value: textColor.color,
					onChange: setTextColor,
					label: _x(
						'Text',
						'Color settings label',
						'shp_gantrisch_adb'
					),
				},
				{
					value: backgroundColor.color,
					onChange: setBackgroundColor,
					label: _x(
						'Background',
						'Color settings label',
						'shp_gantrisch_adb'
					),
				},
			]}
		>
			<ContrastChecker
				backgroundColor={backgroundColor.color}
				textColor={textColor.color}
				isLargeText={false}
			/>
		</PanelColorSettings>
	);
};

const Edit = (props) => {
	const { attributes } = props;
	const { align, backgroundColor, textColor } = attributes;

	let classNameBase = getBlockDefaultClassName(block_name);

	var backgroundColorClass = getColorClassName('background', backgroundColor),
		textColorClass = getColorClassName('color', textColor),
		classNames = classnames({
			['align' + align]: !!align,
			[backgroundColorClass]: backgroundColorClass,
			[textColorClass]: textColorClass,
			'has-background': !!backgroundColor,
			'has-text-color': !!textColor,
		});

	const blockProps = useBlockProps({
		className: classNames,
	});

	return (
		<Fragment>
			<InspectorControls>
				<ColorPicker props={props} />
			</InspectorControls>
			<div {...blockProps}>
				<div
					className={`${classNameBase}__inner`}
					dangerouslySetInnerHTML={{
						__html: _x(
							'Single offer excerpt. Single offer excerpt. Single offer excerpt. Single offer excerpt. Single offer excerpt. Single offer excerpt.',
							'Editor message',
							'shp_gantrisch_adb'
						),
					}}
				/>
			</div>
		</Fragment>
	);
};

/**
 * This is the HOC which is returned to the registerBlockType function.
 *
 * compose extends the linked Edit Component with other functionality.
 *
 * withColors provides the indicated properties for managing the colors.
 * The color attributes are usually hex values; this functionality converts
 * the hex colors into an object, which contain color.slug (name) and color.color (hex)
 *
 * withSelect gets the available colors via REST API, which are then
 * available for selection in the color pickers.
 *
 */
export default compose([
	withColors('backgroundColor', 'textColor'),
	withSelect((select) => {
		let colors = [],
			colorSettings = select('core/editor').getEditorSettings().colors;

		if (colorSettings) {
			colorSettings.map((color) => {
				colors.push({ color: color.color, name: color.name });
			});
		}

		return {
			colors: colors,
		};
	}),
])(Edit);
