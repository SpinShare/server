<?php
header('Content-Type: text/plain; charset=UTF-8');

spl_autoload_register(function ($class) {
    $prefix = 'SpinShare\\SpeenOrbitalOS\\';
    $base_dir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use SpinShare\SpeenOrbitalOS\Commands\CommandFactory;

$authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
$isAuthorized = $authorization === "star_blazer spinsofast";

$input = $_POST['input'] ?? '';
if (!$input) {
    return;
}

$parts = explode(' ', $input);
$commandName = $parts[0];
$args = array_slice($parts, 1);

$factory = new CommandFactory();
$command = $factory->getCommand($commandName);

if (!$command || ($command->isAuthorizedOnly() && !$isAuthorized)) {
    echo "$commandName: command not found. Type 'help' for a list of available commands.";
    return;
}

try {
    echo $command->execute($args, $isAuthorized);
} catch (Exception $e) {
    echo "§F44Error executing command: " . $e->getMessage();
}
