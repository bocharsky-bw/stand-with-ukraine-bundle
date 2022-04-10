<?php

namespace BW\StandWithUkraineBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class AcceptLanguageSubscriber implements EventSubscriberInterface
{
    private const COUNTRY_CODE_RUSSIA = 'RU';

    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
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

        $preferredLanguage = $request->getPreferredLanguage();
        if (1 !== preg_match('/en/i', $preferredLanguage)) {
            return;
        }

        $shouldAlsoCheckForCountry = false;
        if ($shouldAlsoCheckForCountry && !$this->isRequestFromRussia($request)) {
            return;
        }

        $browser = $this->determineBrowser($request);
        $content = $this->twig->render('@StandWithUkraine/page.html.twig', [
            'browser' => $browser,
            'messageAsLink' => false,
            'censoredChar' => self::generateRandomCensoredChar(),
        ]);
        $response = new Response($content, Response::HTTP_NOT_ACCEPTABLE);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => ['onRequestEvent', 14],
        ];
    }

    private function determineBrowser(Request $request): ?string
    {
        $userAgent = $request->headers->get('user-agent');

        switch (true) {
            case preg_match('/Chrome/i', $userAgent):
                return 'Google Chrome';
            case preg_match('/Firefox/i', $userAgent):
                return 'Firefox';
            case preg_match('/Safari/i', $userAgent):
                return 'Safari';
        }

        return null;
    }

    private function isRequestFromRussia(Request $request): bool
    {
        $userIp = $request->server->get('REMOTE_ADDR');
        if (!$userIp) {
            return false;
        }

        $jsonContent = file_get_contents('http://www.geoplugin.net/json.gp?ip='.$ip);
        // TODO Try/catch the exception to silent it
        $data = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);

        if (self::COUNTRY_CODE_RUSSIA !== $data['geoplugin_countryCode']) {
            return false;
        }

        return true;
    }

    private static function generateRandomCensoredChar(): string
    {
        $chars = [
            '@',
            '#',
            '$',
            '%',
            '&',
            '*',
        ];

        $randomIndex = array_rand($chars);

        return $chars[$randomIndex];
    }
}
