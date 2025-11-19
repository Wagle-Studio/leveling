<?php

namespace App\Libs\Conversation\Context;

interface ContextInterface {
    public function initialize(...$params): void;
}
