<?php

namespace App\Domain\Model;

// 値オブジェクトとして不変 (Immutable) に設計
class HitPoint
{
    // PHP 8.4 での コンストラクタプロパティプロモーションを活用
    public function __construct(
        private readonly int $currentValue,
        private readonly int $maxValue
    ) {
        // コンストラクタで簡単な整合性チェック
        if ($this->currentValue < 0 || $this->maxValue <= 0) {
            throw new \InvalidArgumentException("HP must be positive.");
        }
    }

    // 💡 振る舞い（HP減少）
    public function decrease(int $damage): self
    {
        // 0未満にならないように調整
        $newHp = max(0, $this->currentValue - $damage);
        
        // 不変性を守るため、新しいインスタンスを生成して返す
        return new self($newHp, $this->maxValue);
    }
    
    // 💡 ゲッター
    public function getCurrentValue(): int
    {
        return $this->currentValue;
    }

    public function getMaxValue(): int
    {
        return $this->maxValue;
    }
}