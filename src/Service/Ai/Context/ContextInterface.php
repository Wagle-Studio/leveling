<?php

namespace App\Service\Ai\Context;

interface ContextInterface {
    public function initialize(...$params): void;
}
