<?php

namespace Modules\DynamicServices\Workflows;

use InvalidArgumentException;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Workflows\Contracts\WorkflowHandlerInterface;

class WorkflowResolver
{
    /** @var array<string, class-string<WorkflowHandlerInterface>> */
    protected array $handlers = [
        DynamicService::CATEGORY_TRAINING => TrainingWorkflowHandler::class,
        DynamicService::CATEGORY_ASSISTANCE => AssistanceWorkflowHandler::class,
        DynamicService::CATEGORY_SOCIAL_PROGRAMS => SocialProgramsWorkflowHandler::class,
        DynamicService::CATEGORY_SURGICAL_PROCEDURES => SurgicalProceduresWorkflowHandler::class,
        DynamicService::CATEGORY_DETECTION_CENTER => DetectionCenterWorkflowHandler::class,
    ];

    public function resolve(DynamicService $service): WorkflowHandlerInterface
    {
        $category = $service->category;

        if (! isset($this->handlers[$category])) {
            throw new InvalidArgumentException("No workflow handler registered for category [{$category}].");
        }

        return app($this->handlers[$category]);
    }

    public function resolveByCategory(string $category): WorkflowHandlerInterface
    {
        if (! isset($this->handlers[$category])) {
            throw new InvalidArgumentException("No workflow handler registered for category [{$category}].");
        }

        return app($this->handlers[$category]);
    }
}
