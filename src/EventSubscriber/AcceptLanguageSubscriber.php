<?php

namespace BW\StandWithUkraineBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class AcceptLanguageSubscriber implements EventSubscriberInterface
{
    private const PREFERRED_LANG_RU = 'ru';

    private ?BannerSubscriber $bannerSubscriber = null;
    private Environment $twig;
    private bool $useLinks;

    public function __construct(?BannerSubscriber $bannerSubscriber, Environment $twig, bool $useLinks)
    {
        $this->bannerSubscriber = $bannerSubscriber;
        $this->twig = $twig;
        $this->useLinks = $useLinks;
    }

    /**
     * @TODO Check for AJAX request and return AJAX response instead
     */
    public function onRequestEvent(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        // Allow internal pages, like WDT and Profiler
        if (str_starts_with($request->getPathInfo(), '/_')) {
            return;
        }

        if (!$this->isPreferredLanguageForbidden($request)) {
            return;
        }

        $browserName = $this->determineBrowser($request);
        // TODO Rename page to access-denied.html.twig
        $content = $this->twig->render('@StandWithUkraine/ban-language.html.twig', [
            'browserName' => $browserName,
            'useLinks' => $this->useLinks,
        ]);
        $response = new Response($content, Response::HTTP_NOT_ACCEPTABLE);
        $event->setResponse($response);

        if ($this->bannerSubscriber) {
            $this->bannerSubscriber->disable();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // priority should be lower than for CountrySubscriber
            RequestEvent::class => ['onRequestEvent', 11],
        ];
    }

    private function isPreferredLanguageForbidden(Request $request): bool
    {
        $preferredLanguage = $request->getPreferredLanguage();
        $shouldOverwritePreferredLang = $request->query->getBoolean('swu_overwrite_preferred_lang_ru', false);
        if ($shouldOverwritePreferredLang) {
            $preferredLanguage = self::PREFERRED_LANG_RU;
        }
        if (!$preferredLanguage) {
            return false;
        }

        if (1 !== preg_match('/'.self::PREFERRED_LANG_RU.'/i', $preferredLanguage)) {
            return false;
        }

        return true;
    }

    private function determineBrowser(Request $request): ?string
    {
        $userAgent = $request->headers->get('user-agent');
        if (!$userAgent) {
            return null;
        }

        switch (true) {
            case preg_match('/Chrome/i', $userAgent):
                return 'Chrome';
            case preg_match('/Firefox/i', $userAgent):
                return 'Firefox';
            case preg_match('/Safari/i', $userAgent):
                return 'Safari';
        }

        return null;
    }
}
