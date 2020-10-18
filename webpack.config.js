var Encore = require('@symfony/webpack-encore');
// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath(Encore.isProduction() ? '/build' : '/visualizer5/public/build')
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    // only needed for CDN's or sub-directory deploy
    .setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .createSharedEntry('layout', './assets/js/layout.js')
    //.addEntry('layout', './assets/js/layout.js')
    //.addEntry('login', './assets/js/login.js')
    .addEntry('main', './assets/js/main.js')
    .addEntry('login', './assets/js/login.js')
    .addEntry('coverage', './assets/js/coverage.js')
    .addEntry('catchup', './assets/js/catchup.js')
    .addEntry('cluster-filter', './assets/js/cluster_filter.js')
    .addEntry('ccs-sm-filter', './assets/js/ccs_sm_filter.js')
    .addEntry('bphs-plus', './assets/js/bphs_plus.js')
    .addEntry('ref_committee', './assets/js/ref_committee.js')
    .addEntry('covid19', './assets/js/covid19.js')
    .addEntry('download', './assets/js/coverage_catchup_roc_download.js')
    .addEntry('fixed-cols-table', './assets/css/fixed-cols-table.css')
    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    //.splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    //.enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    //.enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .configureBabel(function (config) {
        //config.presets.push('stage-2');
        config.plugins = [
            '@babel/plugin-proposal-class-properties'
        ]
    })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
    .copyFiles({
        'from': './assets/static',
        'to': 'static/[path][name].[ext]'
    })
;
// var config = Encore.getWebpackConfig();
// config.module.rules[3].options.publicPath = '../';

module.exports = Encore.getWebpackConfig();
