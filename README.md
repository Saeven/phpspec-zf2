# phpspec-zf2

Zend Framework 2 extension for the excellent PHPSPEC BDD test system.
Come chat about it with me here http://circlical.com/blog/2015/11/23/zend-framework-2-and-phpspec

## Why?

I wanted to test behavior in factories and objects that required the ZF2 ServiceManager.  It's pretty difficult to escape when using Zend Framework 2.

## Configuration

First, install it using composer:

`composer require saeven/phpspec-zf2`

Then, configure your phpspec.yml file.  I put mine in the base folder, right next to composer and a symlink to phpspec in the bin folder:

**phpspec.yml**

```
  suites:
      Application:
          namespace: Application
          spec_prefix: Spec
          src_path: module/Application/src/
          spec_path: module/Application/bundle
      CirclicalUser:
          namespace: CirclicalUser
          spec_prefix: Spec
          src_path: module/CirclicalUser/src/
          spec_path: module/CirclicalUser/bundle
  extensions:
    - PhpSpec\ZendFramework2\Extension\ZendFramework2Extension
```

Separately...
`ln -s ./vendor/bin/phpspec ./phpspec`


Then, describe your object, and make its specification extend PhpSpec\ZendFramework2\ZF2ObjectBehavior instead of ObjectBehavior. 

## Example Spec

I have a Form factory for example, and wanted to test its implementation of MutableCreationOptionsInterface.  Based on whether or not I give it a 'captcha' parameter of true, it should, or shouldn't have a captcha element in the form it returns.  I test that with this specification:

```
<?php

namespace Spec\CirclicalUser\Factory\Form;

use CirclicalUser\InputFilter\UserInputFilter;
use PhpSpec\ZendFramework2\ZF2ObjectBehavior;
use Prophecy\Argument;

class UserFormFactorySpec extends ZF2ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('CirclicalUser\Factory\Form\UserFormFactory');
    }

    function its_factory_works()
    {
        $this->createService( $this->getServiceLocator()->get('FormElementManager') )->shouldBeAnInstanceOf( \CirclicalUser\Form\UserForm::class );
    }

    function it_can_create_forms_without_captcha()
    {
        $this->setCreationOptions(['captcha' => false, 'country' => 'US' ]);
        $ret = $this->createService( $this->getServiceLocator()->get('FormElementManager') );
        $ret->shouldBeAnInstanceOf( \CirclicalUser\Form\UserForm::class );
        $ret->getInputFilter()->shouldBeAnInstanceOf( UserInputFilter::class );
        $ret->getInputFilter()->shouldThrow( \Zend\InputFilter\Exception\InvalidArgumentException::class )->during( 'get', ['g-recaptcha-response'] );
    }

    function it_can_create_forms_with_captcha()
    {
        $this->setCreationOptions(['captcha' => true, 'country' => 'US' ]);
        $ret = $this->createService( $this->getServiceLocator()->get('FormElementManager') );
        $ret->shouldBeAnInstanceOf( \CirclicalUser\Form\UserForm::class );
        $ret->getInputFilter()->shouldBeAnInstanceOf( UserInputFilter::class );
        $ret->getInputFilter()->get('g-recaptcha-response')->shouldBeAnInstanceOf( \Zend\InputFilter\Input::class );
    }
}
```

The glue this system provides, is that it loads the SM into your test object -- as per how it was configured in your application.
