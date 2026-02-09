<?php

namespace App\Providers;

use App\Services\Workflow\ConditionEvaluator;
use App\Services\Workflow\Executors\AutoStepExecutor;
use App\Services\Workflow\Executors\DecisionStepExecutor;
use App\Services\Workflow\Executors\HumanStepExecutor;
use App\Services\Workflow\Executors\WaitingStepExecutor;
use App\Services\Workflow\StepExecutorRegistry;
use App\Services\Workflow\TransitionResolver;
use App\Services\Workflow\WorkflowEngine;
use Illuminate\Support\ServiceProvider;

/**
 * Registers the generic Workflow Engine and its dependencies.
 *
 * The engine is configuration-driven and has no coupling to legacy
 * dynamic service workflow logic.
 */
class WorkflowServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConditionEvaluator::class);

        $this->app->singleton(TransitionResolver::class, function ($app) {
            return new TransitionResolver($app->make(ConditionEvaluator::class));
        });

        $this->app->singleton(StepExecutorRegistry::class, function ($app) {
            return new StepExecutorRegistry([
                $app->make(HumanStepExecutor::class),
                $app->make(DecisionStepExecutor::class),
                $app->make(AutoStepExecutor::class),
                $app->make(WaitingStepExecutor::class),
            ]);
        });

        $this->app->singleton(WorkflowEngine::class, function ($app) {
            return new WorkflowEngine(
                $app->make(StepExecutorRegistry::class),
                $app->make(TransitionResolver::class)
            );
        });
    }
}
