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
			title_start,
			title_start_stop,
			title_goal,
			title_goal_stop,
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
								'Überschrift Startort',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_start}
							onChange={(title_start) =>
								setAttributes({ title_start })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift öV Startort',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_start_stop}
							onChange={(title_start_stop) =>
								setAttributes({ title_start_stop })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift Zielort',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_goal}
							onChange={(title_goal) =>
								setAttributes({ title_goal })
							}
						/>
						<TextControl
							label={_x(
								'Überschrift öV Zielort',
								'TextControl label',
								'shp_gantrisch_adb'
							)}
							value={title_goal_stop}
							onChange={(title_goal_stop) =>
								setAttributes({ title_goal_stop })
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
