import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { Disabled, PanelBody } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { __, _x } from '@wordpress/i18n';

import metadata from '../../../../block.json';
import icon from './icon';

registerBlockType(metadata.name, {
	icon,
	edit: ({ attributes }) => {
		const { category } = attributes;

		return (
			<>
				<InspectorControls>
					<PanelBody
						title={_x('Settings')}
						initialOpen={true}
					></PanelBody>
				</InspectorControls>
				<div {...useBlockProps()}>
					<Disabled>
						<ServerSideRender
							block={metadata.name}
							attributes={{ category: category }}
						/>
					</Disabled>
				</div>
			</>
		);
	},
});
