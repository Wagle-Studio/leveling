<?php

namespace App\Libs\Domain\Core\Interface;

/**
 * @template T
 */
interface ServiceLocatorInterface
{
    /**
     * @param T $input
     */
    public function handle(object $input): void;
}
