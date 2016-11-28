<?php
spl_autoload_register(function ($className) {
    $classFilePath = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    require_once($classFilePath);
});

use \Breathalyzer\Breathalyzer as Breathalyzer;
use \Breathalyzer\Exception\BreathalyzerException as BreathalyzerException;

if (!isset($argv[1])) {
    die("You need to set relative file path as parameter\n");
}

$testingPath = $argv[1];

try {
    $breathalyzer = new Breathalyzer($testingPath);
    $totalChangesAmount = $breathalyzer->getSmallerDistanceForString();
} catch (BreathalyzerException $e) {
    die("Exception: {$e->getMessage()}\n");
} catch (\Exception $e) {
    die("Fatal exception: {$e->getMessage()}\n");
}

echo "$totalChangesAmount\n";