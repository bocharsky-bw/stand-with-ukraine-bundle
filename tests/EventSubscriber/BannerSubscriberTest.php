<?php

namespace BW\StandWithUkraineBundle\Tests\EventSubscriber;

use BW\StandWithUkraineBundle\EventSubscriber\BannerSubscriber;
use DG\BypassFinals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class BannerSubscriberTest extends TestCase
{
    /**
     * @var Environment|MockObject
     */
    private $twigMock;

    /**
     * @var Response|MockObject
     */
    private $responseMock;

    public function testSubscriberCanBeDisabled()
    {
        BypassFinals::enable();

        $subscriber = $this->createSubscriber();
        $subscriber->disable();

        $event = $this->createMock(ResponseEvent::class);
        $event->expects($this->never())
            ->method('isMainRequest');

        $subscriber->onResponseEvent($event);
    }

    public function testSubscriberAddsBanner()
    {
        BypassFinals::enable();

        $subscriber = $this->createSubscriber();

        $event = $this->createResponseEvent();
        $this->responseMock
            ->method('getContent')
            ->willReturn('<body>content</body>');

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->willReturn('<div>banner</div>');
        $this->responseMock
            ->expects($this->once())
            ->method('setContent')
            ->with('<body><div>banner</div>content</body>');

        $subscriber->onResponseEvent($event);
    }

    private function createSubscriber(): BannerSubscriber
    {
        $this->twigMock = $this->createMock(Environment::class);

        return new BannerSubscriber($this->twigMock, 'top', '/swu', 'ThisBundle');
    }

    private function createResponseEvent(int $requestType = HttpKernelInterface::MAIN_REQUEST): ResponseEvent
    {
        $kernelMock = $this->createMock(KernelInterface::class);
        $requestMock = $this->createMock(Request::class);
        $this->responseMock = $this->createMock(Response::class);

        return new ResponseEvent($kernelMock, $requestMock, $requestType, $this->responseMock);
    }
}
