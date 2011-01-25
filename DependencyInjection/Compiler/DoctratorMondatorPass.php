<?php

/*
 * Copyright 2010 Pablo Díez Pascual <pablodip@gmail.com>
 *
 * This file is part of DoctratorBundle.
 *
 * DoctratorBundle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DoctratorBundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with DoctratorBundle. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Bundle\Pablodip\DoctratorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * DoctratorBundle.
 *
 * @package DoctratorBundle
 * @author  Pablo Díez Pascual <pablodip@gmail.com>
 */
class DoctratorMondatorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('doctrator.mondator')) {
            return;
        }

        $mondatorDefinition = $container->getDefinition('doctrator.mondator');

        // core
        $definition = new Definition('Doctrator\Extension\Core');
        $container->setDefinition('doctrator.extension.core', $definition);

        $mondatorDefinition->addMethodCall('addExtension', array(new Reference('doctrator.extension.core')));

        // bundles
        $definition = new Definition('Bundle\Pablodip\DoctratorBundle\Extension\Bundles');
        $container->setDefinition('doctrator.extension.bundles', $definition);

        $mondatorDefinition->addMethodCall('addExtension', array(new Reference('doctrator.extension.bundles')));

        // custom
        foreach ($container->findTaggedServiceIds('doctrator.mondator.extension') as $id => $attributes) {
            $mondatorDefinition->addMethodCall('addExtension', array(new Reference($id)));
        }
    }
}
