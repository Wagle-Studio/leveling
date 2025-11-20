<?php

namespace App\Libs\Ai;

interface AiProviderInterface
{
    public function send(string $systemInstruction, string $userInstruction): object;

    public function getName(): string;
}
