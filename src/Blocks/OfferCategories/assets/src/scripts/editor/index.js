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
				<div
					className={`${classNameBase}__entries ${classNameBase}__entries--maincategory`}
				>
					{['Main category', 'Main category'].map(
						(category, index) => {
							return (
								<span
									key={index}
									className={`${classNameBase}_entry ${classNameBase}_entry--maincategory`}
									dangerouslySetInnerHTML={{
										__html: category,
									}}
								/>
							);
						}
					)}
				</div>
				<div
					className={`${classNameBase}__entries ${classNameBase}__entries--subcategory`}
				>
					{[
						'Sub-category',
						'Sub-category',
						'Sub-category',
						'Sub-category',
					].map((category, index) => {
						return (
							<span
								key={index}
								className={`${classNameBase}_entry ${classNameBase}_entry--subcategory`}
								dangerouslySetInnerHTML={{
									__html: category,
								}}
							/>
						);
					})}
				</div>
			</div>
		);
	},
});
