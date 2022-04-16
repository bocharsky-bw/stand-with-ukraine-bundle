<?php

namespace BW\StandWithUkraineBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('censor', [TwigRuntime::class, 'censor']),
        ];
    }
}
