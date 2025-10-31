<?php

namespace Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\Model\AttackPower;

class AttackPowerTest extends TestCase
{
    /**
     * @test
     * 攻撃力は正の整数で初期化されるべき
     */
    public function attack_power_should_be_initialized_with_positive_value(): void
    {
        $power = new AttackPower(50);
        
        $this->assertSame(50, $power->getValue());
    }

    /**
     * @test
     * 攻撃力が負の値またはゼロの場合、例外を投げるべき
     */
    public function exception_should_be_thrown_if_value_is_not_positive(): void
    {
        // 期待される例外を指定
        $this->expectException(\InvalidArgumentException::class);
        
        // 攻撃力がゼロの場合
        new AttackPower(0);
        
        // 💡 負の値のテストも追加する場合、別のテストメソッドに分けるか、
        // Data Providerを使用するのがベストプラクティスです。今回はシンプルに。
    }

    /**
     * @test
     * 値オブジェクトは不変であるべき (振る舞いがないため、インスタンスが常に同じであること)
     */
    public function attack_power_is_immutable(): void
    {
        $power1 = new AttackPower(10);
        $power2 = $power1; // 同じ参照
        
        $this->assertSame($power1, $power2);
    }
}