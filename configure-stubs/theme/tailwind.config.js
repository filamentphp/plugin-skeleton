const preset = require('./vendor/filament/filament/tailwind.config.preset')

module.exports = {
    presets: [preset],
    content: [
        './vendor/filament/**/*.blade.php'
    ],
}
