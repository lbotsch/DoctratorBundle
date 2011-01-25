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

namespace Bundle\Pablodip\DoctratorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * DoctratorExtension.
 *
 * @package DoctratorBundle
 * @author  Pablo Díez Pascual <pablodip@gmail.com>
 */
class DoctratorExtension extends Extension
{
    /**
     * Loads the configuration.
     *
     * @param array            $config    An array of settings.
     * @param ContainerBuilder $container A ContainerBuilder instance.
     *
     * @return void
     */
    public function configLoad(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('doctrator.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/doctrator';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'doctrator';
    }
}