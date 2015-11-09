<?php

namespace PhpSpec\ZendFramework2\Extension;

use PhpSpec\ServiceContainer;
use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ZendFramework2\Listener\ZendFramework2Listener;
use PhpSpec\ZendFramework2\Runner\Maintainer\ControllerMaintainer;
use PhpSpec\ZendFramework2\Runner\Maintainer\ServiceManagerMaintainer;
use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use PhpSpec\ZendFramework2\Runner\Maintainer\ZendFramework2Maintainer;

/**
 * Setup the ZF2 extension.
 *
 * Bootstraps ZF2 and sets up some objects in the Container.
 */
class ZendFramework2Extension implements ExtensionInterface
{
    /**
     * Setup the ZF2 extension.
     *
     * @param  \PhpSpec\ServiceContainer $container
     * @return void
     */
    public function load(ServiceContainer $container)
    {
        chdir(__DIR__);

        self::chroot();
        self::initAutoloader();

        $zf2ModulePaths = array(dirname(dirname(__DIR__)));

        if (($path = static::findParentPath('vendor'))) {
            $zf2ModulePaths[] = $path;
        }
        if (($path = static::findParentPath('module')) !== $zf2ModulePaths[0]) {
            $zf2ModulePaths[] = $path;
        }

        $app_config = require getcwd() . '/config/application.config.php';

        // use ModuleManager to load this module and it's dependencies
        $config = [
            'module_listener_options' => [
		        'config_glob_paths'    => [
		           getcwd() .'/config/autoload/{,*.}{global,local}.php',
		        ],
		        'module_paths' => [
		            getcwd() . '/module',
		            getcwd() . '/vendor',
		        ],
		    ],
            'modules' => $app_config['modules'],
        ];

        $container->setShared(
            'zf2',
            function ($c) use( $config ){
                $serviceManager = new ServiceManager(new ServiceManagerConfig());
                $serviceManager->setService('ApplicationConfig', $config);
                $serviceManager->get('ModuleManager')->loadModules();
                return $serviceManager;
            }
        );

        $container->setShared(
            'runner.maintainers.zf2sm',
            function ($c) {
                return new ServiceManagerMaintainer(
                    $c->get('zf2')
                );
            }
        );

        $container->setShared(
            'event_dispatcher.listeners.zf2',
            function ($c) {
                return new ZendFramework2Listener($c->get('zf2'));
            }
        );
    }

    public static function chroot()
    {
        $rootPath = dirname(static::findParentPath('module'));
        chdir($rootPath);
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (file_exists($vendorPath . '/autoload.php')) {
            include $vendorPath . '/autoload.php';
        }

        include $vendorPath . '/zendframework/zend-loader/src/AutoloaderFactory.php';
        AutoloaderFactory::factory([
            'Zend\Loader\StandardAutoloader' => [
                'autoregister_zf' => true,
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
                ],
            ],
        ]);
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}