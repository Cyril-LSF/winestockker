<?php

namespace App\Twig\Extension;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use App\Twig\Runtime\VarDirectoryRuntime;

class VarDirectoryExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [VarDirectoryRuntime::class, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('var_asset', [VarDirectoryRuntime::class, 'doSomething']),
        ];
    }

}
