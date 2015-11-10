<?php
namespace PhpSpec\ZendFramework2\Listener;

use PhpSpec\Event\ExampleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PhpSpec\Event\SpecificationEvent;
use Zend\ServiceManager\ServiceManager;

/**
 * This listener is used to setup the ZF2 application for each spec.
 */
class ZendFramework2Listener implements EventSubscriberInterface
{
    /**
     * @var ServiceManager
     */
    private $serviceLocator;

    public function __construct(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'beforeSpecification' => ['beforeSpecification', 1],
        );
    }


    /**
     * Run the `beforeSpecification` hook.
     *
     * @param  \PhpSpec\Event\SpecificationEvent $event
     * @return void
     */
    public function beforeSpecification(SpecificationEvent $event)
    {
        $spec = $event->getSpecification();
        $refl = $spec->getClassReflection();
    }
}