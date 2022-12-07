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
					className={`${classNameBase}__content c-message c-message--error`}
					dangerouslySetInnerHTML={{
						__html: _x(
							'ADB Single offer map. Output currently disabled.',
							'Editor message',
							'shp_gantrisch_adb'
						),
					}}
				/>
			</div>
		);
	},
});
