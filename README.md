NicofumaSwaggerBundle
===============

[![Latest Stable Version](https://poser.pugx.org/nicofuma/swagger-bundle/v/stable.png)](https://packagist.org/packages/nicofuma/swagger-bundle "Latest Stable Version")
[![Latest Unstable Version](https://poser.pugx.org/nicofuma/swagger-bundle/v/unstable.png)](https://packagist.org/packages/nicofuma/swagger-bundle "Latest Unstable Version")
[![License](https://poser.pugx.org/nicofuma/swagger-bundle/license)](https://packagist.org/packages/nicofuma/swagger-bundle)
[![Travis Build Status](https://api.travis-ci.org/Nicofuma/SwaggerBundle.png?branch=master)](https://travis-ci.org/Nicofuma/SwaggerBundle "Build status")
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c960afc9-f67d-464b-9c0a-351864e86e7e/mini.png)](https://insight.sensiolabs.com/projects/c960afc9-f67d-464b-9c0a-351864e86e7e "SensioLabsInsight")
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Nicofuma/SwaggerBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Nicofuma/SwaggerBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Nicofuma/SwaggerBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Nicofuma/SwaggerBundle/?branch=master)

Description
-----------

This bundle integrates [Swagger](http://swagger.io/) in Symfony.

Currently, it supports the following features:
 - Automatic validation of all incoming requests based on the Swagger definition (including headers and query string)
 - Configuring multiple API with different swagger files using configuration
 - FOSRestBundle integration: automatic configuration of the ParamFetcher
 - Behat integration: context to validate the response

Installation
------------

Add the required package using composer.

### Stable version

```bash
composer require nicofuma/swagger-bundle:^1.0
```

### Bleeding-edge version

```bash
composer require nicofuma/swagger-bundle:@dev
```

### Enabling the bundle

Add the bundle to your AppKernel.

```php
// in %kernel.root_dir%/AppKernel.php
$bundles = array(
    // ...
    new Nicofuma\SwaggerBundle\NicofumaSwaggerBundle(),
    // ...
);
```

Configuration
-------------

```yml
swagger:
    definition:
        pattern: '/api/v1'
        swagger_file: swagger/swagger.json
        strict: true
```

`pattern` any url matching this pattern will be tested against the `swagger_file`
`swagger_file` swagger file to use for this API. Can be either an bsolute path, a path relative to `%kernel.root_dir%/Resources/` or a bundle resource `@MyBundle/Dir/swagger.json`
`strict` whether or not an exception must be thrown if the path does not match any definition in the swagger file

Configuration reference
-----------------------

```yml
# Default configuration for extension with alias: "swagger"
swagger:
    definitions:
        -
            pattern:
                # use the urldecoded format
                path:                 ^/api/public/
                host:                 null
                ips:                  []
                methods:              []
            swagger_file:         swagger-public.json
            strict:               true
        -
            pattern:
                # use the urldecoded format
                path:                 ^/api/private/
                host:                 null
                ips:                  []
                methods:              []
            swagger_file:         swagger-private.json
            strict:               true

```

Behat integration
-----------------

Add the following context in your behat.yml file
```
        - SwaggerValidationBundle\Tests\Behat\Context\SwaggerContext: {map: '@swagger_validation.validator_map'}
```

Contributing
------------

NicofumaSwaggerBundle is an open source project. If you'd like to contribute, please do.

License
-------

This library is under the MIT license. For the full copyright and license information, please view the [LICENSE]() file that was distributed with this source code.
