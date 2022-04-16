<?php

namespace BW\StandWithUkraineBundle\DependencyInjection;

use BW\StandWithUkraineBundle\EventSubscriber\BannerSubscriber;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
            $definition = $container->register(BannerSubscriber::class, BannerSubscriber::class);
            $definition->setArgument('$twig', new Reference(Environment::class));
            $definition->setArgument('$targetUrl', $config['banner']['target_url']);
            $definition->setArgument('$brandName', $config['banner']['brand_name']);
            $definition->addTag('kernel.event_subscriber');
        }
    }
}
