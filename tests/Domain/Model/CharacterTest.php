<?php

namespace Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\Model\Character;
use App\Domain\Model\HitPoint;
use App\Domain\Model\AttackPower;
use App\Domain\Model\DefensePower;
use App\Domain\Service\DamageCalculator;


class CharacterTest extends TestCase
{
    /**
     * @test
     * キャラクターは名前、ID、HP値オブジェクトで初期化されるべき
     */
    public function character_should_be_initialized_with_name_id_and_hitpoint(): void
    {
        $id = 'player-123';
        $name = '勇者';
        $attackPower = new AttackPower(15);
        $defensePower = new DefensePower(8);
        $hp = new HitPoint(100, 100);

        // 厳密なDDDでは、エンティティのIDは必須
        $character = new Character($id, $name, $hp, $attackPower, $defensePower);

        $this->assertSame($id, $character->getId());
        $this->assertSame($name, $character->getName());
        $this->assertSame(100, $character->getCurrentHp()); // Helperメソッドを作成予定
        $this->assertTrue($character->isAbleToBattle());
    }

    /**
     * @test
     * ダメージを受けたとき、HPが減少し、同じエンティティのインスタンスであるべき（同一性の維持）
     */
    public function receiving_damage_should_reduce_hp_but_maintain_identity(): void
    {
        $hp = new HitPoint(100, 100);
        $attackPower = new AttackPower(15);
        $defensePower = new DefensePower(8);
        $character = new Character('id-01', '敵スライム', $hp, $attackPower, $defensePower);

        // ダメージを与える振る舞い
        $newCharacter = $character->receiveDamage(30);

        // 1. 同一性の確認 (DDD エンティティの重要要件)
        $this->assertSame($character, $newCharacter);

        // 2. HPが減少していること
        $this->assertSame(70, $character->getCurrentHp());
    }

    /**
     * @test
     * HPが0以下になったとき、isAbleToBattle() が false を返すこと
     */
    public function character_should_be_unable_to_battle_when_hp_reaches_zero(): void
    {
        $hp = new HitPoint(10, 10);
        $attackPower = new AttackPower(15);
        $defensePower = new DefensePower(8);
        $character = new Character('id-02', '瀕死の敵', $hp, $attackPower, $defensePower);
        
        // 戦闘不能になるダメージ
        $character->receiveDamage(10); 

        $this->assertSame(0, $character->getCurrentHp());
        $this->assertFalse($character->isAbleToBattle());
    }

    /**
     * @test
     * 攻撃を行ったときに対象のHPが正しく変更されている
     */
    public function target_hp_should_be_decreased_when_character_atack(): void
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

        // 攻撃を行う
        $character->attack($targetCharacter, $calculator);

        // 対象のHP = 攻撃される前のHP - (攻撃者の攻撃力 - 対象の防御力)
        // 97 = 100 - (15 - 12)
        $this->assertSame(97, $targetCharacter->getCurrentHp());
    }
}