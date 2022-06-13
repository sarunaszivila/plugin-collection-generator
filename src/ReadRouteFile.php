<?php

namespace PluginCollectionGenerator;

class ReadRouteFile
{
    public function check(string $path): ?ReadRouteFile
    {
        if (!str_contains($path, 'Route')) {
            return null;
        }
        return $this;
    }

    public function route(string $path): array
    {
        $routes = [];
        if ($file = fopen($path, 'rb')) {
            while (!feof($file)) {
                $line = fgets($file);
                if (str_contains($line, 'extends RouteServiceProvider')) {
                    $routes['name'] = explode(' ', $line)[1];
                }

                if (preg_match('/\b(->get|->post|->put|->patch|->delete)\b/', $line)) {
                    $routes['route'][] = $line;
                }
            }
            fclose($file);
        }
        return $routes;
    }
}