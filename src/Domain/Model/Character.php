<?php

namespace App\Domain\Model;

use App\Domain\Service\DamageCalculator;

// Characterはアグリゲートルート（エンティティ）であり、同一性を持つ
class Character
{
    // PHP 8.4のプロパティプロモーションを活用
    public function __construct(
        private readonly string $id, // 永続的な同一性を保つためのID (read-only)
        private readonly string $name,
        private HitPoint $hp, // HPは振る舞いによって変更される可能性があるため、read-onlyではない
        private readonly AttackPower $attackPower,
        private readonly DefensePower $defensePower,
    ) {}

    // 振る舞い 1: ダメージを受ける
    public function receiveDamage(int $damage): self
    {
        // HitPointオブジェクトの不変性を活用し、新しいHP値オブジェクトを取得
        $newHp = $this->hp->decrease($damage);
        
        // エンティティの可変性: 自身のプロパティを新しい値オブジェクトで置き換える
        $this->hp = $newHp;

        // 同一性を保つため、自分自身（$this）を返す
        return $this;
    }

    // 振る舞い 2: 戦闘可能状態の確認
    public function isAbleToBattle(): bool
    {
        return $this->hp->getCurrentValue() > 0;
    }
    
    // ゲッター (テストで必要)
    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    // テストがHPの現在値を簡単に確認できるようにするヘルパー
    public function getCurrentHp(): int
    {
        return $this->hp->getCurrentValue();
    }

    public function getAttackPower(): AttackPower
    {
        return $this->attackPower;
    }

    public function getDefensePower(): DefensePower
    {
        return $this->defensePower;
    }

    public function attack(Character $target, DamageCalculator $calculator): void
    {
        $damage = $calculator->calculate(
            $this->getAttackPower(),
            $target->getDefensePower()
        );

        $target->receiveDamage($damage);
    }
}