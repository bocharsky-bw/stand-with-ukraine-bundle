<?php

namespace BW\StandWithUkraineBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('censor', [AppRuntime::class, 'censor']),
        ];
    }
}
