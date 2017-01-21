<?php

namespace Core\Bundle\AdminBundle\Twig;

class InstanceofSimpleTest extends \Twig_Extension {
    
    public function getTests() {
        return array(
            new \Twig_SimpleTest('instanceof', array($this, 'isInstanceof')),
        );
    }
    
    public function isInstanceof($object,$class)
    {
        //echo get_class($object) ;exit;
        //echo $class;exit;
        return $object instanceof $class;
    }

    public function getName()
    {
        return 'instanceof';
    }
}
