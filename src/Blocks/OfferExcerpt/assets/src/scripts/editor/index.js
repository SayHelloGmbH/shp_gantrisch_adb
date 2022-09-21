import { registerBlockType } from '@wordpress/blocks';

import { _x } from '@wordpress/i18n';
import block_json from '../../../../block.json';
const { name: block_name } = block_json;

import icon from './icon';
import edit from './_edit';

registerBlockType(block_name, {
	icon,
	edit,
});
