<?php

use PluginCollectionGenerator\ReadRouteFile;
use PluginCollectionGenerator\RecursiveScan;
use PluginCollectionGenerator\RouteConstructor;

require_once 'src/RecursiveScan.php';
require_once 'src/ReadRouteFile.php';
require_once 'src/RouteConstructor.php';

/**
 * argv 1 SRC folder
 * argv 2 NAMESPACE of plugin
 */
$scan = RecursiveScan::scan(@$argv[1]);
$read = new ReadRouteFile();

$routes = [];
foreach ($scan as $path) {
    $route = $read->check($path)?->route($path);
    if (is_array($route)) {
        $routes[] = $route;
    }
}
$constructor = new RouteConstructor(@$argv[2]);
$collection = $constructor->getCollection($routes);
file_put_contents('plugin.postman_collection.json', json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

