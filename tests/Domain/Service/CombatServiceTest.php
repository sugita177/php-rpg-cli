<?php

namespace Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\CombatService;
use App\Domain\Service\DamageCalculator;
use App\Domain\Model\Character;
use App\Domain\Model\HitPoint;
use App\Domain\Model\AttackPower;
use App\Domain\Model\DefensePower;

class CombatServiceTest extends TestCase
{
    private CombatService $combatService;
    private DamageCalculator $damageCalculator;

    protected function setUp(): void
    {
        // 依存関係を準備
        $this->damageCalculator = new DamageCalculator();
        $this->combatService = new CombatService($this->damageCalculator);
    }

    /**
     * @test
     * 攻撃実行後、ターゲットのHPが正しく減少するべき
     */
    public function target_hp_should_be_decreased_after_attack_execution(): void
    {
        $hp = new HitPoint(10, 10);
        $attackPower = new AttackPower(15);
        $defensePower = new DefensePower(8);
        $character = new Character('id-03', 'attacker', $hp, $attackPower, $defensePower);

        $targetHp = new HitPoint(100, 100);
        $targetAttackPower = new AttackPower(11);
        $targetDefensePower = new DefensePower(12);
        $targetCharacter = new Character('id-04', 'target', $targetHp, $targetAttackPower, $targetDefensePower);
        $calculator = new DamageCalculator();

        $attacker = new Character('id-03', 'attacker', $hp, $attackPower, $defensePower);
        $target = new Character('id-04', 'target', $targetHp, $targetAttackPower, $targetDefensePower);

        // CombatService がロジックを実行
        $this->combatService->executeAttack($attacker, $target);

        // 期待値: 100 - (15 - 12) = 97
        $this->assertSame(97, $target->getCurrentHp());
    }
    
    /**
     * @test
     * 攻撃力が防御力より低い場合でも、ダメージは必ず1以上となるべき (最低保証ダメージ)
     */
    public function damage_should_always_be_at_least_one(): void
    {
        // 攻撃力 < 防御力 となるように設定
        $attackerAttack = new AttackPower(10);
        $defenderDefense = new DefensePower(50);
        $targetHp = new HitPoint(100, 100);

        // 攻撃側と防御側キャラクターの準備（ダミーのHPや防御力で初期化）
        $attacker = new Character('id-A', 'Overwhelmed', $targetHp, $attackerAttack, new DefensePower(1));
        $target = new Character('id-T', 'Tank', $targetHp, new AttackPower(1), $defenderDefense);

        // サービス実行
        $this->combatService->executeAttack($attacker, $target);

        // 期待値: DamageCalculator のロジックにより、ダメージは (10 - 50) = -40 ではなく 1 となる
        $this->assertSame(99, $target->getCurrentHp()); 
    }

    /**
     * @test
     * 攻撃によりHPがゼロになった場合、ターゲットのisAbleToBattle()がfalseとなるべき
     */
    public function target_should_be_dead_after_fatal_damage(): void
    {
        // 10ダメージで死亡するよう設定
        $attackerAttack = new AttackPower(20);
        $defenderDefense = new DefensePower(10);
        $targetHp = new HitPoint(10, 10); // 現在HP 10

        $attacker = new Character('id-X', 'Killer', $targetHp, $attackerAttack, new DefensePower(1));
        $target = new Character('id-Y', 'Victim', $targetHp, new AttackPower(1), $defenderDefense);
        
        // 期待ダメージ: 20 - 10 = 10 (致命傷)
        $this->combatService->executeAttack($attacker, $target);

        // 1. HPが0になっていること
        $this->assertSame(0, $target->getCurrentHp());

        // 2. 戦闘可能状態がfalseになっていること
        $this->assertFalse($target->isAbleToBattle()); 
    }
}