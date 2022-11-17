import { registerBlockType } from '@wordpress/blocks';

import metadata from '../../../../block.json';
import icon from './icon';

registerBlockType(metadata.name, {
	icon,
});
