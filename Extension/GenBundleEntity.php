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
    protected function setup()
    {
        $this->addRequiredOption('gen_dir');
    }

    /**
     * @inheritdoc
     */
    protected function doProcess()
    {
        foreach (array('bundle_name', 'bundle_dir') as $parameter) {
            if (!isset($this->configClass[$parameter])) {
                throw new \RuntimeException(sprintf('The class "%s" does not have the "%s" parameter.', $this->class, $parameter));
            }
        }

        /*
         * Definitions.
         */
        $classes = array(
            'entity'          => 'Gen\%bundle_name%\Entity\%class%',
            'entity_bundle'   => $this->definitions['entity']->getClass(),
            'entity_base'     => 'Gen\%bundle_name%\Entity\Base\%class%',
            'repository'        => 'Gen\%bundle_name%\Entity\%class%Repository',
            'repository_bundle' => $this->definitions['repository']->getClass(),
            'repository_base'   => 'Gen\%bundle_name%\Entity\Base\%class%Repository',
        );
        foreach ($classes as &$class) {
            $class = strtr($class, array(
                '%bundle_name%' => $this->configClass['bundle_name'],
                '%class%'       => substr($this->class, strrpos($this->class, '\\') + 1),
            ));
        }

        // entity
        $this->definitions['entity']->setClass($classes['entity']);
        $this->definitions['entity']->setParentClass('\\'.$classes['entity_bundle']);
        $this->definitions['entity']->setDocComment(<<<EOF
/**
 * {$classes['entity']} entity.
 */
EOF
        );

        $this->definitions['entity_bundle'] = new Definition($classes['entity_bundle']);
        $this->definitions['entity_bundle']->setParentClass('\\'.$classes['entity_base']);
        $this->definitions['entity_bundle']->setIsAbstract(true);
        $this->definitions['entity_bundle']->setDocComment(<<<EOF
/**
 * {$classes['entity']} entity bundle.
 */
EOF
        );

        $this->definitions['entity_base']->setClass($classes['entity_base']);
        $this->definitions['entity_base']->setDocComment(<<<EOF
/**
 * {$classes['entity']} entity base.
 */
EOF
        );

        // repository
        $this->definitions['repository']->setClass($classes['repository']);
        $this->definitions['repository']->setParentClass('\\'.$classes['repository_bundle']);
        $this->definitions['repository']->setDocComment(<<<EOF
/**
 * {$classes['entity']} entity repository
 */
EOF
        );

        $this->definitions['repository_bundle'] = new Definition($classes['repository_bundle']);
        $this->definitions['repository_bundle']->setParentClass('\\'.$classes['repository_base']);
        $this->definitions['repository_bundle']->setDocComment(<<<EOF
/**
 * {$classes['entity']} entity repository bundle
 */
EOF
        );

        $this->definitions['repository_base']->setClass($classes['repository_base']);
        $this->definitions['repository_base']->setDocComment(<<<EOF
/**
 * {$classes['entity']} entity repository base
 */
EOF
        );

        /*
         * Outputs.
         */
        $dirs = array(
            'entity'        => '%gen_dir%/%bundle_name%/Entity',
            'entity_bundle' => '%bundle_dir%/Entity',
            'entity_base'   => '%gen_dir%/%bundle_name%/Entity/Base',
        );
        foreach ($dirs as &$dir) {
            $dir = strtr($dir, array(
                '%gen_dir%'     => $this->getOption('gen_dir'),
                '%bundle_dir%'  => $this->configClass['bundle_dir'],
                '%bundle_name%' => $this->configClass['bundle_name'],
            ));
        }

        // entity
        $this->outputs['entity']->setDir($dirs['entity']);
        $this->outputs['entity_bundle'] = new Output($dirs['entity_bundle']);
        $this->outputs['entity_base']->setDir($dirs['entity_base']);

        // repository
        $this->outputs['repository']->setDir($dirs['entity']);
        $this->outputs['repository_bundle'] = new Output($dirs['entity_bundle']);
        $this->outputs['repository_base']->setDir($dirs['entity_base']);
    }
}
