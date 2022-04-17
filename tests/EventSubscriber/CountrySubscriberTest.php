<?php

namespace BW\StandWithUkraineBundle\Tests\EventSubscriber;

use BW\StandWithUkraineBundle\EventSubscriber\AcceptLanguageSubscriber;
use BW\StandWithUkraineBundle\EventSubscriber\BannerSubscriber;
use BW\StandWithUkraineBundle\EventSubscriber\CountrySubscriber;
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

class CountrySubscriberTest extends TestCase
{
    private const PUBLIC_DNS_GOOGLE = '8.8.8.8';
    private const PUBLIC_DNS_YANDEX = '77.88.8.8';

    /**
     * @var BannerSubscriber|MockObject
     */
    private $bannerSubsciberMock;
    /**
     * @var BannerSubscriber|MockObject
     */
    private $twigMock;
    /**
     * @var Request|MockObject
     */
    private $requestMock;

    public function testCountryIsForbidden()
    {
        $subscriber = $this->createSubscriber();
        $event = $this->createRequestEvent();

        $this->twigMock
            ->expects($this->once())
            ->method('render');
        $this->bannerSubsciberMock
            ->expects($this->once())
            ->method('disable');
        $this->requestMock->server->set('REMOTE_ADDR', self::PUBLIC_DNS_YANDEX);

        $subscriber->onRequestEvent($event);

        $response = $event->getResponse();
        self::assertEquals($response->getStatusCode(), 403);
    }

    public function testCountryIsNotForbidden()
    {
        $subscriber = $this->createSubscriber();
        $event = $this->createRequestEvent();

        $this->twigMock
            ->expects($this->never())
            ->method('render');
        $this->bannerSubsciberMock
            ->expects($this->never())
            ->method('disable');
        $this->requestMock->server->set('REMOTE_ADDR', self::PUBLIC_DNS_GOOGLE);

        $subscriber->onRequestEvent($event);

        self::assertNull($event->getResponse());
    }

    private function createSubscriber(): CountrySubscriber
    {
        $this->bannerSubsciberMock = $this->createMock(BannerSubscriber::class);
        $this->twigMock = $this->createMock(Environment::class);

        return new CountrySubscriber($this->bannerSubsciberMock, $this->twigMock, true);
    }

    private function createRequestEvent(int $requestType = HttpKernelInterface::MAIN_REQUEST): RequestEvent
    {
        $kernelMock = $this->createMock(KernelInterface::class);
        $this->requestMock = $this->createMock(Request::class);
        $this->requestMock->query = new ParameterBag();
        $this->requestMock->server = new ParameterBag();

        return new RequestEvent($kernelMock, $this->requestMock, $requestType);
    }
}
