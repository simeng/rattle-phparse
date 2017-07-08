module.exports = {
	context: __dirname,
	entry: "./src/app",
	output: {
		filename: 'bundle.js',
		path: __dirname + '/public/build'
	},
	module: {
		loaders: [
			{ test: /\.css$/, loader: "style-loader!css-loader" }
		]
	}
};
