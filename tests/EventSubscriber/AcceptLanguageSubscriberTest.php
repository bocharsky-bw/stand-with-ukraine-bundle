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
    /**
     * @var Request|MockObject
     */
    private $requestMock;

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
        $this->requestMock
            ->method('getPreferredLanguage')
            ->willReturn('ru');

        $subscriber->onRequestEvent($event);

        $response = $event->getResponse();
        self::assertEquals($response->getStatusCode(), 406);
    }

    public function testPreferredLanguageNotForbidden()
    {
        $subscriber = $this->createSubscriber();
        $event = $this->createRequestEvent();

        $this->twigMock
            ->expects($this->never())
            ->method('render');
        $this->bannerSubsciberMock
            ->expects($this->never())
            ->method('disable');
        $this->requestMock
            ->method('getPreferredLanguage')
            ->willReturn('uk');

        $subscriber->onRequestEvent($event);

        self::assertNull($event->getResponse());
    }

    /**
     * @dataProvider userAgentProvider()
     */
    public function testDetermineBrowser(string $userAgent, ?string $browser)
    {
        $subscriber = $this->createSubscriber();

        $requetMock = $this->createMock(Request::class);
        $requetMock->headers = new ParameterBag();
        $requetMock->headers->set('user-agent', $userAgent);

        $method = new \ReflectionMethod($subscriber, 'determineBrowser');
        $method->setAccessible(true);
        $result = $method->invoke($subscriber, $requetMock);

        self::assertEquals($result, $browser);
    }

    public function userAgentProvider(): array
    {
        return [
            [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.88 Safari/537.36',
                'Chrome',
            ],
            [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:99.0) Gecko/20100101 Firefox/99.0',
                'Firefox',
            ],
            [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.4 Safari/605.1.15',
                'Safari',
            ],
            // Undefined browser
            [
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.4',
                null,
            ],
            // No User-Agent at all?
            [
                '',
                null,
            ],
        ];
    }

    private function createSubscriber(): AcceptLanguageSubscriber
    {
        $this->bannerSubsciberMock = $this->createMock(BannerSubscriber::class);
        $this->twigMock = $this->createMock(Environment::class);

        return new AcceptLanguageSubscriber($this->bannerSubsciberMock, $this->twigMock, true);
    }

    private function createRequestEvent(int $requestType = HttpKernelInterface::MAIN_REQUEST): RequestEvent
    {
        $kernelMock = $this->createMock(KernelInterface::class);
        $this->requestMock = $this->createMock(Request::class);
        $this->requestMock->query = new ParameterBag();
        $this->requestMock->headers = new ParameterBag();

        return new RequestEvent($kernelMock, $this->requestMock, $requestType);
    }
}
