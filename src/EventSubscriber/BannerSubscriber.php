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
    private bool $isDisabled = false;

    private Environment $twig;
    private string $position;
    private ?string $targetUrl = null;
    private ?string $brandName = null;

    public function __construct(Environment $twig, string $position, ?string $targetUrl, ?string $brandName)
    {
        $this->twig = $twig;
        $this->position = $position;
        $this->targetUrl = $targetUrl;
        $this->brandName = $brandName;
    }

    /**
     * @TODO Check for AJAX request and return AJAX response instead
     */
    public function onResponseEvent(ResponseEvent $event)
    {
        // Make sure other listeners have not disabled it
        if ($this->isDisabled) {
            return;
        }

        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        // Skip AJAX requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

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

        $bannerHtml = $this->twig->render('@StandWithUkraine/_banner.html.twig', [
            'httpHost' => $request->getHttpHost(),
            'targetUrl' => $this->targetUrl,
            'brandName' => $this->brandName,
        ]);

        switch ($this->position) {
            case 'top':
                $content = preg_replace('@\<body.*?\>@i', '$0'.$bannerHtml, $content, 1);

                break;
            case 'bottom':
                $content = str_ireplace('</body>', $bannerHtml.'</body>', $content);

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

    public function disable(): void
    {
        $this->isDisabled = true;
    }
}
