let preprocessor = "sass", // Preprocessor (sass, less, styl); 'sass' also work with the Scss syntax in blocks/ folder.
  fileswatch = "php,html,htm,txt,json,md,woff2"; // List of files extensions for watching & hard reload

import pkg from "gulp";
const { gulp, src, dest, parallel, series, watch } = pkg;

import browserSync from "browser-sync";
import bssi from "browsersync-ssi";
import ssi from "ssi";
import webpackStream from "webpack-stream";
import webpack from "webpack";
import TerserPlugin from "terser-webpack-plugin";
import gulpSass from "gulp-sass";
import dartSass from "sass";
import sassglob from "gulp-sass-glob";
const sass = gulpSass(dartSass);

import postCss from "gulp-postcss";
import cssnano from "cssnano";
import autoprefixer from "autoprefixer";
import changed from "gulp-changed";
import concat from "gulp-concat";
import rsync from "gulp-rsync";
import del from "del";

function browsersync() {
  browserSync.init({
    proxy: "https://cwines.local",
    ghostMode: { clicks: false },
    notify: false,
    online: true,
    // tunnel: 'yousutename', // Attempt to use the URL https://yousutename.loca.lt
  });
}

function scripts() {
  return src(["./js/*.js", "!./js/*.min.js"])
    .pipe(
      webpackStream(
        {
          mode: "production",
          performance: { hints: false },

          module: {
            rules: [
              {
                test: /\.m?js$/,
                exclude: /(node_modules)/,
                use: {
                  loader: "babel-loader",
                  options: {
                    presets: ["@babel/preset-env"],
                    plugins: ["babel-plugin-root-import"],
                  },
                },
              },
            ],
          },
          optimization: {
            minimize: true,
            minimizer: [
              new TerserPlugin({
                terserOptions: { format: { comments: false } },
                extractComments: false,
              }),
            ],
          },
        },
        webpack
      )
    )
    .on("error", (err) => {
      this.emit("end");
    })
    .pipe(concat("cw.min.js"))
    .pipe(dest("./js"))
    .pipe(browserSync.stream());
}

function styles() {
  return src([`./styles/${preprocessor}/*.*`, `!./styles/${preprocessor}/_*.*`])
    .pipe(eval(`${preprocessor}glob`)())
    .pipe(eval(preprocessor)({ "include css": true }))
    .pipe(
      postCss([
        autoprefixer({ grid: "autoplace" }),
        cssnano({
          preset: ["default", { discardComments: { removeAll: true } }],
        }),
      ])
    )
    .pipe(concat("cw.min.css"))
    .pipe(dest("./css"))
    .pipe(browserSync.stream());
}

function startwatch() {
  watch(`./styles/${preprocessor}/**/*`, { usePolling: true }, styles);
  watch(["./js/**/*.js", "!./js/**/*.min.js"], { usePolling: true }, scripts);
  watch(`./**/*.{${fileswatch}}`, { usePolling: true }).on(
    "change",
    browserSync.reload
  );
}

export { scripts, styles };
export let assets = series(scripts, styles);
export let build = series(scripts, styles);

export default series(scripts, styles, parallel(browsersync, startwatch));
