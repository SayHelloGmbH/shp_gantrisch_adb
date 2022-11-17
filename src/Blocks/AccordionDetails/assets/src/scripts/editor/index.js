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
			title_termin,
			title_leistungen,
			title_price,
			title_place,
			title_opening,
			title_season,
			title_infrastructure,
			title_additional,
			title_suitability,
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
								'Überschrift Termin',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_termin}
							onChange={(title_termin) =>
								setAttributes({ title_termin })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Leistungen',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_leistungen}
							onChange={(title_leistungen) =>
								setAttributes({ title_leistungen })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Preis',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_price}
							onChange={(title_price) =>
								setAttributes({ title_price })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Ort',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_place}
							onChange={(title_place) =>
								setAttributes({ title_place })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Öffnungszeiten',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_opening}
							onChange={(title_opening) =>
								setAttributes({ title_opening })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Saison',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_season}
							onChange={(title_season) =>
								setAttributes({ title_season })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Infrastruktur',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_infrastructure}
							onChange={(title_infrastructure) =>
								setAttributes({ title_infrastructure })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Zusatzinformation',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_additional}
							onChange={(title_additional) =>
								setAttributes({ title_additional })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Eignung',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_suitability}
							onChange={(title_suitability) =>
								setAttributes({ title_suitability })
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
