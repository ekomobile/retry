<?php

namespace Ekomobile\Retry\Backoff;

class Zero implements BackOffInterface
{
    public function resetBackOff(): void
    {
    }

    public function nextBackOff(): int
    {
        return 0;
    }
}
