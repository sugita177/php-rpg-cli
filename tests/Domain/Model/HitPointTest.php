<?php

namespace Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\Model\HitPoint; // まだ存在しないクラス

class HitPointTest extends TestCase
{
    /**
     * @test
     * 初期化時に現在値と最大値が正しく設定されるべき
     */
    public function initial_values_should_be_set_correctly(): void
    {
        $hp = new HitPoint(100, 100);
        
        $this->assertSame(100, $hp->getCurrentValue());
        $this->assertSame(100, $hp->getMaxValue());
    }
    
    /**
     * @test
     * ダメージを受けたときに現在値が減少し、新しいオブジェクトが返されるべき (不変性の確認)
     */
    public function decreasing_hp_should_return_new_object_and_reduce_value(): void
    {
        $initialHp = new HitPoint(100, 100);
        $damage = 30;
        
        // HPが減少した新しいインスタンスを取得
        $newHp = $initialHp->decrease($damage);
        
        // 1. 新しいオブジェクトであること (不変性の確認)
        $this->assertNotSame($initialHp, $newHp);
        
        // 2. HPが正しく減少していること
        $this->assertSame(70, $newHp->getCurrentValue());
        
        // 3. 元のオブジェクトが変更されていないこと
        $this->assertSame(100, $initialHp->getCurrentValue());
    }

    /**
     * @test
     * HPが0未満にならないこと（下限のガード）
     */
    public function hp_should_not_go_below_zero(): void
    {
        $hp = new HitPoint(10, 100);
        
        // 10ダメージ以上の大ダメージを与える
        $newHp = $hp->decrease(50); 
        
        $this->assertSame(0, $newHp->getCurrentValue());
    }
}