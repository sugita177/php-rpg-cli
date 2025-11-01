<?php

namespace App\Domain\Model;

// 値オブジェクトとして不変 (Immutable) に設計
class AttackPower
{
    public function __construct(
        private readonly int $value
    ) {
        // 攻撃力が正の数であることの検証 (DDD: 整合性の確保)
        if ($this->value <= 0) {
            throw new \InvalidArgumentException("Attack power must be a positive integer.");
        }
    }

    // ゲッター
    public function getValue(): int
    {
        return $this->value;
    }
    
    // 値オブジェクトなので、内部の値を変更するメソッドは持たない
}