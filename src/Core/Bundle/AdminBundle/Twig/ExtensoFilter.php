<?php

namespace Core\Bundle\AdminBundle\Twig;

class ExtensoFilter extends \Twig_Extension {
    
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('extenso', array($this, 'priceFilter')),
        );
    }

    public function priceFilter($valor, $bolExibirMoeda = true, $bolPalavraFeminina = false)
    {
        return \Core\Bundle\AdminBundle\Services\Extenso::valorPorExtenso($valor, $bolExibirMoeda, $bolPalavraFeminina);
    }

    public function getName()
    {
        return 'extenso';
    }
}
