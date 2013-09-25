<?php

/**
 * My Application bootstrap file.
 */
use Nette\Application\Routers\Route;

// Load Nette Framework
require LIBS_DIR . '/Nette/loader.php';


// Configure application
$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setDebugMode($configurator::AUTO);
$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
        ->addDirectory(APP_DIR)
        ->addDirectory(LIBS_DIR)
        ->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();

// Setup router
$container->router[] = new Route('[<lang [a-z]{2}>][/<url>]', array(
    'module' => 'Front',
    'presenter' => 'Homepage',
    'action' => 'default',
    'lang' => 'cs',
    'url' => 'index',
        ));

$container->router[] = new Route('admin/[<lang [a-z]{2}>/]sign[/<action>]', 'Admin:Sign:in', Route::ONE_WAY);

$container->router[] = new Route('admin/[<lang [a-z]{2}>/]<presenter>[/<action>][/<id>]', array(
    'module' => 'Admin',
    'presenter' => 'Asset',
    'action' => 'edit',
    'id' => 1,
        ));

$container->router[] = new Route('map.xml', array(
    'module' => 'Front',
    'presenter' => 'Homepage',
    'lang' => 'cs',
    'action' => 'mapXml',
        ));

$container->router[] = new Route('[<lang [a-z]{2}>][/<url>]', array(
    'module' => 'Front',
    'presenter' => 'Homepage',
    'action' => 'default',
    'url' => 'index',
        ));

// Navigation panel
\Panel\Navigation::register();

// Configure and run the application!
$container->application->run();
