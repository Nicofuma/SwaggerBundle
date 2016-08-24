<?php

namespace SwaggerValidationBundle\Swagger;

use FR3D\SwaggerAssertions\SchemaManager;
use Symfony\Component\HttpKernel\KernelInterface;

class SchemaManagerFactory
{
    /** @var KernelInterface */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Instantiates a new SchemaManager from a given file.
     * The file can be an absolute path, a resource located in app/Resources or a resource located in a bundle.
     *
     * @param string $file
     *
     * @return SchemaManager
     */
    public function createFromFile($file)
    {
        if (is_file($file)) {
            // Do nothing
        } elseif (is_file($this->kernel->getRootDir().'/Resources/'.$file)) {
            $file = $this->kernel->getRootDir().'/Resources/'.$file;
        } else {
            $file = $this->kernel->locateResource($file);
        }

        $uri = 'file://'.$file;

        return SchemaManager::fromUri($uri);
    }
}
