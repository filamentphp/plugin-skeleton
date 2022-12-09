const preset = require('./vendor/filament/filament/tailwind.config.preset')

module.exports = {
    presets: [preset],
    content: ['./resources/**/*.{blade.php,js}'],
    corePlugins: {
        preflight: false,
    },
}
