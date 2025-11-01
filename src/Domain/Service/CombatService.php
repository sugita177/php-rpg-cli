<?php

namespace App\Domain\Service;

use App\Domain\Model\Character;

class CombatService
{
    // コンストラクタでDamageCalculatorへの依存を受け取る
    public function __construct(
        private readonly DamageCalculator $damageCalculator
    ) {}

    /**
     * キャラクターAがキャラクターBを攻撃する一連の処理を実行する
     */
    public function executeAttack(Character $attacker, Character $target): void
    {
        // 1. ダメージ計算サービスを利用してダメージ量を決定
        $damageAmount = $this->damageCalculator->calculate(
            $attacker->getAttackPower(),
            $target->getDefensePower()
        );

        // 2. ターゲットエンティティの振る舞いを呼び出し、状態を変更
        //    (ターゲット自身がダメージを受けるロジックを持っている)
        $target->receiveDamage($damageAmount);

        // 拡張性: ここでログ出力やクリティカル判定などのロジックを追加できる
    }
}