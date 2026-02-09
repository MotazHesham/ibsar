<?php

namespace App\Services\Workflow;

use App\Services\Workflow\Contracts\WorkflowStepExecutorInterface;
use InvalidArgumentException;

/**
 * Simple registry/factory for workflow step executors.
 *
 * This can be bound in the service container and extended
 * with custom executors if new step types are introduced.
 */
class StepExecutorRegistry
{
    /**
     * @var WorkflowStepExecutorInterface[]
     */
    protected array $executors = [];

    /**
     * @param iterable<WorkflowStepExecutorInterface> $executors
     */
    public function __construct(iterable $executors = [])
    {
        foreach ($executors as $executor) {
            $this->register($executor);
        }
    }

    public function register(WorkflowStepExecutorInterface $executor): void
    {
        $this->executors[] = $executor;
    }

    public function forType(string $stepType): WorkflowStepExecutorInterface
    {
        foreach ($this->executors as $executor) {
            if ($executor->supports($stepType)) {
                return $executor;
            }
        }

        throw new InvalidArgumentException("No workflow step executor registered for type [{$stepType}]");
    }
}

