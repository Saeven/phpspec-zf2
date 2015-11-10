<?php

namespace PhpSpec\ZendFramework2\Runner\Maintainer;

use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\SpecificationInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * This maintainer is used to bind the ZF2 wrapper to nodes that implement
 * the `setServiceLocator` method.
 */
class ServiceManagerMaintainer implements MaintainerInterface
{
    /**
     * ZF2 Service Manager
     */
    private $serviceLocator;

    public function __construct(ServiceManager $sm)
    {
        $this->serviceLocator = $sm;
    }

    /**
     * Check if this maintainer applies to the given node.
     *
     * Will check for the `setServiceLocator` method.
     *
     * @param  \PhpSpec\Loader\Node\ExampleNode $example
     * @return boolean
     */
    public function supports(ExampleNode $example)
    {
        return
            $example
                ->getSpecification()
                ->getClassReflection()
                ->hasMethod('setServiceLocator');
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(ExampleNode $example, SpecificationInterface $context,
                            MatcherManager $matchers, CollaboratorManager $collaborators)
    {
        $reflection =
            $example
                ->getSpecification()
                ->getClassReflection()
                ->getMethod('setServiceLocator');

        $reflection->invokeArgs($context, array($this->serviceLocator));
    }

    /**
     * {@inheritdoc}
     */
    public function teardown(ExampleNode $example, SpecificationInterface $context,
                             MatcherManager $matchers, CollaboratorManager $collaborators)
    {

    }

    /**
     * Give this maintainer a high priority in the stack to ensure that ZF2
     * is bootstrapped early.
     *
     * @return int
     */
    public function getPriority()
    {
        return 1000;
    }
}
