<?php

namespace App\Domain\Core\Interface;

interface ScenarioInterface
{
    public function run(object $payload): void;
}
