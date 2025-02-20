<?php

namespace Stormmore\Framework\Classes;

class RouteScanner
{
    public function scan(array $classes): array
    {
        $routes = [];
        foreach ($classes as $filePath) {
            $fileRoutes = $this->getClassFileRoutes($filePath);
            $routes = array_merge($routes, $fileRoutes);
        }
        uksort($routes, function ($key1, $key2) {
            $lengthMatch = substr_count($key2, "/") <=> substr_count($key1, "/");
            if ($lengthMatch) {
                return $lengthMatch;
            }
            return $key1 <=> $key2;
        });
        return $routes;
    }

    private function getClassFileRoutes(string $filePath): array
    {
        $tokens = $this->getControllerTokens($filePath);

        $namespace = "";
        if ($tokens[0]->name == "namespace") {
            $namespace = $tokens[0]->value;
        }

        $routes = [];
        $index = 0;
        do
        {
            $controllerPos = $this->findControllerTokenIndex($index, $tokens);
            if ($controllerPos < 0) {
                return $routes;
            }
            $classToken =  array_key_value($tokens, $controllerPos + 1, null);
            if ($classToken?->name != "class" ) {
                return [];
            }
            $className = $classToken->value;
            if (!empty($namespace)) {
                $className = $namespace . "\\" . $className;
            }
            $routes = array_merge($routes, $this->readControllerRouteTokens($controllerPos + 2, $className, $tokens));
            $index = $controllerPos + 1;
        }
        while(true);

        return $routes;
    }

    private function findControllerTokenIndex(int $startIndex, array $tokens): int
    {
        for($i = $startIndex; $i < count($tokens); $i++) {
            if ($tokens[$i]->name == "controller") {
                return $i;
            }
        }
        return -1;
    }

    private function readControllerRouteTokens(int $startIndex, string $className, array $tokens): array
    {
        $routes = [];
        for($i = $startIndex; $i < count($tokens); $i++) {
            $token = $tokens[$i];
            if($token->name == "route") {
                $methodToken = array_key_value($tokens, $i + 1, null);
                if ($methodToken?->name == "function") {
                    $urls = $tokens[$i]->value;
                    foreach(explode(",", $urls) as $url) {
                        $routes[$url] = [$className, $methodToken->value];
                    }
                    $i = $i + 1;
                }
            }
            if ($token->name == 'controller' or $token->name == 'class') {
                return $routes;
            }
        }
        return $routes;
    }

    private function getControllerTokens(string $filePath): array
    {
        $controllerTokens = [];
        $allTokens = PhpToken::tokenize(file_get_contents($filePath));
        foreach ($allTokens as $i => $token) {
            if ($token->text == 'namespace') {
                $controllerTokens[] = new Token('namespace', $allTokens[$i + 2]);
            }
            if ($token->text == '#[') {
                $attributeNameToken = $allTokens[$i + 1];
                if ($attributeNameToken->text == 'Controller') {
                    $controllerTokens[] = new Token('controller', '');
                }
                if ($attributeNameToken->text == 'Route') {
                    $urls = $this->readUrls($allTokens, $i);
                    $controllerTokens[] = new Token('route', $urls);
                }
            }
            if ($token->text == 'function') {
                $accessibility = $allTokens[$i - 2];
                if ($accessibility->text != 'private' and $accessibility->text != 'protected') {
                    $controllerTokens[] = new Token('function', $allTokens[$i + 2]);
                }
            }
            if ($token->text == 'class') {
                $controllerTokens[] = new Token('class', $allTokens[$i + 2]);
            }
        }
        return $controllerTokens;
    }

    private function readUrls(array $tokens, int $index): string
    {
        $ii = $index + 3;
        $urls = "";
        while($tokens[$ii]->id != 41) {
            $urls .= trim($tokens[$ii]->text);
            $ii++;
        }
        return str_replace(array('"', "'"), '', $urls);
    }
}