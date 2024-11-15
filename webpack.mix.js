let mix = require('laravel-mix');

// Imagemin webpack plugin
const ImageMinimizerPlugin = require('image-minimizer-webpack-plugin');
// Copy webpack plugin
const copyWebpackPlugin = require('copy-webpack-plugin');

mix.version();

if (!mix.inProduction()) {
	/* need both of these for sass sourcemaps to work */
	mix.webpackConfig({
		devtool: 'inline-source-map',
	});
	mix.sourceMaps();
}

// Turn off URL processing in CSS.
// If processCssUrls is on Mix goes through your CSS and adds query strings to your URL's for cache busting.
// Mix also copies the referenced images in your CSS to images folder in distributionPath but doesn't do any image optimization.
// Since we have a separate task for image optimization we have to turn this off in Mix.
mix.options({ processCssUrls: false });

mix.setPublicPath('./'); // set because of this issue: https://github.com/JeffreyWay/laravel-mix/issues/1126
mix.js('assets/src/js/omnichannel.js', 'assets/dist/js/omnichannel.js');

mix.sass('assets/src/scss/omnichannel.scss', 'assets/dist/css/omnichannel.css');

// Images
// Mix doesn't have support for image minification out of the box so we have to modify webpack config.
mix.webpackConfig({
	plugins: [
		new copyWebpackPlugin({ // eslint-disable-line new-cap
			patterns: [
				{
					context: 'assets/src/img/',
					from: '**/*.{jpg,jpeg,png,gif,svg}',
					to: 'assets/dist/img',
				},
			],
		}),
		new ImageMinimizerPlugin({
			test: [
				/\.(jpe?g|png|gif)$/i, // Image file extensions.
				/\.svg$/i, // Separate RegEx for SVG.
			],
			minimizer: {
				implementation: ImageMinimizerPlugin.imageminMinify,
				options: {
					plugins: [
						[
							'gifsicle',
							{
								interlaced: true,
							},
						],
						[
							'optipng',
							{
								optimizationLevel: 5,
							},
						],
						'mozjpeg',
						'svgo',
					],
				},
			},
		}),
	],
});

// Fonts
mix.copy('assets/src/font', 'assets/dist/font');
