<?php

namespace Retry;

use Ekomobile\Retry\Backoff\BackOffInterface;
use Ekomobile\Retry\Backoff\Exponential;
use Ekomobile\Retry\Exception\Permanent;

/**
 *
 */
class Retry
{
    /** @var callable */
    private $operation;

    /** @var BackOffInterface */
    private $backOff;

    /** @var callable|null */
    private $notify;

    /**
     * @param callable              $operation
     * @param BackOffInterface|null $backOff
     * @param callable|null         $notify
     */
    public function __construct(callable $operation, BackOffInterface $backOff = null, callable $notify = null)
    {
        $this->operation = $operation;
        $this->backOff = $backOff ?? new Exponential();
        $this->notify = $notify;
    }

    /**
     * @throws \Throwable
     */
    public function __invoke()
    {
        $this->backOff->resetBackOff();

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

                $backOffDuration = $this->backOff->nextBackOff();
                if ($backOffDuration == BackOffInterface::STOP) {
                    throw $e;
                }

                \usleep($backOffDuration);
            }
        }
    }
}
