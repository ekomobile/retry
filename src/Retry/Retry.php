<?php

namespace Ekomobile\Retry;

use Ekomobile\Retry\Backoff\BackoffInterface;
use Ekomobile\Retry\Backoff\Exponential;
use Ekomobile\Retry\Exception\Permanent;

/**
 *
 */
class Retry
{
    /** @var callable */
    private $operation;

    /** @var BackoffInterface */
    private $backoff;

    /** @var callable|null */
    private $notify;

    /**
     * @param callable              $operation
     * @param BackoffInterface|null $backoff
     * @param callable|null         $notify
     */
    public function __construct(callable $operation, BackoffInterface $backoff = null, callable $notify = null)
    {
        $this->operation = $operation;
        $this->backoff = $backoff ?? new Exponential();
        $this->notify = $notify;
    }

    /**
     * @throws \Throwable
     */
    public function __invoke()
    {
        $this->backoff->resetBackoff();

        while (true) {
            try {
                ($this->operation)();
                break;

            } catch (Permanent $e) {
                if ($this->notify) {
                    ($this->notify)($e->getPrevious());
                }
                throw $e->getPrevious();

            } catch (\Throwable $e) {
                if ($this->notify) {
                    ($this->notify)($e);
                }

                $backoffDuration = $this->backoff->nextBackoff();
                if ($backoffDuration == BackoffInterface::STOP) {
                    throw $e;
                }

                \usleep($backoffDuration);
            }
        }
    }
}
