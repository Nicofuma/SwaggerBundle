SwaggerValidationBundle
===============

[![Latest Stable Version](https://poser.pugx.org/nicofuma/swagger-validation-bundle/v/stable.png)](https://packagist.org/packages/nicofuma/swagger-validation-bundle "Latest Stable Version")
[![Latest Unstable Version](https://poser.pugx.org/nicofuma/swagger-validation-bundle/v/unstable.png)](https://packagist.org/packages/nicofuma/swagger-validation-bundle "Latest Unstable Version")
[![License](https://poser.pugx.org/nicofuma/swagger-validation-bundle/license)](https://packagist.org/packages/nicofuma/swagger-validation-bundle)
[![Travis Build Status](https://api.travis-ci.org/Nicofuma/SwaggerValidationBundle.png?branch=master)](https://travis-ci.org/Nicofuma/SwaggerValidationBundle "Build status")
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c960afc9-f67d-464b-9c0a-351864e86e7e/mini.png)](https://insight.sensiolabs.com/projects/c960afc9-f67d-464b-9c0a-351864e86e7e "SensioLabsInsight")
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Nicofuma/SwaggerValidationBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Nicofuma/SwaggerValidationBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Nicofuma/SwaggerValidationBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Nicofuma/SwaggerValidationBundle/?branch=master)

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
composer require nicofuma/swagger-validation-bundle:^1.0
```

### Bleeding-edge version

```bash
composer require nicofuma/swagger-validation-bundle:@dev
```

### Enabling the bundle

Add the bundle to your AppKernel.

```php
// in %kernel.root_dir%/AppKernel.php
$bundles = array(
    // ...
    new SwaggerValidationBundle\SwaggerValidationBundle(),
    // ...
);
```

Configuration
-------------

```yml
swagger_validation:
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
# Default configuration for extension with alias: "swagger_validation"
swagger_validation:
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


Contributing
------------

SwaggerValidationBundle is an open source project. If you'd like to contribute, please do.

License
-------

This library is under the MIT license. For the full copyright and license information, please view the [LICENSE]() file that was distributed with this source code.
