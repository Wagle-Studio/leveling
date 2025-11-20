<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\QuestOpen;

use App\Entity\Step;

final class QuestOpenPromptBuilder
{
    private string $systemInstruction;
    private string $userInstruction;

    public function initialize(Step $step): void
    {
        $objective = $step->getObjective();
        $duration = $objective->getDuration();
        $objectiveLabel = $objective->getLabel();
        $skill = $objective->getSkill();
        $skillLabel = $skill->getLabel();
        $stepLabel = $step->getLabel();
        $stepInstruction = $step->getInstruction();

        $branchLabels = [];
        $domainLabels = [];

        foreach ($skill->getBranches() as $branch) {
            $branchLabels[] = $branch->getLabel();

            foreach ($branch->getDomains() as $domain) {
                $domainLabels[] = $domain->getLabel();
            }
        }

        $branchLabels = array_values(array_unique($branchLabels));
        $domainLabels = array_values(array_unique($domainLabels));

        $this->systemInstruction = $this->buildSystemInstructions(
            $objectiveLabel,
            $skillLabel,
            $branchLabels,
            $domainLabels
        );

        $this->userInstruction = $this->buildUserInstructions(
            $objectiveLabel,
            $skillLabel,
            $branchLabels,
            $domainLabels,
            $duration,
            $stepLabel,
            $stepInstruction
        );
    }

    public function getSystemInstructions(): string
    {
        return $this->systemInstruction;
    }

    public function getUserInstructions(): string
    {
        return $this->userInstruction;
    }

    private function getOutputSchema(): array
    {
        return [
            'id' => 'string',
            'step_id' => 'string',
            'before_label' => 'string',
            'before_scene' => 'string',
            'success_label' => 'string',
            'success_scene' => 'string',
            'failure_label' => 'string',
            'failure_scene' => 'string',
            'created_at' => 'string',
        ];
    }

    private function buildSystemInstructions(
        string $objectiveLabel,
        string $skillLabel,
        array $branchLabels,
        array $domainLabels
    ): string {
        $instructions = [
            [
                'instruction' => 'You are an assistant specialized in simple, clear and direct narrative writing inspired by gamebooks. You work in the following domains: "%s".',
                'values' => implode(', ', $domainLabels),
            ],
            [
                'instruction' => 'You also understand the following branches: "%s".',
                'values' => implode(', ', $branchLabels),
            ],
            [
                'instruction' => 'You help the user progress in the skill: "%s", by generating short narrative scenes around real-life actions.',
                'values' => $skillLabel,
            ],
            [
                'instruction' => 'The user\'s current objective is: "%s". You must NEVER modify, extend or reinterpret this objective.',
                'values' => $objectiveLabel,
            ],
            [
                'instruction' => 'Write in fluent French, with a simple and direct tone. Use everyday vocabulary. Avoid metaphors, poetic images, literary style and abstract expressions.',
            ],
            [
                'instruction' => 'Use the second person singular ("tu") to address the user.',
            ],
            [
                'instruction' => 'Each scene must be a short paragraph between 40 and 90 words, with short and clear sentences.',
            ],
            [
                'instruction' => 'You must answer ONLY with a single valid JSON object, with no text before or after.',
            ],
            [
                'instruction' => 'The JSON object MUST contain exactly the following keys at the top level: "id", "step_id", "before_label", "before_scene", "success_label", "success_scene", "failure_label", "failure_scene", "created_at".',
            ],
            [
                'instruction' => '"before_label" is a short title (max 80 characters) for the scene BEFORE the action.',
            ],
            [
                'instruction' => '"before_scene" is the paragraph describing the moment just before the user decides to do or not do the step. It must stay concrete and realistic.',
            ],
            [
                'instruction' => '"success_label" is a short title (max 80 characters) for the scene IF the user completes the step.',
            ],
            [
                'instruction' => '"success_scene" is the paragraph describing what happens IF the user completes the step: progress, better understanding, small feeling of satisfaction. No exaggeration, no drama.',
            ],
            [
                'instruction' => '"failure_label" is a short title (max 80 characters) for the scene IF the user does NOT complete the step.',
            ],
            [
                'instruction' => '"failure_scene" is the paragraph describing what happens IF the user does NOT complete the step: slower progression, small frustration, missed practice opportunity. No moral judgement. Stay factual and kind.',
            ],
            [
                'instruction' => '"id", "step_id" and "created_at" are technical fields. You MUST still include them in the JSON object, but you must set their value to null.',
            ],
            [
                'instruction' => 'Stick strictly to the provided domain(s), branch(es), skill and objective. Do NOT invent new objectives or different types of actions.',
            ],
            [
                'instruction' => 'Follow strictly this JSON shape example: "%s".',
                'values' => json_encode($this->getOutputSchema()),
            ],
        ];

        return $this->formatInstructions($instructions);
    }

    private function buildUserInstructions(
        string $objectiveLabel,
        string $skillLabel,
        array $branchLabels,
        array $domainLabels,
        int $duration,
        string $stepLabel,
        string $stepInstruction
    ): string {
        $instructions = [
            [
                'instruction' => 'The user is progressing in the following domain(s): "%s".',
                'values' => implode(', ', $domainLabels),
            ],
            [
                'instruction' => 'Within these domain(s), the user is currently in the branch(es): "%s".',
                'values' => implode(', ', $branchLabels),
            ],
            [
                'instruction' => 'The user is developing the skill: "%s".',
                'values' => $skillLabel,
            ],
            [
                'instruction' => 'The objective associated with this skill is: "%s".',
                'values' => $objectiveLabel,
            ],
            [
                'instruction' => 'The overall learning plan for this objective is spread over "%d" day(s).',
                'values' => $duration,
            ],
            [
                'instruction' => 'You now focus on ONE specific step of this plan. The label of this step is: "%s".',
                'values' => $stepLabel,
            ],
            [
                'instruction' => 'The concrete real-life instruction for this step is: "%s". This is exactly the action that the user may or may not perform.',
                'values' => $stepInstruction,
            ],
            [
                'instruction' => 'Based on this context, generate the four narrative fields: "before_label" and "before_scene" (before the action), "success_label" and "success_scene" (if the action is done), "failure_label" and "failure_scene" (if the action is not done).',
            ],
            [
                'instruction' => 'The scenes must stay strictly focused on this single step and on its immediate effects. Do NOT anticipate future steps and do NOT change the defined objective.',
            ],
            [
                'instruction' => 'Remember: "id", "step_id" and "created_at" must be present in the JSON but set to null. The backend system will replace them later.',
            ],
        ];

        return $this->formatInstructions($instructions);
    }

    private function formatInstructions(array $instructions): string
    {
        $formattedInstructions = [];

        foreach ($instructions as $instruction) {
            if (isset($instruction['values'])) {
                $values = is_array($instruction['values']) ? $instruction['values'] : [$instruction['values']];
                $formattedInstructions[] = sprintf($instruction['instruction'], ...$values);
            } else {
                $formattedInstructions[] = $instruction['instruction'];
            }
        }

        return implode(' ', $formattedInstructions);
    }
}
