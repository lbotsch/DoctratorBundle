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

namespace Bundle\DoctratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrator\Driver\DoctratorDriver;
use Doctrator\EntityManagerContainer;

/**
 * DoctratorBundle.
 *
 * @package DoctratorBundle
 * @author  Pablo Díez Pascual <pablodip@gmail.com>
 */
class DoctratorBundle extends Bundle
{
    public function boot()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        // FIXME
        $metadataDriverImpl = $em->getConfiguration()->getMetadataDriverImpl();
        foreach ($this->container->get('kernel')->getBundles() as $bundle) {
            if (is_dir($dir = $bundle->getPath().'/Entity')) {
                $bundleClass = get_class($bundle);
                $metadataDriverImpl->addDriver(new DoctratorDriver($dir), substr($bundleClass, 0, strrpos($bundleClass, '\\')).'\Entity');
            }
        }

        EntityManagerContainer::setEntityManager($em);
    }
}
