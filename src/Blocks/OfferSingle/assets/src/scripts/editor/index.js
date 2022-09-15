import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { _x } from '@wordpress/i18n';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;

import icon from '../_components/icon';

registerBlockType(block_name, {
	icon,
	edit: () => {
		const blockProps = useBlockProps();
		return (
			<div {...blockProps}>
				<div
					className={'c-editormessage c-editormessage--info'}
					dangerouslySetInnerHTML={{
						__html: _x(
							'Placeholder for single offer content. The actual content will be loaded dynamically by passing the offer-id to the page on which this block has been placed.',
							'Editor message',
							'shp_gantrisch_adb'
						),
					}}
				/>
			</div>
		);
	},
});
