<?php

function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

$source = file_get_contents(__DIR__ . '/../configure.php');

if ($source === false) {
    throw new RuntimeException('Unable to read configure.php.');
}

$start = strpos($source, 'function ask(');
$end = strpos($source, "\nfunction confirm(", $start);

if (($start === false) || ($end === false)) {
    throw new RuntimeException('Unable to locate ask() in configure.php.');
}

$askFunction = substr($source, $start, $end - $start);

$script = <<<'PHP'
namespace ConfigureAskRegression;

const STDOUT = 'stdout';

$writes = [];
$readlineArguments = null;

function fwrite($stream, $string)
{
    global $writes;

    $writes[] = [$stream, $string];

    return strlen($string);
}

function readline(...$arguments)
{
    global $readlineArguments;

    $readlineArguments = $arguments;

    return '';
}
PHP;

$script .= "\n{$askFunction}\n";

$script .= <<<'PHP'
return [
    'result' => ask('Author name', 'Taylor Otwell'),
    'writes' => $writes,
    'readlineArguments' => $readlineArguments,
];
PHP;

$result = eval($script);

assertSameValue('Taylor Otwell', $result['result'], 'ask() should return the default value when readline() returns an empty string.');

assertSameValue([
    ['stdout', "\e[0;32mAuthor name"],
    ['stdout', "\e[0;33m (Taylor Otwell)"],
    ['stdout', "\e[0m: "],
], $result['writes'], 'ask() should print the prompt with fwrite() before calling readline().');

assertSameValue([], $result['readlineArguments'], 'ask() should call readline() without passing ANSI prompt text.');

fwrite(STDOUT, "configure ask regression passed\n");
