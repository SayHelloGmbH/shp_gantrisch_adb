import { getBlockDefaultClassName, registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { _x } from '@wordpress/i18n';

import block_json from '../../../../block.json';
const { name: block_name } = block_json;
const classNameBase = getBlockDefaultClassName(block_name);

import icon from './icon';

registerBlockType(block_name, {
	icon,
	edit: () => {
		const blockProps = useBlockProps();
		return (
			<div {...blockProps}>
				<div className={`${classNameBase}__entries`}>
					{['Category 1', 'Category 2', 'Category 3'].map(
						(category, index) => {
							return (
								<div
									key={index}
									className={`${classNameBase}__entry`}
									dangerouslySetInnerHTML={{
										__html: category,
									}}
								/>
							);
						}
					)}
				</div>
			</div>
		);
	},
});
