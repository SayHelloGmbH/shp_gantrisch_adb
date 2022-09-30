import gulp from 'gulp';

const config = {
	name: 'WordPress plugin with blocks',
	key: 'shp_gantrich:outdooractive',
	assetsDir: 'assets/',
	gulpDir: './.build/gulp/',
	assetsBuild: '.build/assets/',
	blockScriptsSrc: './src/Blocks/**/assets/src/scripts',
	blockScriptsDist: './src/Blocks/',
	blockStylesSrc: './src/Blocks/**/assets/src/styles/**/*.{scss,js}',
	blockStylesDist: './src/Blocks/',
	errorLog: function (error) {
		console.log('\x1b[31m%s\x1b[0m', error);
		if (this.emit) {
			this.emit('end');
		}
	},
	reload: [
		'*.php',
		'{Classes,inc,partials,templates,includes}/**/*.{php,html,twig}',
	],
};

import { task as taskBlockScripts } from './.build/gulp/task-block-scripts';
import { task as taskBlockStyles } from './.build/gulp/task-block-styles';

export const block_scripts = () => taskBlockScripts(config);
export const block_styles = () => taskBlockStyles(config);

export const watch = () => {
	const settings = { usePolling: true, interval: 100 };

	gulp.watch(config.blockStylesSrc, settings, gulp.series(block_styles));

	gulp.watch(
		`${config.blockScriptsSrc}/**/*.{scss,js}`,
		settings,
		gulp.series(block_scripts)
	);
};

export const taskDefault = gulp.series(watch);

export default taskDefault;
