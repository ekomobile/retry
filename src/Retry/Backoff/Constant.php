<?php

namespace Ekomobile\Retry\Backoff;

class Constant implements BackoffInterface
{
    /** @var int microseconds */
    private $interval;

    public function __construct(int $interval)
    {
        $this->interval = $interval;
    }

    public function resetBackoff(): void
    {
    }

    public function nextBackoff(): int
    {
        return $this->interval;
    }
}
