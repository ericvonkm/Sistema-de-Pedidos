<?php
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';
require_once '../app/core/Database.php';
require_once '../app/config/config.php';

// Autoloader simple
spl_autoload_register(function($className) {
    $paths = [
        '../app/controllers/',
        '../app/models/',
        '../app/core/',
        '../app/helpers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Inicializar aplicación
$app = new App();

