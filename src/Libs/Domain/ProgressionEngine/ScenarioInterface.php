<?php

namespace App\Libs\Domain\ProgressionEngine;

interface ScenarioInterface
{
    public function run(object $payload): void;
}
