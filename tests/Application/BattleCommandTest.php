<?php

namespace Tests\Application;

use PHPUnit\Framework\TestCase;
use App\Application\BattleCommand;
use App\Domain\Service\CombatService;

class BattleCommandTest extends TestCase
{
    /**
     * @test
     * コマンドはCombatServiceを呼び出し、バトル結果を出力すべき
     */
    public function command_should_call_combat_service_and_output_result(): void
    {
        // 1. 外部依存のモックアップ
        
        // 💡 CombatServiceのモック: 実際に戦闘ロジックを実行する代わりに、
        // 呼び出しが行われたことだけを検証するためのダミーオブジェクト
        $mockCombatService = $this->createMock(CombatService::class);
        
        // 💥 期待する動作を定義 💥
        // execute()が呼び出されたことをアサートする
        $mockCombatService->expects($this->once())
                          ->method('executeAttack');
        
        // 2. コマンドの初期化
        // 実際には、CLIからの入出力を担当するI/Oインターフェースも必要ですが、
        // 今回はシンプルに、ドメインサービスへの依存のみを注入します。
        $command = new BattleCommand($mockCombatService);

        // 3. テストの実行
        // 実際には、キャラクター情報（IDなど）を入力として受け取ります
        $command->execute('player-1', 'monster-1');

        // 💡 出力検証 (今回は一旦スキップし、ロジックの呼び出し検証に焦点を当てます)
        // $this->assertStringContainsString('戦闘終了！', $output);
    }
}