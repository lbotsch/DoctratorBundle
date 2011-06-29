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

namespace Lubo\DoctratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrator\Driver\DoctratorDriver;
use Doctrator\EntityManagerContainer;
use Lubo\DoctratorBundle\DependencyInjection\Compiler\DoctratorMondatorPass;

/**
 * DoctratorBundle.
 *
 * @package DoctratorBundle
 * @author  Pablo Díez Pascual <pablodip@gmail.com>
 */
class LuboDoctratorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->container->has('doctrator.mondator')) {
            $em = $this->container->get('doctrine.orm.entity_manager');

            // FIXME
            $modelDir = $this->container->getParameter('kernel.root_dir').'/../src/Model';
            $driver = new DoctratorDriver($modelDir);
            $em->getConfiguration()->getMetadataDriverImpl()->addDriver($driver, 'Model');

            EntityManagerContainer::setEntityManager($em);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DoctratorMondatorPass);
    }
}
