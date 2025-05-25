<?php

namespace Stormmore\Framework\Std;

use Random\Randomizer;
use Stormmore\Framework\App;

class Path
{

    public static function make($path)
    {
        
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


    public static function resolve_path_alias(string $templatePath): string
    {
        $configuration = App::getInstance()->getAppConfiguration();
        $appDirectory = $configuration->projectDirectory;
        $aliases = $configuration->aliases;
        if (str_starts_with($templatePath, "@/")) {
            return str_replace("@", $appDirectory, $templatePath);
        } else if (str_starts_with($templatePath, '@')) {
            $firstSeparator = strpos($templatePath, "/");
            if ($firstSeparator) {
                $alias = substr($templatePath, 0, $firstSeparator);
                $path = substr($templatePath, $firstSeparator);
            } else {
                $alias = $templatePath;
                $path = '';
            }
            if (!array_key_exists($alias, $aliases)) { return false;}
            $aliasPath = $aliases[$alias];
            if (str_starts_with($aliasPath, '@')) {
                $aliasPath = Path::resolve_path_alias($aliasPath);
            }

            $templatePath = $aliasPath . $path;
        }

        return $templatePath;
    }
}