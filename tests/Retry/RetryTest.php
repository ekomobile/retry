<?php

namespace Ekomobile\Retry;

use Ekomobile\Retry\Backoff\BackoffInterface;
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

        /** @var BackoffInterface|\PHPUnit\Framework\MockObject\MockObject $backoff */
        $backoff = $this->createMock(BackoffInterface::class);
        $backoff->expects($this->once())
            ->method('resetBackoff');
        $backoff->expects($this->never())
            ->method('nextBackoff');

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

        /** @var BackoffInterface|\PHPUnit\Framework\MockObject\MockObject $backoff */
        $backoff = $this->createMock(BackoffInterface::class);
        $backoff->expects($this->once())
            ->method('resetBackoff');
        $backoff->expects($this->never())
            ->method('nextBackoff');

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

        /** @var BackoffInterface|\PHPUnit\Framework\MockObject\MockObject $backoff */
        $backoff = $this->createMock(BackoffInterface::class);
        $backoff->expects($this->once())
            ->method('resetBackoff');
        $backoff->expects($this->once())
            ->method('nextBackoff')
            ->willReturn(BackoffInterface::STOP);

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

        /** @var BackoffInterface|\PHPUnit\Framework\MockObject\MockObject $backoff */
        $backoff = $this->createMock(BackoffInterface::class);
        $backoff->expects($this->once())
            ->method('resetBackoff');
        $backoff->expects($this->exactly(3))
            ->method('nextBackoff')
            ->willReturnOnConsecutiveCalls(10, 10, BackoffInterface::STOP);

        $this->expectException(\Exception::class);
        $r = new Retry($operation, $backoff);
        $r();
    }
}
