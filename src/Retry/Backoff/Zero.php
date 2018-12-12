<?php

namespace Ekomobile\Retry\Backoff;

class Zero implements BackoffInterface
{
    public function resetBackoff(): void
    {
    }

    public function nextBackoff(): int
    {
        return 0;
    }
}
