<?php

namespace Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\Model\DefensePower; // まだ存在しない

class DefensePowerTest extends TestCase
{
    /**
     * @test
     * 防御力はゼロまたは正の整数で初期化されるべき
     */
    public function defense_power_should_be_initialized_with_zero_or_positive_value(): void
    {
        $power = new DefensePower(10);
        $this->assertSame(10, $power->getValue());
        
        // ゼロの防御力も有効であるべき
        $zeroPower = new DefensePower(0);
        $this->assertSame(0, $zeroPower->getValue());
    }

    /**
     * @test
     * 防御力が負の値の場合、例外を投げるべき
     */
    public function exception_should_be_thrown_if_value_is_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        // 負の値を渡してオブジェクトを作成
        new DefensePower(-1); 
    }
    
    /**
     * @test
     * 値オブジェクトは不変であるべき (振る舞いがないため、インスタンスが常に同じであること)
     */
    public function defense_power_is_immutable(): void
    {
        $power1 = new DefensePower(10);
        $power2 = $power1; // 同じ参照
        
        $this->assertSame($power1, $power2);
    }
}