#!/usr/bin/env php
<?php

$gitName = run('git config user.name');
$authorName = ask('Author name', $gitName);

$gitEmail = run('git config user.email');
$authorEmail = ask('Author email', $gitEmail);

$usernameGuess = explode(':', run('git config remote.origin.url'))[1] ?? '';
if ($usernameGuess !== '') {
    $usernameGuess = dirname($usernameGuess);
    $usernameGuess = basename($usernameGuess);
}
$authorUsername = ask('Author username', $usernameGuess);

$vendorName = ask('Vendor name', $authorUsername);
$vendorSlug = slugify($vendorName);
$vendorNamespace = ucwords($vendorName);
$vendorNamespace = ask('Vendor namespace', $vendorNamespace);

$folderName = basename(getcwd());

$packageName = ask('Package name', $folderName);
$packageSlug = slugify($packageName);
$packageSlugWithoutPrefix = remove_prefix('filament-', $packageSlug);

$className = title_case($packageName);
$className = ask('Class name', $className);
$description = ask('Package description');

$usePhpStan = confirm('Enable PhpStan?', true);
$usePint = confirm('Enable Pint?', true);
$useDependabot = confirm('Enable Dependabot?', true);
$useLaravelRay = confirm('Use Ray for debugging?', true);
$useUpdateChangelogWorkflow = confirm('Use automatic changelog updater workflow?', true);

$isTheme = confirm('Is this a custom theme?');
$formsOnly = ! $isTheme && confirm('Is this for Forms only?');
$tablesOnly = ! ($isTheme || $formsOnly) && confirm('Is this for Tables only?');

