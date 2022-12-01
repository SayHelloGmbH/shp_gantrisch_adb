import gulp from 'gulp';

import gulpWebpack from 'webpack-stream';
import rename from 'gulp-rename';
import uglify from 'gulp-uglify';
import fs from 'fs';

const getDirectories = (path) =>
	fs
		.readdirSync(path)
		.filter((file) => fs.statSync(path + '/' + file).isDirectory());

export const task = (config) => {
	return new Promise((resolve) => {
		const bundles = getDirectories(`${config.buildSrc}/scripts/`);
		const entry = {};

		bundles.forEach((bundle) => {
			const filePath = `${config.buildSrc}/scripts/${bundle}/index.js`;
			if (fs.existsSync(filePath)) {
				entry[bundle] = './' + filePath;
			}
		});

		gulp.src([`${config.buildSrc}/scripts/*`])
			.pipe(
				gulpWebpack({
					entry,
					mode: 'production',
					module: {
						rules: [
							{
								test: /\.js$/,
								exclude: /node_modules/,
								loader: 'babel-loader',
							},
							{
								test: /\.css$/i,
								exclude: /node_modules/,
								use: [
									{
										loader: 'style-loader',
									},
									{
										loader: 'css-loader',
									},
								],
							},
							{
								test: /\.scss$/i,
								exclude: /node_modules/,
								use: [
									{
										loader: 'style-loader',
									},
									{
										loader: 'css-loader',
									},
									{
										loader: 'sass-loader',
									},
								],
							},
						],
					},
					output: {
						filename: '[name].js',
					},
					externals: {
						jquery: 'jQuery',
					},
				})
			)
			.on('error', config.errorLog)
			.pipe(gulp.dest(config.assetsDir + 'scripts/'))

			// Minify
			.pipe(uglify())
			.pipe(
				rename({
					suffix: '.min',
				})
			)
			.on('error', config.errorLog)
			.pipe(gulp.dest(config.assetsDir + 'scripts/'));
		resolve();
	});
};
