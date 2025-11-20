<?php

namespace App\Libs\Ai\Agent;

use App\Libs\Ai\AiProviderInterface;
use OpenAI;
use OpenAI\Client;

class OpenAiAgent implements AiProviderInterface
{
    private readonly Client $client;

    public function __construct()
    {
        $this->client = OpenAI::client($_ENV['OPENAI_API_KEY']);
    }

    public function getName(): string
    {
        return 'OpenAI';
    }

    public function send(string $systemInstruction, string $userInstruction): object
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-5-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemInstruction
                ],
                [
                    'role' => 'user',
                    'content' => $userInstruction
                ]
            ],
            'response_format' => [
                'type' => 'json_object'
            ],
        ]);

        return json_decode($response['choices'][0]['message']['content']);
    }
}
