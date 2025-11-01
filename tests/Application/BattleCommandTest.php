<?php

namespace Tests\Application;

use PHPUnit\Framework\TestCase;
use App\Application\BattleCommand;
use App\Domain\Service\CombatService;
use App\Domain\Repository\CharacterRepositoryInterface; // 新しい依存
use App\Domain\Model\Character; // モックの戻り値用
use App\Domain\Model\{HitPoint, AttackPower, DefensePower}; // モック用

class BattleCommandTest extends TestCase
{
    private CombatService $mockCombatService;
    private CharacterRepositoryInterface $mockRepository;

    protected function setUp(): void
    {
        // 外部依存をモックで準備
        $this->mockCombatService = $this->createMock(CombatService::class);
        $this->mockRepository = $this->createMock(CharacterRepositoryInterface::class);
    }

    /**
     * @test
     * コマンドはリポジトリから両方のキャラクターをロードし、CombatServiceを呼び出すべき
     */
    public function command_should_load_characters_and_call_combat_service(): void
    {
        // 💡 ユーザー入力のシミュレーション
        $attackerId = 'player-1';
        $targetId = 'monster-1';

        // 💡 1. リポジトリのモック設定 (REDの状態を作るために重要なステップ)
        // キャラクターのダミーオブジェクトを作成
        $dummyAttacker = new Character($attackerId, 'A', new HitPoint(1, 1), new AttackPower(1), new DefensePower(1));
        $dummyTarget = new Character($targetId, 'T', new HitPoint(1, 1), new AttackPower(1), new DefensePower(1));
        
        // リポジトリが find() されたら、上記ダミーオブジェクトを返すように設定
        $this->mockRepository->expects($this->exactly(2))
                             ->method('find')
                             ->will($this->returnValueMap([
                                 [$attackerId, $dummyAttacker],
                                 [$targetId, $dummyTarget],
                             ]));

        // 💡 2. CombatService のモック設定
        // executeAttackが、上記ダミーオブジェクトを受け取って一度呼び出されることを期待
        $this->mockCombatService->expects($this->once())
                                ->method('executeAttack')
                                ->with($dummyAttacker, $dummyTarget);
        
        // 3. コマンドの初期化と実行
        $command = new BattleCommand($this->mockCombatService, $this->mockRepository);
        $command->execute($attackerId, $targetId);
    }

    /**
     * @test
     * キャラクターが見つからない場合、コマンドはエラーを出力して終了すべき
     */
    public function command_should_exit_if_character_not_found(): void
    {
        // 攻撃者ID: 'missing-id'
        // ターゲットID: 'monster-1'
        $missingId = 'missing-id';
        $targetId = 'monster-1';

        // リポジトリが find() されたときに返す値を設定
        $this->mockRepository->expects($this->exactly(2)) // 💥 期待呼び出し回数を 2 回に変更 💥
                             ->method('find')
                             ->will($this->returnValueMap([
                                 [$missingId, null], // 1回目: Attackerはnull
                                 [$targetId, null], // 2回目: Targetもnullを返す設定 (ここではどちらでも構いませんが、明示的に設定します)
                             ]));
        
        // CombatServiceは呼び出されないことを期待 (nullが返されるため)
        $this->mockCombatService->expects($this->never())
                                ->method('executeAttack');

        $command = new BattleCommand($this->mockCombatService, $this->mockRepository);
        
        $command->execute($missingId, $targetId); 
    }
}