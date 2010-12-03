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

namespace Bundle\DoctratorBundle\Extension;

use Mondongo\Mondator\Definition\Definition;
use Mondongo\Mondator\Extension;
use Mondongo\Mondator\Output\Output;

/**
 * GenBundleEntity extension.
 *
 * @package DoctratorBundle
 * @author  Pablo Díez Pascual <pablodip@gmail.com>
 */
class GenBundleEntity extends Extension
{
    /**
     * @inheritdoc
     */
    protected function doProcess()
    {
        foreach (array('bundle_class', 'bundle_dir') as $parameter) {
            if (!isset($this->configClass[$parameter])) {
                throw new \RuntimeException(sprintf('The class "%s" does not have the "%s" config class parameter.', $this->class, $parameter));
            }
        }

        /*
         * Definitions.
         */
        $classes = array(
            'entity_bundle'     => '%bundle_namespace%\Entity\%class_name%',
            'repository_bundle' => '%bundle_namespace%\Entity\%class_name%Repository',
        );
        foreach ($classes as &$class) {
            $class = strtr($class, array(
                '%bundle_namespace%' => substr($this->configClass['bundle_class'], 0, strrpos($this->configClass['bundle_class'], '\\')),
                '%class_name%'       => substr($this->class, strrpos($this->class, '\\') + 1),
            ));
        }

        // entity
        $this->definitions['entity']->setParentClass('\\'.$classes['entity_bundle']);

        $this->definitions['entity_bundle'] = new Definition($classes['entity_bundle']);
        $this->definitions['entity_bundle']->setParentClass('\\'.$this->definitions['entity_base']->getClass());
        $this->definitions['entity_bundle']->setIsAbstract(true);
        $this->definitions['entity_bundle']->setDocComment(<<<EOF
/**
 * {$this->class} entity bundle.
 */
EOF
        );

        // repository
        $this->definitions['repository']->setParentClass('\\'.$classes['repository_bundle']);

        $this->definitions['repository_bundle'] = new Definition($classes['repository_bundle']);
        $this->definitions['repository_bundle']->setParentClass('\\'.$this->definitions['repository_base']->getClass());
        $this->definitions['repository_bundle']->setDocComment(<<<EOF
/**
 * {$this->class} entity repository bundle
 */
EOF
        );

        /*
         * Outputs.
         */

        // entity
        $this->outputs['entity_bundle'] = new Output($this->configClass['bundle_dir'].'/Entity');

        // repository
        $this->outputs['repository_bundle'] = new Output($this->configClass['bundle_dir'].'/Entity');
    }
}
