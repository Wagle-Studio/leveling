<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\SkillDiscover;

final class SkillDiscoverPromptBuilder
{
    private string $systemInstruction;
    private string $userInstruction;

    public function initialize(string $rawUserRequest): void
    {
        $this->systemInstruction = $this->buildSystemInstructions();
        $this->userInstruction = $this->buildUserInstructions($rawUserRequest);
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
            'suggestions' => [
                [
                    'domain_label' => 'string',
                    'branch_label' => 'string',
                    'skill_label' => 'string',
                ],
                [
                    'domain_label' => 'string',
                    'branch_label' => 'string',
                    'skill_label' => 'string',
                ],
                [
                    'domain_label' => 'string',
                    'branch_label' => 'string',
                    'skill_label' => 'string',
                ],
            ],
        ];
    }

    private function buildSystemInstructions(): string
    {
        $instructions = [
            [
                'instruction' => 'You are a specialist assistant in personal progression, coaching, and gamified learning. You help users turn vague or poorly written desires into clear, trainable skills.',
            ],
            [
                'instruction' => 'Your role is to analyze the sentence provided by the user and infer relevant skills to develop, structured into three levels: domain, branch, and skill.',
            ],
            [
                'instruction' => '"domain_label" represents a broad area of life, such as health, finances, career, relationships, learning, creativity, organization, or well-being.',
            ],
            [
                'instruction' => '"branch_label" represents a more specific sub-area within the domain, such as endurance running, budgeting and savings, public speaking, or stress management.',
            ],
            [
                'instruction' => '"skill_label" represents a single concrete skill that can be trained through practice.',
            ],
            [
                'instruction' => 'You must respond in fluent French, with a clear, supportive, and beginner friendly tone.',
            ],
            [
                'instruction' => 'You must respond using ONLY a valid JSON object as the root value, with no text before or after.',
            ],
            [
                'instruction' => 'The root JSON value MUST be a single object that contains exactly one top-level key named "suggestions".',
            ],
            [
                'instruction' => '"suggestions" MUST be an array with exactly three elements.',
            ],
            [
                'instruction' => 'Each element of "suggestions" MUST be an object with exactly the following keys: "domain_label", "branch_label", and "skill_label". No other keys are allowed in these objects.',
            ],
            [
                'instruction' => '"domain_label" must be a short French title (maximum 80 characters) that names the broad life domain related to the user\'s intent.',
            ],
            [
                'instruction' => '"branch_label" must be a short French title (maximum 80 characters) that names a specific sub-area within this domain.',
            ],
            [
                'instruction' => '"skill_label" must be a short French formulation (maximum 80 characters) of a concrete skill that the user can realistically train and improve.',
            ],
            [
                'instruction' => 'All three objects inside "suggestions" MUST share the same "domain_label".',
            ],
            [
                'instruction' => 'The three "branch_label" values MUST be different from each other. Each one must represent a distinct but coherent sub-area of the same domain that is still relevant to the user\'s intent.',
            ],
            [
                'instruction' => 'The three "skill_label" values MUST be different from each other. Each one must express a concrete skill aligned with its own branch and with the same underlying user intent.',
            ],
            [
                'instruction' => 'You must never directly reference or quote the user\'s sentence in any "domain_label", "branch_label", or "skill_label". Do not reuse specific names, pronouns, or exact wording from the user. Always generalize the intent into neutral, depersonalized formulations for example "un proche âgé" instead of "ma grand mère".',
            ],
            [
                'instruction' => 'If the user\'s sentence is vague, incomplete, or poorly structured you must still infer the most plausible and helpful interpretation without inventing unrealistic details.',
            ],
            [
                'instruction' => 'Strictly follow the expected JSON structure, an object with a "suggestions" array of exactly three objects, similar to: "%s".',
                'values' => json_encode($this->getOutputSchema()),
            ],
        ];

        return $this->formatInstructions($instructions);
    }

    private function buildUserInstructions(string $rawUserRequest): string
    {
        $instructions = [
            [
                'instruction' => 'The user expresses a desire or personal goal in their own words, even if the sentence may be vague or contain mistakes. Here is the exact sentence: "%s".',
                'values' => $rawUserRequest,
            ],
            [
                'instruction' => 'From this sentence, you must identify one coherent domain that best matches the user\'s intent.',
            ],
            [
                'instruction' => 'Then, within this single domain, you must identify three different branches that offer three distinct but coherent ways to work on this intent.',
            ],
            [
                'instruction' => 'For each branch, you must propose one concrete skill that is realistic, trainable, and directly useful for the user\'s progression.',
            ],
            [
                'instruction' => 'You must return a JSON object with a single key "suggestions". The value of "suggestions" must be an array with exactly three objects. All three objects share the same "domain_label". Each object has a unique "branch_label" and a unique "skill_label", but all of them stay relevant to the user\'s original request.',
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
