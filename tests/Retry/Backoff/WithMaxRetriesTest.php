<?php

namespace Ekomobile\Retry;

use Ekomobile\Retry\Backoff\BackoffInterface;
use Ekomobile\Retry\Backoff\WithMaxRetries;
use PHPUnit\Framework\TestCase;

class WithMaxRetriesTest extends TestCase
{
    public function testMaxRetries(): void
    {
        $retries = 3;
        $backoffInterval = 10;

        /** @var BackoffInterface|\PHPUnit\Framework\MockObject\MockObject $backoff */
        $backoff = $this->createMock(WithMaxRetries::class);
        $backoff->expects($this->once())
            ->method('resetBackoff');
        $backoff->expects($this->exactly($retries))
            ->method('nextBackoff')
            ->willReturn($backoffInterval);

        $b = new WithMaxRetries($backoff, $retries);

        $this->assertEquals($b->nextBackoff(), $backoffInterval);
        $this->assertEquals($b->nextBackoff(), $backoffInterval);
        $this->assertEquals($b->nextBackoff(), $backoffInterval);
        $this->assertEquals($b->nextBackoff(), BackoffInterface::STOP);
    }

    /**
     * @dataProvider invalidMaxRetriesDataProvider
     *
     * @param int $maxRetries
     */
    public function testInvalidMaxRetries($maxRetries): void
    {
        /** @var BackoffInterface|\PHPUnit\Framework\MockObject\MockObject $backoff */
        $backoff = $this->createMock(WithMaxRetries::class);
        $backoff->expects($this->never())
            ->method('resetBackoff');
        $backoff->expects($this->never())
            ->method('nextBackoff');

        $this->expectException(\InvalidArgumentException::class);
        new WithMaxRetries($backoff, $maxRetries);
    }

    public function invalidMaxRetriesDataProvider(): array
    {
        return [
            [0],
            [-1],
            [\PHP_INT_MIN],
        ];
    }
}
