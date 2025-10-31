<?php

namespace Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\DamageCalculator;
use App\Domain\Model\AttackPower;
use App\Domain\Model\DefensePower;

class DamageCalculatorTest extends TestCase
{
    private DamageCalculator $calculator;

    protected function setUp(): void
    {
        // サービスの実装を初期化
        $this->calculator = new DamageCalculator();
    }

    /**
     * @test
     * 基本的なダメージ計算 (攻撃力 - 防御力) が正しく行われるべき
     */
    public function basic_damage_calculation_should_be_attack_minus_defense(): void
    {
        $attackerPower = 50;
        $defenderDefense = 20;

        // 攻撃側と防御側の値オブジェクト (AttackPower, DefensePower) を作成
        // (テスト駆動なので、これらのクラスはまだ存在しなくても良い)
        $attack = new AttackPower($attackerPower);
        $defense = new DefensePower($defenderDefense);

        // サービスに計算を依頼
        $damage = $this->calculator->calculate($attack, $defense);
        
        // 期待値: 50 - 20 = 30
        $this->assertSame(30, $damage);
    }
    
    /**
     * @test
     * ダメージは必ず1以上であるべき (最低保証ダメージ)
     */
    public function damage_should_always_be_at_least_one(): void
    {
        $attackerPower = 10;
        $defenderDefense = 100; // 防御力が高い

        $attack = new AttackPower($attackerPower);
        $defense = new DefensePower($defenderDefense);

        // 期待値: 10 - 100 = -90 ではなく、最低ダメージの 1 
        $damage = $this->calculator->calculate($attack, $defense);
        
        $this->assertSame(1, $damage);
    }
}