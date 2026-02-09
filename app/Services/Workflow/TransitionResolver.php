<?php

namespace App\Services\Workflow;

use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\WorkflowTransition;

/**
 * TransitionResolver
 *
 * Selects the appropriate transition to follow from a given step,
 * based on an optional simple condition key and/or an expression
 * evaluated against the instance context.
 */
class TransitionResolver
{
    public function __construct(
        protected ConditionEvaluator $evaluator
    ) {
    }

    /**
     * Resolve the next transition for the current step.
     *
     * @param WorkflowInstance $instance
     * @param WorkflowStep     $currentStep
     * @param string|null      $conditionKey Simple key (approved, rejected, etc.)
     * @param array            $context      Context used for expression evaluation.
     */
    public function resolve(
        WorkflowInstance $instance,
        WorkflowStep $currentStep,
        ?string $conditionKey,
        array $context = []
    ): ?WorkflowTransition {
        $transitions = $currentStep->outgoingTransitions()->get();

        $matchedByKeyAndExpression = null;
        $matchedByExpressionOnly = null;
        $defaultTransition = null;

        foreach ($transitions as $transition) {
            /** @var WorkflowTransition $transition */
            $matchesKey = $conditionKey !== null && $transition->condition_key === $conditionKey;
            $expressionResult = $this->evaluator->evaluate($transition->condition_expression, $context);

            if ($matchesKey && $expressionResult) {
                $matchedByKeyAndExpression = $transition;
                break;
            }

            if (!$matchesKey && $expressionResult && !$matchedByExpressionOnly) {
                $matchedByExpressionOnly = $transition;
            }

            if ($transition->is_default) {
                $defaultTransition = $transition;
            }
        }

        if ($matchedByKeyAndExpression) {
            return $matchedByKeyAndExpression;
        }

        if ($matchedByExpressionOnly) {
            return $matchedByExpressionOnly;
        }

        return $defaultTransition;
    }
}

