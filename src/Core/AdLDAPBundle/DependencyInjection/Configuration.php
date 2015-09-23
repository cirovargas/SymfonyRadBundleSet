<?php

namespace Core\AdLDAPBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('core_ad_ldap');
        
        $rootNode
            ->children()
                ->scalarNode('account_suffix')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('admin_username')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('admin_password')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('base_dn')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('domain_controllers')->prototype('scalar')->end()
            ->end()
        ->end()->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
