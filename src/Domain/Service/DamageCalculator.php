<?php

namespace App\Domain\Service;

use App\Domain\Model\AttackPower;
use App\Domain\Model\DefensePower;

// ドメインサービス: 複数の値オブジェクトやエンティティにまたがる計算ロジック
class DamageCalculator
{
    /**
     * 攻撃力と防御力から最終的なダメージ量を計算する
     */
    public function calculate(AttackPower $attack, DefensePower $defense): int
    {
        // 攻撃値から防御値を引く
        $baseDamage = $attack->getValue() - $defense->getValue();
        
        // 💡 ドメインルール: ダメージは必ず 1 以上である（最低保証ダメージ）
        // テストで要求されたルールを実装します
        if ($baseDamage < 1) {
            return 1;
        }

        return $baseDamage;
    }
}