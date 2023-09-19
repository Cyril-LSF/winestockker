<?php

namespace App\Twig\Runtime;

use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\RuntimeExtensionInterface;

class VarDirectoryRuntime implements RuntimeExtensionInterface
{
    public function __construct(private KernelInterface $kernel)
    {
        // Inject dependencies if needed
    }

    public function doSomething(string $value)
    {
        return $this->kernel->getProjectDir() . '/var/' . $value;
    }
}
