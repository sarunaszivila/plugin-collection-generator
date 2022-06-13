<?php

namespace PluginCollectionGenerator;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

class RecursiveScan
{
    public static function scan(?string $source): ?array
    {
        if (!$source) {
            throw new RuntimeException('No src folder argv 1.');
        }
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source));
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }
            $files[] = $file->getPathname();
        }
        return $files;
    }
}