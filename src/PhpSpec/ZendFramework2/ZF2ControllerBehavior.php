<?php

/**
,,
`""*3b..											
     ""*3o.					  						11/9/15 10:40 PM
         "33o.			                  			S. Alexandre M. Lemaire
           "*33o.                                 	
              "333o.								Redistribution of these files is illegal.
                "3333bo...       ..o:
                  "33333333booocS333    ..    ,.
               ".    "*3333SP     V3o..o33. .333b
                "33o. .33333o. ...A33333333333333b
          ""bo.   "*33333333333333333333P*33333333:
             "33.    V333333333P"**""*"'   VP  * "l
               "333o.433333333X
                "*3333333333333AoA3o..oooooo..           .b
                       .X33333333333P""     ""*oo,,     ,3P
                      33P""V3333333:    .        ""*****"
                    .*"    A33333333o.4;      .
                         .oP""   "333333b.  .3;
                                  A3333333333P
                                  "  "33333P"
                                      33P*"
		                              .3"
                                     "
                                     
                                     
*/

namespace PhpSpec\ZendFramework2;

class ZF2ControllerBehavior extends ZF2ObjectBehavior
{
    protected function setup(){
        $this->getWrappedObject()->setServiceLocator( $this->getServiceLocator() );
        $this->getWrappedObject()->setPluginManager( $this->getServiceLocator()->get('ControllerPluginManager'));
    }
}