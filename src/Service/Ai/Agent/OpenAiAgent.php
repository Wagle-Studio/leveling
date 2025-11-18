<?php

namespace App\Service\Ai\Agent;

use App\Service\Ai\Prompt\PromptInterface;
use OpenAI;
use OpenAI\Client;

class OpenAiAgent
{
    private readonly Client $client;

    public function __construct()
    {
        $this->client = OpenAI::client($_ENV['OPENAI_API_KEY']);
    }

    public function send(PromptInterface $prompt): object
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-5-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $prompt->getSystemInstructions()
                ],
                [
                    'role' => 'user',
                    'content' => $prompt->getUserInstructions()
                ]
            ],
            'response_format' => [
                'type' => 'json_object'
            ],
        ]);

        return json_decode($response['choices'][0]['message']['content']);
    }
}
