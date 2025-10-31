<?php

namespace Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\Model\Character; // まだ存在しない
use App\Domain\Model\HitPoint;  // 既に実装済み

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
        $hp = new HitPoint(100, 100);

        // 厳密なDDDでは、エンティティのIDは必須
        $character = new Character($id, $name, $hp);

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
        $character = new Character('id-01', '敵スライム', $hp);

        // ダメージを与える振る舞い
        $newCharacter = $character->receiveDamage(30);

        // 1. 同一性の確認 (DDD エンティティの重要要件)
        $this->assertSame($character, $newCharacter);

        // 2. HPが減少していること
        $this->assertSame(70, $character->getCurrentHp());
    }

    /**
     * @test
     * HPが0以下になったとき、isAlive() が false を返すこと
     */
    public function character_should_be_dead_when_hp_reaches_zero(): void
    {
        $hp = new HitPoint(10, 10);
        $character = new Character('id-02', '瀕死の敵', $hp);
        
        // 戦闘不能になるダメージ
        $character->receiveDamage(10); 

        $this->assertSame(0, $character->getCurrentHp());
        $this->assertFalse($character->isAbleToBattle());
    }
}