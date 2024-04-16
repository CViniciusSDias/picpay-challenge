<?php

declare(strict_types=1);

namespace App\Infra;

use Monolog\ResettableInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

#[AsEventListener]
class ResetMonologFingersCrossedHandlerOnTerminate
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(TerminateEvent $event)
    {
        $this->logger->debug('Resetting logger', ['logger_class' => $this->logger::class]);
        if ($this->logger instanceof ResettableInterface) {
            $this->logger->reset();
        }
    }
}
