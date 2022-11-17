import { getBlockDefaultClassName, registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { _x } from '@wordpress/i18n';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;
const classNameBase = getBlockDefaultClassName(block_name);

registerBlockType(block_name, {
	edit: () => {
		const blockProps = useBlockProps();
		return (
			<div {...blockProps}>
				<div
					className={`${classNameBase}__title`}
					dangerouslySetInnerHTML={{
						__html: _x(
							'Single offer description long. Single offer description long. Single offer description long. Single offer description long. Single offer description long. Single offer description long. Single offer description long. Single offer description long. Single offer description long. Single offer description long. Single offer description long. Single offer description long. Single offer description long.',
							'Editor message',
							'shp_gantrisch_adb'
						),
					}}
				/>
			</div>
		);
	},
});
