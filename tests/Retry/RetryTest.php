<?php

namespace Ekomobile\Retry;

use Ekomobile\Retry\Backoff\BackOffInterface;
use Ekomobile\Retry\Exception\Permanent;
use PHPUnit\Framework\TestCase;

class RetryTest extends TestCase
{
    public function testCallable(): void
    {
        /** @var callable|\PHPUnit\Framework\MockObject\MockObject $operation */
        $operation = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $operation->expects($this->once())
            ->method('__invoke');

        /** @var BackOffInterface|\PHPUnit\Framework\MockObject\MockObject $backoff */
        $backoff = $this->createMock(BackOffInterface::class);
        $backoff->expects($this->once())
            ->method('resetBackOff');
        $backoff->expects($this->never())
            ->method('nextBackOff');

        $r = new Retry($operation, $backoff);

        $this->assertIsCallable($r);
        $r();
    }

    public function testPermanentException(): void
    {
        /** @var callable|\PHPUnit\Framework\MockObject\MockObject $operation */
        $operation = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $operation->expects($this->once())
            ->method('__invoke')
            ->willThrowException(new Permanent(new \Exception()));

        /** @var BackOffInterface|\PHPUnit\Framework\MockObject\MockObject $backoff */
        $backoff = $this->createMock(BackOffInterface::class);
        $backoff->expects($this->once())
            ->method('resetBackOff');
        $backoff->expects($this->never())
            ->method('nextBackOff');

        $this->expectException(\Exception::class);
        $r = new Retry($operation, $backoff);
        $r();
    }

    public function testException(): void
    {
        /** @var callable|\PHPUnit\Framework\MockObject\MockObject $operation */
        $operation = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $operation->expects($this->once())
            ->method('__invoke')
            ->willThrowException(new \Exception());

        /** @var BackOffInterface|\PHPUnit\Framework\MockObject\MockObject $backoff */
        $backoff = $this->createMock(BackOffInterface::class);
        $backoff->expects($this->once())
            ->method('resetBackOff');
        $backoff->expects($this->once())
            ->method('nextBackOff')
            ->willReturn(BackOffInterface::STOP);

        $this->expectException(\Exception::class);
        $r = new Retry($operation, $backoff);
        $r();
    }

    public function testRetry(): void
    {
        /** @var callable|\PHPUnit\Framework\MockObject\MockObject $operation */
        $operation = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $operation->expects($this->exactly(3))
            ->method('__invoke')
            ->willThrowException(new \Exception());

        /** @var BackOffInterface|\PHPUnit\Framework\MockObject\MockObject $backoff */
        $backoff = $this->createMock(BackOffInterface::class);
        $backoff->expects($this->once())
            ->method('resetBackOff');
        $backoff->expects($this->exactly(3))
            ->method('nextBackOff')
            ->willReturnOnConsecutiveCalls(10, 10, BackOffInterface::STOP);

        $this->expectException(\Exception::class);
        $r = new Retry($operation, $backoff);
        $r();
    }
}
