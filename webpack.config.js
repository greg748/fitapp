// entry point
// output final bundle file
// import and export auto included

//this is a node script
const path = require('path');

module.exports = {
  entry: './src/app.js',
  output: {
    path: path.join(__dirname,'js'),
    filename: 'bundle.js'
  },
  module: {
    rules: [{
      loader: 'babel-loader',
      test: /\.js$/,
      exclude: /node_modules/
    }]
  },
  devtool: 'cheap-module-eval-source-map',
  devServer: {
    contentBase: path.join(__dirname,'workouts')
  }
};
