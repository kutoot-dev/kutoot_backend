<?php

use Illuminate\Support\Facades\Route;

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = Route::getRoutes();
$controllers = [];

foreach ($routes as $route) {
    $action = $route->getAction();
    if (isset($action['controller'])) {
        $controller = explode('@', $action['controller'])[0];
        if (str_contains($controller, 'App\Http\Controllers')) {
            $controllers[] = $controller;
        }
    }
}

$controllers = array_unique($controllers);

foreach ($controllers as $controllerClass) {
    try {
        $reflection = new ReflectionClass($controllerClass);
        $fileName = $reflection->getFileName();

        if (!$fileName || !file_exists($fileName)) {
            echo "Skipping $controllerClass (File not found)\n";
            continue;
        }

        $content = file_get_contents($fileName);
        $className = $reflection->getShortName();
        $groupName = preg_replace('/Controller$/', '', $className);
        // Add spaces before capitals for readability
        $groupName = preg_replace('/(?<!^)([A-Z])/', ' $1', $groupName);

        $docComment = $reflection->getDocComment();

        if ($docComment) {
            if (str_contains($docComment, '@group')) {
                echo "Skipping $className (Group already exists)\n";
                continue;
            }

            // Add @group to existing docblock
            $newDocComment = preg_replace('/\*\s*$/m', "* @group $groupName\n ", $docComment);
            $content = str_replace($docComment, $newDocComment, $content);
        } else {
            // Create new docblock before class definition
            $newDocBlock = "/**\n * @group $groupName\n */\n";
            $classPattern = '/class\s+' . $className . '/';
            $content = preg_replace($classPattern, $newDocBlock . 'class ' . $className, $content);
        }

        file_put_contents($fileName, $content);
        echo "Updated $className with group '$groupName'\n";

    } catch (Exception $e) {
        echo "Error processing $controllerClass: " . $e->getMessage() . "\n";
    }
}

echo "Done!\n";
