<?php

namespace Liquidaciones\IntranetBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class IntranetFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.intranet.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('intranet.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'security.authentication.listener.intranet.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('intranet.security.authentication.listener'))
                ->replaceArgument(2, $config['login_path'])
                ->replaceArgument(3, $config['nombreUsuario'])
                ->replaceArgument(4, $config['rol'])
                ->replaceArgument(5, $config['codSistema'])
                ->replaceArgument(6, $config['descEstablecimiento'])
                ->replaceArgument(7, $config['perfilId'])
                ->replaceArgument(8, $config['IdEstablecimiento'])
                ->replaceArgument(9, $config['Id'])
                ->replaceArgument(10, $config['IdEstablLargo'])
                ->replaceArgument(11, $config['multidep_roles'])
                ->replaceArgument(12, $config['multidep_multi'])
                ->replaceArgument(13, $config['multidep_actual']);

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'intranet';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node->children()->scalarNode('login_path')->defaultValue('')->end();
        $node->children()->scalarNode('nombreUsuario')->defaultValue('')->end();
        $node->children()->scalarNode('rol')->defaultValue('')->end();
        $node->children()->scalarNode('codSistema')->defaultValue('')->end();
        $node->children()->scalarNode('descEstablecimiento')->defaultValue('')->end();
        $node->children()->scalarNode('perfilId')->defaultValue('')->end();
        $node->children()->scalarNode('IdEstablecimiento')->defaultValue('')->end();
        $node->children()->scalarNode('Id')->defaultValue('')->end();
        $node->children()->scalarNode('IdEstablLargo')->defaultValue('')->end();
        $node->children()->variableNode('multidep_roles')->defaultValue(array())->end();
        $node->children()->scalarNode('multidep_multi')->defaultValue('')->end();
        $node->children()->scalarNode('multidep_actual')->defaultValue('')->end();
    }
}
