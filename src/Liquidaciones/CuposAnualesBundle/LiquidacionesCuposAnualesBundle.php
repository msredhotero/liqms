<?php

namespace Liquidaciones\CuposAnualesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Liquidaciones\IntranetBundle\DependencyInjection\Security\Factory\IntranetFactory;

class LiquidacionesCuposAnualesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new IntranetFactory());
    }
}
