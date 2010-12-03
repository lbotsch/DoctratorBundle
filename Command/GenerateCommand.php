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

namespace Bundle\DoctratorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Mondongo\Mondator\Mondator;

/**
 * GenerateCommand.
 *
 * @package DoctratorBundle
 * @author  Pablo Díez Pascual <pablodip@gmail.com>
 */
class GenerateCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('doctrator:generate')
            ->setDescription('Generate entity classes from config classes.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('processing config classes');

        $genDir = $this->container->getParameter('kernel.root_dir').'/../src/Gen';

        $configClasses = array();
        foreach ($this->container->get('kernel')->getBundles() as $bundle) {
            $bundleClass        = get_class($bundle);
            $bundleName         = substr($bundleClass, strrpos($bundleClass, '\\') + 1);
            $bundleGenNamespace = 'Gen\\'.$bundleName;

            if (is_dir($dir = $bundle->getPath().'/Resources/config/doctrator')) {
                $finder = new Finder();
                foreach ($finder->files()->name('*.yml')->followLinks()->in($dir) as $file) {
                    foreach ((array) Yaml::load($file) as $class => $configClass) {
                        // class
                        if (0 === strpos($class, $bundleGenNamespace)) {
                            if (
                                0 !== strpos($class, $bundleGenNamespace.'\Entity')
                                ||
                                strlen($bundleGenNamespace.'\Entity') !== strrpos($class, '\\')
                            ) {
                                throw new \RuntimeException(sprintf('The class "%s" is not in the Entity namespace of the bundle.', $class));
                            }
                        }

                        // outputs && bundle
                        if (0 === strpos($class, $bundleGenNamespace)) {
                            $configClass['output'] = $genDir.'/'.$bundleName.'/Entity';

                            $configClass['bundle_class'] = $bundleClass;
                            $configClass['bundle_dir']   = $bundle->getPath();
                        } else {
                            unset($configClass['output'], $configClass['bundle_name'], $configClass['bundle_dir']);
                        }

                        // merge
                        if (!isset($configClasses[$class])) {
                            $configClasses[$class] = array();
                        }
                        $configClasses[$class] = array_merge_recursive($configClasses[$class], $configClass);
                    }
                }
            }
        }

        $output->writeln('generating classes');

        $mondator = new Mondator();
        $mondator->setConfigClasses($configClasses);
        $mondator->setExtensions(array(
            new \Doctrator\Extension\Core(),
            new \Bundle\DoctratorBundle\Extension\GenBundleEntity(),
        ));
        $mondator->process();
    }
}
