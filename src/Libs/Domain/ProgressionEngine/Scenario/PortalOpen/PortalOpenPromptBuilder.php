<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\PortalOpen;

use App\Entity\Objective;

final class PortalOpenPromptBuilder
{
    private string $systemInstruction;
    private string $userInstruction;

    public function initialize(Objective $objective): void
    {
        $duration = $objective->getDuration();
        $objectiveLabel = $objective->getLabel();
        $skillLabel = $objective->getSkill()->getLabel();

        $branchLabels = [];
        $domainLabels = [];

        foreach ($objective->getSkill()->getBranches() as $branch) {
            $branchLabels[] = $branch->getLabel();

            foreach ($branch->getDomains() as $domain) {
                $domainLabels[] = $domain->getLabel();
            }
        }

        $branchLabels = array_values(array_unique($branchLabels));
        $domainLabels = array_values(array_unique($domainLabels));

        $this->systemInstruction = $this->buildSystemInstructions(
            $duration,
            $objectiveLabel,
            $skillLabel,
            $branchLabels,
            $domainLabels
        );
        $this->userInstruction = $this->buildUserInstructions(
            $duration,
            $skillLabel
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
            "steps" => [
                'label' => 'string',
                'instruction' => 'string',
            ],
        ];
    }

    private function buildSystemInstructions(int $duration, string $objectiveLabel, string $skillLabel, array $branchLabels, array $domainLabels): string
    {
        $instructions = [
            [
                'instruction' => 'You are a specialist assistant with expertise in the following domains: "%s".',
                'values' => implode(', ', $domainLabels),
            ],
            [
                'instruction' => 'You are recognized as a specialist in the following branches: "%s".',
                'values' => implode(', ', $branchLabels),
            ],
            [
                'instruction' => 'You contribute by helping to develop the skill: "%s".',
                'values' => $skillLabel,
            ],
            [
                'instruction' => 'You design a personalized learning path in exactly "%1$d" steps. The number of steps also represents the number of days of the learning plan: you must produce exactly "%1$d" daily steps, one per day, each representing a concrete actionable task.',
                'values' => $duration,
            ],
            [
                'instruction' => 'You must respond in fluent French, with a clear, encouraging and pedagogical tone, suitable for a beginner to intermediate learner.',
            ],
            [
                'instruction' => 'You must answer using ONLY a single valid JSON object, with no additional text before or after.',
            ],
            [
                'instruction' => 'The JSON object MUST contain a single top-level array of exactly "%d" steps.',
                'values' => $duration,
            ],
            [
                'instruction' => 'Each element of the array MUST contain: "label" (short and explicit title, max 80 characters, with NO reference to days or numbering) and "instruction" (a very short, direct and actionable task description, written like a todo item). Each step must describe one single concrete task that directly contributes to accomplishing the objective.',
            ],
            [
                'instruction' => 'The user\'s objective is: "%s". This objective is a fixed and final target for the learning plan. You must NEVER change, extend or reinterpret this objective. The last step must correspond exactly to achieving this objective, and all previous steps are intermediate stages that progressively prepare for it without ever going beyond it.',
                'values' => $objectiveLabel,
            ],
            [
                'instruction' => 'You must break down the objective into exactly "%1$d" progressive steps, starting clearly below the final level and increasing difficulty or intensity gradually. The first step must be significantly easier than the objective, intermediate steps must form a smooth progression, and only the last step may reach the full level described by the objective.',
                'values' => $duration,
            ],
            [
                'instruction' => 'If the objective contains quantitative elements (for example time, distance, number of repetitions, level, volume, etc.), you must interpret them as hard maximum limits. No step may exceed these quantitative limits, including the last step.',
            ],
            [
                'instruction' => 'You must not ask the user to choose a new objective or suggest a more ambitious target. You must always build the plan strictly toward the objective that is already defined.',
            ],
            [
                'instruction' => 'You may propose complementary or related tasks (for example: watching a short video, reading an article, stretching or mobility work, rest or recovery days), as long as each step remains a concrete, actionable contribution toward the user\'s objective.',
            ],
            [
                'instruction' => 'You strictly adhere to the expected JSON shape, similar to: "%s".',
                'values' => json_encode($this->getOutputSchema()),
            ],
        ];

        return $this->formatInstructions($instructions);
    }

    private function buildUserInstructions(int $duration, string $skillLabel): string
    {
        $instructions = [
            [
                'instruction' => 'The user would like to develop the following skill: "%s".',
                'values' => $skillLabel,
            ],
            [
                'instruction' => 'The user needs a "%d"-step action plan to develop this skill.',
                'values' => $duration,
            ],
            [
                'instruction' => 'The action plan must follow a pedagogical progression, starting from simple and accessible steps and gradually increasing difficulty while remaining realistic and within the boundaries of the user\'s objective.',
            ],
        ];

        return $this->formatInstructions($instructions);
    }

    private function formatInstructions(array $instructions): string
    {
        $formattedInstructions = [];

        foreach ($instructions as $instruction) {
            $formattedInstructions[] = isset($instruction['values'])
                ? sprintf($instruction['instruction'], $instruction['values'])
                : $instruction['instruction'];
        }

        return implode(' ', $formattedInstructions);
    }
}
