<?php

namespace Stormmore\Framework\Std;

use Random\Randomizer;
use Stormmore\Framework\App;

class Path
{

    public static function make($path)
    {
        
    }

    public static function getRootPath(string $path, string $root): null|string
    {
        $lastOccuranceOfRoot = strrpos($path, $root);
        if ($lastOccuranceOfRoot === false) {
            return null;
        }
        return substr($path, 0, $lastOccuranceOfRoot + strlen($root));
    }

    /**
     * @param int $length length with or without extension. Default 64. Optional.
     * @param string $extension file extension. Optional.
     * @param string $directory to check whether unique file exist or not. Optional
     * @return string generated unique file name
     */
    public static function gen_unique_file_name(int $length = 64, string $extension = '', string $directory = ''): string
    {
        $filename = '';
        if (!empty($extension)) {
            $length = $length - strlen($extension) - 1;
        }
        do {
            $randomizer = new Randomizer();
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
            $charactersLength = strlen($characters);
            for ($i = 0; $i < $length; $i++) {
                $filename .= $characters[$randomizer->getInt(0, $charactersLength - 1)];
            }
            if (!empty($extension)) {
                $filename .= '.' . $extension;
            }
        } while (!empty($directory) and file_exists($directory . "/" . $filename));

        return $filename;
    }

    public static function concatenate_paths(string ...$paths): string
    {
        $path = '';
        for ($i = 0; $i < count($paths); $i++) {
            $element = $paths[$i];
            if ($i < count($paths) - 1 and !str_ends_with($element, "/")) {
                $element .= "/";
            }
            if (str_ends_with($path, "/") and str_starts_with($element, "/")) {
                $element = substr($element, 1);
            }
            $path .= $element;
        }
        return $path;
    }

    public static function isAlias(string $path): bool
    {
        return str_starts_with($path, "@");
    }

    public static function resolve_alias(string $pathAlias): string
    {
        $configuration = App::getInstance()->getAppConfiguration();
        $appDirectory = $configuration->projectDirectory;
        $aliases = $configuration->aliases;
        if (str_starts_with($pathAlias, "@/")) {
            return str_replace("@", $appDirectory, $pathAlias);
        } else if (str_starts_with($pathAlias, '@')) {
            $firstSeparator = strpos($pathAlias, "/");
            if ($firstSeparator) {
                $alias = substr($pathAlias, 0, $firstSeparator);
                $path = substr($pathAlias, $firstSeparator);
            } else {
                $alias = $pathAlias;
                $path = '';
            }
            if (!array_key_exists($alias, $aliases)) { return false;}
            $aliasPath = $aliases[$alias];
            if (str_starts_with($aliasPath, '@')) {
                $aliasPath = Path::resolve_alias($aliasPath);
            }

            $pathAlias = $aliasPath . $path;
        }

        return $pathAlias;
    }
}