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
			title_additional,
			title_infrastructure,
			title_season,
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
								'Season title',
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
								'Infrastructure title',
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
								'Additional info title',
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
								'Suitability title',
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
