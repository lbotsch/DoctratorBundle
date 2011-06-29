# DoctratorBundle

Bundle to use Doctrator with Symfony2.

## Installing

From the root of your Symfony project...

### Install [Mondongo](https://github.com/mondongo/mondongo), [Doctrator](https://github.com/pablodip/doctrator) & [DoctratorBundle](https://github.com/pablodip/DoctratorBundle)

    $ git submodule git://github.com/pablodip/DoctratorBundle.git src/Lubo/DoctratorBundle
    $ git submodule git://github.com/pablodip/doctrator.git vendor/doctrator
    $ git submodule git://github.com/mondongo/mondongo.git vendor/mondongo
    $ git submodule update --init

### Add to Autoloader

You can find this in *autoload.php* or perhaps your *bootstrap.php* file:

    $loader->registerNamespaces(array(
        ...
        'Lubo'                       => __DIR__.'/../src',
        'Doctrator'                      => __DIR__.'/../vendor/doctrator/src',
        'Mondongo'                       => __DIR__.'/../vendor/mondongo/src',
    ));

### Test out the CLI

Just to make sure everything is loading properly...

    $ php app/console doctrator:generate --help

    Usage:
     doctrator:generate
    ...

## Getting Started

**Recommended Reading:** [Mondator Documentation](http://mondongo.es/documentation/1.0/mondator/en/usage)

### Create a config files

DoctratorBundle will automatically parse these files:

  * app/config/doctrator/*.yml
  * Bundle/Resources/config/doctrator/*.yml

Here is an example:

    # app/config/mondongo/schema.yml
    Model\Article:
        columns:
            id:      { id: auto, type: integer}
            title:   { type: string, length: 255 }
            content: { type: string }

    # DoctratorUserBundle/Resources/config/doctrator/schema.yml (note the bundle name in the namespace)
    Model\DoctratorUserBundle\User:
        columns:
            id:       { id: auto, type: integer }
            username: { type: string, length: 20 }
            password: { type: string, length: 40 }

### Generate your models

    $ php app/console doctrator:generate

