import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __, _x } from '@wordpress/i18n';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;

registerBlockType(block_name, {
	edit: () => {
		const blockProps = useBlockProps();

		return (
			<div {...blockProps}>
				<div
					className={`c-message c-message--error`}
					dangerouslySetInnerHTML={{
						__html: _x(
							'Placeholder for ADB time/date. API does not currently return any valid values for this element (30.11.2022)',
							'Editor message',
							'shp_gantrisch_adb'
						),
					}}
				/>
			</div>
		);
	},
});
