<?php

namespace BW\StandWithUkraineBundle\DependencyInjection;

use BW\StandWithUkraineBundle\EventSubscriber\AcceptLanguageSubscriber;
use BW\StandWithUkraineBundle\EventSubscriber\BannerSubscriber;
use BW\StandWithUkraineBundle\EventSubscriber\CountrySubscriber;
use BW\StandWithUkraineBundle\Twig\TwigExtension;
use BW\StandWithUkraineBundle\Twig\TwigRuntime;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Twig\Environment;

class StandWithUkraineExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        if ($config['banner']['enabled']) {
            // TODO Use string key instead of FQCN class name?
            $definition = $container->register(BannerSubscriber::class, BannerSubscriber::class);
            // TODO Reference could be replaced with TypedReference?
            $definition->setArgument('$twig', new Reference(Environment::class));
            $definition->setArgument('$position', $config['banner']['position']);
            $definition->setArgument('$targetUrl', $config['banner']['target_url']);
            $definition->setArgument('$brandName', $config['banner']['brand_name']);
            $definition->addTag('kernel.event_subscriber');
        }
        if ($config['ban_language']['enabled']) {
            $definition = $container->register(AcceptLanguageSubscriber::class, AcceptLanguageSubscriber::class);
            $definition->setArgument('$bannerSubscriber', new Reference(BannerSubscriber::class, ContainerInterface::NULL_ON_INVALID_REFERENCE));
            $definition->setArgument('$twig', new Reference(Environment::class));
            $definition->setArgument('$useLinks', $config['ban_language']['use_links']);
            $definition->addTag('kernel.event_subscriber');
        }
        if ($config['ban_country']['enabled']) {
            $definition = $container->register(CountrySubscriber::class, CountrySubscriber::class);
            $definition->setArgument('$bannerSubscriber', new Reference(BannerSubscriber::class, ContainerInterface::NULL_ON_INVALID_REFERENCE));
            $definition->setArgument('$twig', new Reference(Environment::class));
            $definition->setArgument('$useLinks', $config['ban_country']['use_links']);
            $definition->addTag('kernel.event_subscriber');
        }

        if ($config['ban_language']['enabled'] || $config['ban_country']['enabled']) {
            // We need to register a Twig extension to provide custom filters
            $definition = $container->register(TwigExtension::class, TwigExtension::class);
            $definition->addTag('twig.extension');
            $definition = $container->register(TwigRuntime::class, TwigRuntime::class);
            $definition->addTag('twig.runtime');
        }
    }
}
