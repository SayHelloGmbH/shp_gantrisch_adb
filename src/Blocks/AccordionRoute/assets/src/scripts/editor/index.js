import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { _x } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;

registerBlockType(block_name, {
	edit: ({ attributes, setAttributes }) => {
		const {
			title_block,
			title_routelength,
			title_ascent,
			title_descent,
			title_unpaved,
			title_heightdifference,
			title_time,
			title_difficulty_technical,
			title_difficulty_condition,
			title_equipmentrental,
			title_safety,
			title_signals,
		} = attributes;
		const blockProps = useBlockProps();

		return (
			<>
				<InspectorControls>
					<PanelBody title={_x('Settings')} initialOpen={true}>
						<TextControl
							label={_x(
								'Block title',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_block}
							onChange={(title_block) =>
								setAttributes({ title_block })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Routenlänge',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_routelength}
							onChange={(title_routelength) =>
								setAttributes({ title_routelength })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Höhenmeter Aufstieg',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_ascent}
							onChange={(title_ascent) =>
								setAttributes({ title_ascent })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Höhenmeter Abstieg',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_descent}
							onChange={(title_descent) =>
								setAttributes({ title_descent })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Anteil ungeteerter Wegstrecke',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_unpaved}
							onChange={(title_unpaved) =>
								setAttributes({ title_unpaved })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Höhendifferenz',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_heightdifference}
							onChange={(title_heightdifference) =>
								setAttributes({ title_heightdifference })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Zeitbedarf',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_time}
							onChange={(title_time) =>
								setAttributes({ title_time })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Schwierigkeitsgrad Technik',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_difficulty_technical}
							onChange={(title_difficulty_technical) =>
								setAttributes({ title_difficulty_technical })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Schwierigkeitsgrad Kondition',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_difficulty_condition}
							onChange={(title_difficulty_condition) =>
								setAttributes({ title_difficulty_condition })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Materialmiete',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_equipmentrental}
							onChange={(title_equipmentrental) =>
								setAttributes({ title_equipmentrental })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Sicherheitshinweise',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_safety}
							onChange={(title_safety) =>
								setAttributes({ title_safety })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Signalisation',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_signals}
							onChange={(title_signals) =>
								setAttributes({ title_signals })
							}
						/>
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					<ServerSideRender
						block={block_name}
						attributes={attributes}
					/>
				</div>
			</>
		);
	},
});
