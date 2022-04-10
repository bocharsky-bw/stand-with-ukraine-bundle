<?php

namespace BW\StandWithUkraineBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Twig\Environment;

class BannerSubscriber implements EventSubscriberInterface
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @TODO Check for AJAX request and return AJAX response instead
     */
    public function onResponseEvent(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        // Skip internal pages, like WDT and Profiler
        if (str_starts_with($request->getPathInfo(), '/_')) {
            return;
        }

        $response = $event->getResponse();
        if (!$response) {
            return;
        }

        $content = $response->getContent();
        if (!$content) {
            return;
        }

        $bannerHtml = $this->twig->render('@StandWithUkraine/banner.html.twig', [
            'brandName' => 'Symfony Demo',
            'httpHost' => $request->getHttpHost(),
            'targetUrl' => 'https://symfony.com/blog/symfony-stands-with-ukraine',
        ]);

        $position = 'top';
        switch ($position) {
            case 'top':
                $content = preg_replace('@\<body.*?\>@i', '$0'.PHP_EOL.$bannerHtml, $content, 1);

                break;
            case 'bottom':
                $content = str_ireplace('</body>', $bannerHtml.PHP_EOL.'</body>', $content);

                break;
        }

        $response->setContent($content);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => ['onResponseEvent'],
        ];
    }
}
