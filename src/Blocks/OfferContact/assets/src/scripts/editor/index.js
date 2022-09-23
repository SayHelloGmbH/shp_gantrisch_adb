import { registerBlockType } from '@wordpress/blocks';

import { _x } from '@wordpress/i18n';
import block_json from '../../../../block.json';
const { name: block_name } = block_json;

import icon from './icon';

registerBlockType(block_name, {
	icon,
	edit: () => {
		const blockProps = useBlockProps();
		return (
			<div {...blockProps}>
				<div
					className={`${classNameBase}__title`}
					dangerouslySetInnerHTML={{
						__html: _x(
							'Single offer contact information. Single offer contact information. Single offer contact information. Single offer contact information.',
							'Editor message',
							'shp_gantrisch_adb'
						),
					}}
				/>
			</div>
		);
	},
});
