<?php

namespace Ekomobile\Retry\Backoff;

class Constant implements BackOffInterface
{
    /** @var int microseconds */
    private $interval;

    public function __construct(int $interval)
    {
        $this->interval = $interval;
    }

    public function resetBackOff(): void
    {
    }

    public function nextBackOff(): int
    {
        return $this->interval;
    }
}
