<?php

namespace Ekomobile\Retry;

use PHPUnit\Framework\TestCase;

class RetryTest extends TestCase
{
    public function testCallable()
    {
        /** @var callable|\PHPUnit\Framework\MockObject\MockObject $operation */
        $operation = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $operation->expects($this->once())
            ->method('__invoke');

        $r = new Retry($operation);

        $this->assertIsCallable($r);
        $r();
    }
}
