<?php

namespace Ekomobile\Retry\Backoff;

/**
 *
 */
class WithMaxRetries implements BackOffInterface
{
    /** @var int */
    private $maxRetries;

    /** @var int */
    private $numRetries;

    /** @var BackOffInterface */
    private $backOff;

    /**
     * @param BackOffInterface $backOff
     * @param int              $maxRetries
     */
    public function __construct(BackOffInterface $backOff, int $maxRetries)
    {
        $this->backOff = $backOff;
        $this->maxRetries = $maxRetries;

        $this->resetBackOff();
    }

    /**
     *
     */
    public function resetBackOff(): void
    {
        $this->numRetries = $this->maxRetries;
        $this->backOff->resetBackOff();
    }

    /**
     * @return int
     */
    public function nextBackOff(): int
    {
        if ($this->maxRetries > 0) {
            if ($this->numRetries >= $this->maxRetries) {
                return BackOffInterface::STOP;
            }
            $this->numRetries++;
        }

        return $this->backOff->nextBackOff();
    }
}
