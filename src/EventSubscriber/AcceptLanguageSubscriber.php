<?php

namespace BW\StandWithUkraineBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class AcceptLanguageSubscriber implements EventSubscriberInterface
{
    private const PREFERRED_LANG_RU = 'ru';

    private Environment $twig;
    private TranslatorInterface $translator;

    public function __construct(Environment $twig, TranslatorInterface $translator)
    {
        $this->twig = $twig;
        $this->translator = $translator;
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

        if (!$this->isForbiddenLanguage($request)) {
            return;
        }

        $browser = $this->determineBrowser($request);
        $content = $this->twig->render('@StandWithUkraine/page.html.twig', [
            'browser' => $browser,
            'messageAsLink' => true,
        ]);
        $response = new Response($content, Response::HTTP_NOT_ACCEPTABLE);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // priority should be lower than for BlockCountrySubscriber
            RequestEvent::class => ['onRequestEvent', 13],
        ];
    }

    private function isForbiddenLanguage(Request $request): bool
    {
        $preferredLanguage = $request->getPreferredLanguage();

        $overwrittenPreferredLang = $request->query->get('swu_preferred_lang', false);
        if ($overwrittenPreferredLang) {
            $preferredLanguage = $overwrittenPreferredLang;
        }

        if (1 !== preg_match('/'.self::PREFERRED_LANG_RU.'/i', $preferredLanguage)) {
            return false;
        }

        return true;
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

    private function applyCensorship($text): string
    {

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
