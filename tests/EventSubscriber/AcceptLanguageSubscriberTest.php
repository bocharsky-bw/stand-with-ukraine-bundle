<?php

namespace BW\StandWithUkraineBundle\Tests\EventSubscriber;

use BW\StandWithUkraineBundle\EventSubscriber\AcceptLanguageSubscriber;
use BW\StandWithUkraineBundle\EventSubscriber\BannerSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class AcceptLanguageSubscriberTest extends TestCase
{
    /**
     * @var BannerSubscriber|MockObject
     */
    private $bannerSubsciberMock;
    /**
     * @var BannerSubscriber|MockObject
     */
    private $twigMock;

    public function testPreferredLanguageForbidden()
    {
        $subscriber = $this->createSubscriber();
        $event = $this->createRequestEvent();

        $this->twigMock
            ->expects($this->once())
            ->method('render');
        $this->bannerSubsciberMock
            ->expects($this->once())
            ->method('disable');

        $subscriber->onRequestEvent($event);

        $response = $event->getResponse();
        self::assertEquals($response->getStatusCode(), 406);
    }

    private function createSubscriber(): AcceptLanguageSubscriber
    {
        $this->bannerSubsciberMock = $this->createMock(BannerSubscriber::class);
        $this->twigMock = $this->createMock(Environment::class);

        return new AcceptLanguageSubscriber($this->bannerSubsciberMock, $this->twigMock);
    }

    private function createRequestEvent(int $requestType = HttpKernelInterface::MAIN_REQUEST): RequestEvent
    {
        $kernelMock = $this->createMock(KernelInterface::class);
        $requestMock = $this->createMock(Request::class);
        $requestMock->query = $this->createMock(ParameterBag::class);
        $requestMock->headers = $this->createMock(ParameterBag::class);
        $requestMock->method('getPreferredLanguage')
            ->willReturn('ru');

        return new RequestEvent($kernelMock, $requestMock, $requestType);
    }
}
