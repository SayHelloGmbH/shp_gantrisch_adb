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
					{['Keyword 1', 'Keyword 2', 'Keyword 3'].map(
						(keyword, index) => {
							return (
								<div
									key={index}
									className={`${classNameBase}__entry`}
									dangerouslySetInnerHTML={{
										__html: keyword,
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
