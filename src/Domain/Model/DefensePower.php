<?php

namespace App\Domain\Model;

// 値オブジェクトとして不変（Immutable）に設計
class DefensePower
{
    public function __construct(
        private readonly int $value
    ) {
        if ($this->value < 0) {
            throw new \InvalidArgumentException("Defense power must be a positive integer.");
        }
    }

    public function getValue(): int{
        return $this->value;
    }

}