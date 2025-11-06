const mix = require('laravel-mix');

mix.babelConfig({
	presets: ['@babel/preset-env', '@babel/preset-react'],
});

mix.sass('assets-src/map-pickup-point-block/style.scss', 'assets/dist/css/map-pickup-point-block.css');
mix.js('assets-src/index.js', 'assets/dist/js/index.js');

mix.webpackConfig({
	resolve: {
		extensions: ['.js', '.jsx'],
	},
});

mix.override((webpackConfig) => {
	webpackConfig.module.rules.push({
		test: /\.jsx?$/,
		include: (filePath) =>
			filePath.includes('assets-src') ||
			filePath.includes('node_modules/@octolize/flexible-shipping-points-map'),
		use: {
			loader: 'babel-loader',
			options: {
				presets: ['@babel/preset-env', '@babel/preset-react'],
			},
		},
	});
});
