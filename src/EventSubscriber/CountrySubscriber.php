<?php

namespace BW\StandWithUkraineBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class CountrySubscriber implements EventSubscriberInterface
{
    private const COUNTRY_CODE_RU = 'RU';

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

        if (!$this->isRequestFromForbiddenCountry($request)) {
            return;
        }

        $content = $this->twig->render('@StandWithUkraine/ban-country.html.twig', [
            'useLinks' => $this->useLinks,
        ]);
        $response = new Response($content, Response::HTTP_FORBIDDEN);
        $event->setResponse($response);

        if ($this->bannerSubscriber) {
            $this->bannerSubscriber->disable();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // priority should be higher than for AcceptLanguageSubscriber
            RequestEvent::class => ['onRequestEvent', 12],
        ];
    }

    private function isRequestFromForbiddenCountry(Request $request): bool
    {
        $shouldOverwriteCountryCode = $request->query->getBoolean('swu_overwrite_country_code_ru', false);
        if ($shouldOverwriteCountryCode) {
            $countryCode = self::COUNTRY_CODE_RU;
        } else {
            $userIp = $request->server->get('REMOTE_ADDR');
            if (!$userIp) {
                return false;
            }
            // Skip local IP
            if ('127.0.0.1' === $userIp) {
                return false;
            }

            // TODO Cache this request
            $jsonContent = file_get_contents('http://www.geoplugin.net/json.gp?ip=' . $userIp);
            // TODO Try/catch the exception to silent it
            $data = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
            $countryCode = $data['geoplugin_countryCode'];
        }

        if (strcasecmp(self::COUNTRY_CODE_RU, $countryCode)) {
            return false;
        }

        return true;
    }
}