writeln("\r");
writeln('------');
writeln("Author     : \e[0;36m{$authorName} ({$authorUsername}, {$authorEmail})\e[0m");
writeln("Vendor     : \e[0;36m{$vendorName} ({$vendorSlug})\e[0m");
writeln('Package    : '."\e[0;36m".$packageSlug.($description ? ' <{$description}>' : '')."\e[0m");
writeln("Namespace  : \e[0;36m{$vendorNamespace}\\{$className}\e[0m");
writeln("Class name : \e[0;36m{$className}\e[0m");
writeln('---');
writeln("\e[1;37mPackages & Utilities\e[0m");
writeln('Larastan/PhpStan  : '.($usePhpStan ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
writeln('Pint              : '.($usePint ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
writeln('Use Dependabot    : '.($useDependabot ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
writeln('Use Ray App       : '.($useLaravelRay ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
writeln('Auto-Changelog    : '.($useUpdateChangelogWorkflow ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
if ($formsOnly) {
    writeln("Filament/Forms    : \e[0;32mYes\e[0m");
} elseif ($tablesOnly) {
    writeln("Filament/Tables   : \e[0;32mYes\e[0m");
} else {
    writeln("Filament/Filament : \e[0;32mYes\e[0m");
}
writeln('------');
writeln("\r");
writeln('This script will replace the above values in all relevant files in the project directory.');
writeln("\r");

if (! confirm('Modify files?', true)) {
    exit(1);
}

if ($formsOnly) {
    safeUnlink(__DIR__.'/src/SkeletonTheme.php');

    remove_composer_filament_deps([
        'filament/filament',
        'filament/tables',
    ]);
} elseif ($tablesOnly) {
    safeUnlink(__DIR__.'/src/SkeletonTheme.php');

    remove_composer_filament_deps([
        'filament/filament',
        'filament/forms',
    ]);
} else {
    if ($isTheme) {
        safeUnlink(__DIR__.'/src/SkeletonServiceProvider.php');
        safeUnlink(__DIR__.'/src/Skeleton.php');
        removeDirectory(__DIR__.'/config');
        removeDirectory(__DIR__.'/database');
        removeDirectory(__DIR__.'/stubs');
        removeDirectory(__DIR__.'/resources/js');
        removeDirectory(__DIR__.'/resources/lang');
        removeDirectory(__DIR__.'/resources/views');
        removeDirectory(__DIR__.'/src/Commands');
        removeDirectory(__DIR__.'/src/Facades');
        removeDirectory(__DIR__.'/src/Testing');
    } else {
        safeUnlink(__DIR__.'/src/SkeletonTheme.php');
    }

    remove_composer_filament_deps([
        'filament/forms',
        'filament/tables',
    ]);
}

if ($isTheme) {
    copy(__DIR__.'/configure-stubs/theme/package.json', __DIR__.'/package.json');
    copy(__DIR__.'/configure-stubs/theme/plugin.css', __DIR__.'/resources/css/plugin.css');
    copy(__DIR__.'/configure-stubs/theme/tailwind.config.js', __DIR__.'/tailwind.config.js');
} else {
    copy(__DIR__.'/configure-stubs/package/package.json', __DIR__.'/package.json');
    copy(__DIR__.'/configure-stubs/package/plugin.css', __DIR__.'/resources/css/plugin.css');
    copy(__DIR__.'/configure-stubs/package/tailwind.config.js', __DIR__.'/tailwind.config.js');
}

$files = (str_starts_with(strtoupper(PHP_OS), 'WIN') ? replaceForWindows() : replaceForAllOtherOSes());

foreach ($files as $file) {
    replace_in_file($file, [
        ':author_name' => $authorName,
        ':author_username' => $authorUsername,
        'author@domain.com' => $authorEmail,
        ':vendor_name' => $vendorName,
        ':vendor_slug' => $vendorSlug,
        'VendorName' => $vendorNamespace,
        ':package_name' => $packageName,
        ':package_slug' => $packageSlug,
        ':package_slug_without_prefix' => $packageSlugWithoutPrefix,
        'Skeleton' => $className,
        'skeleton' => $packageSlug,
        ':package_description' => $description,
    ]);

    match (true) {
        str_contains($file, determineSeparator('src/Skeleton.php')) => rename($file, determineSeparator('./src/'.$className.'.php')),
        str_contains($file, determineSeparator('src/SkeletonServiceProvider.php')) => rename($file, determineSeparator('./src/'.$className.'ServiceProvider.php')),
        str_contains($file, determineSeparator('src/SkeletonTheme.php')) => rename($file, determineSeparator('./src/'.$className.'Theme.php')),
        str_contains($file, determineSeparator('src/Facades/Skeleton.php')) => rename($file, determineSeparator('./src/Facades/'.$className.'.php')),
        str_contains($file, determineSeparator('src/Commands/SkeletonCommand.php')) => rename($file, determineSeparator('./src/Commands/'.$className.'Command.php')),
        str_contains($file, determineSeparator('src/Testing/TestsSkeleton.php')) => rename($file, determineSeparator('./src/Testing/Tests'.$className.'.php')),
        str_contains($file, determineSeparator('database/migrations/create_skeleton_table.php.stub')) => rename($file, determineSeparator('./database/migrations/create_'.$packageSlugWithoutPrefix.'_table.php.stub')),
        str_contains($file, determineSeparator('config/skeleton.php')) => rename($file, determineSeparator('./config/'.$packageSlugWithoutPrefix.'.php')),
        str_contains($file, 'README.md') => remove_tag($file, 'delete'),
        default => [],
    };
}

if (! $useDependabot) {
    safeUnlink(__DIR__.'/.github/dependabot.yml');
    safeUnlink(__DIR__.'/.github/workflows/dependabot-auto-merge.yml');
}

if (! $useLaravelRay) {
    remove_composer_deps(['spatie/laravel-ray']);
}

if (! $usePhpStan) {
    safeUnlink(__DIR__.'/phpstan.neon.dist');
    safeUnlink(__DIR__.'/phpstan-baseline.neon');
    safeUnlink(__DIR__.'/.github/workflows/phpstan.yml');

    remove_composer_deps([
        'phpstan/extension-installer',
        'phpstan/phpstan-deprecation-rules',
        'phpstan/phpstan-phpunit',
        'nunomaduro/larastan',
    ]);

    remove_composer_script([
        'test:phpstan',
        '@test:phpstan',
    ]);
}

if (! $usePint) {
    safeUnlink(__DIR__.'/.github/workflows/fix-php-code-style-issues.yml');

    remove_composer_deps([
        'laravel/pint',
    ]);

    remove_composer_script(['pint']);
}

if (! $useUpdateChangelogWorkflow) {
    safeUnlink(__DIR__.'/.github/workflows/update-changelog.yml');
}

confirm('Execute `composer install` and run tests?') && run('composer install && composer test');

if (confirm('Let this script delete itself?', true)) {
    removeDirectory(__DIR__.'/configure-stubs');
    unlink(__FILE__);
}

function ask(string $question, string $default = ''): string
{
    $def = $default ? "\e[0;33m ({$default})" : '';
    $answer = readline("\e[0;32m".$question.$def.": \e[0m");

    if (! $answer) {
        return $default;
    }

    return $answer;
}

function confirm(string $question, bool $default = false): bool
{
    $answer = ask($question, ($default ? 'Y/n' : 'y/N'));

    if (strtolower($answer) === 'y/n') {
        return $default;
    }

    return strtolower($answer) === 'y';
}

function writeln(string $line): void
{
    echo $line.PHP_EOL;
}

function run(string $command): string
{
    return trim(shell_exec($command));
}

function slugify(string $subject): string
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $subject), '-'));
}

function title_case(string $subject): string
{
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $subject)));
}

function replace_in_file(string $file, array $replacements): void
{
    $contents = file_get_contents($file);

    file_put_contents(
        $file,
        str_replace(
            array_keys($replacements),
            array_values($replacements),
            $contents
        )
    );
}

function remove_prefix(string $prefix, string $content): string
{
    if (str_starts_with($content, $prefix)) {
        return substr($content, strlen($prefix));
    }

    return $content;
}

function remove_composer_deps(array $names): void
{
    $data = json_decode(file_get_contents(__DIR__.'/composer.json'), true);

    foreach ($data['require-dev'] as $name => $version) {
        if (in_array($name, $names, true)) {
            unset($data['require-dev'][$name]);
        }
    }

    file_put_contents(__DIR__.'/composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function remove_composer_filament_deps(array $names): void
{
    $data = json_decode(file_get_contents(__DIR__.'/composer.json'), true);

    foreach ($data['require'] as $name => $version) {
        if (in_array($name, $names, true)) {
            unset($data['require'][$name]);
        }
    }

    file_put_contents(__DIR__.'/composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function remove_composer_script(array $scriptNames): void
{
    $data = json_decode(file_get_contents(__DIR__.'/composer.json'), true);

    foreach ($data['scripts'] as $name => $script) {
        if (is_array($script)) {
            foreach ($script as $k => $s) {
                if (in_array($s, $scriptNames)) {
                    unset($data['scripts'][$name][$k]);

                    break;
                }
            }
        } elseif (in_array($name, $scriptNames)) {
            unset($data['scripts'][$name]);

            break;
        }
    }

    file_put_contents(__DIR__.'/composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function remove_package_script(array $scriptNames): void
{
    $data = json_decode(file_get_contents(__DIR__.'/package.json'), true);

    foreach ($data['scripts'] as $name => $script) {
        if (is_array($script)) {
            foreach ($script as $k => $s) {
                if (in_array($s, $scriptNames)) {
                    unset($data['scripts'][$name][$k]);

                    break;
                }
            }
        } elseif (in_array($name, $scriptNames)) {
            unset($data['scripts'][$name]);

            break;
        }
    }

    foreach ($data['devDependencies'] as $name => $script) {
        if (is_array($script)) {
            foreach ($script as $k => $s) {
                if (in_array($s, $scriptNames)) {
                    unset($data['devDependencies'][$name][$k]);

                    break;
                }
            }
        } elseif (in_array($name, $scriptNames)) {
            unset($data['devDependencies'][$name]);

            break;
        }
    }

    file_put_contents(__DIR__.'/package.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES |
    JSON_UNESCAPED_UNICODE));
}

function remove_tag(string $file, string $tag): void
{
    $contents = file_get_contents($file);

    file_put_contents(
        $file,
        preg_replace('/<!--'.$tag.'-->.*<!--\/'.$tag.'-->/s', '', $contents) ?: $contents
    );
}

function safeUnlink(string $filename): void
{
    if (file_exists($filename) && is_file($filename)) {
        unlink($filename);
    }
}

function determineSeparator(string $path): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function replaceForWindows(): array
{
    return preg_split('/\\r\\n|\\r|\\n/', run('dir /S /B * | findstr /v /i .git\ | findstr /v /i vendor | findstr /v /i '.basename(__FILE__).' | findstr /r /i /M /F:/ ":author :vendor :package VendorName skeleton vendor_name vendor_slug author@domain.com"'));
}

function replaceForAllOtherOSes(): array
{
    return explode(PHP_EOL, run('grep -E -r -l -i ":author|:vendor|:package|VendorName|skeleton|vendor_name|vendor_slug|author@domain.com" --exclude-dir=vendor ./* ./.github/* | grep -v '.basename(__FILE__)));
}

function removeDirectory($dir): void
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != '.' && $object != '..') {
                if (filetype($dir.'/'.$object) == 'dir') {
                    removeDirectory($dir.'/'.$object);
                } else {
                    unlink($dir.'/'.$object);
                }
            }
        }
        rmdir($dir);
    }
}
