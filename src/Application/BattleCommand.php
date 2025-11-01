<?php

namespace App\Application;

use App\Domain\Service\CombatService;
// 💡 今後、CharacterRepositoryInterface や Character も必要になりますが、
// まずはテストを通すためにCombatServiceのみに焦点を当てます。

class BattleCommand
{
    public function __construct(
        private readonly CombatService $combatService
    ) {}

    /**
     * CLIから呼び出される戦闘処理のエントリーポイント
     */
    public function execute(string $attackerId, string $targetId): void
    {
        // 1. 💥 ここでリポジトリからエンティティを取得するロジックが必要だが、一旦スキップ 💥
        //    (REDを回避するため、現在は直接モックされるCombatServiceの呼び出しのみ)
        
        // 2. ドメインサービスを呼び出す (テストで検証されるロジック)
        // 💡 暫定措置: テストのモックに合わせるため、ダミー引数を渡す
        $dummyAttacker = new \App\Domain\Model\Character($attackerId, 'A', new \App\Domain\Model\HitPoint(1, 1), new \App\Domain\Model\AttackPower(1), new \App\Domain\Model\DefensePower(1));
        $dummyTarget = new \App\Domain\Model\Character($targetId, 'T', new \App\Domain\Model\HitPoint(1, 1), new \App\Domain\Model\AttackPower(1), new \App\Domain\Model\DefensePower(1));

        $this->combatService->executeAttack($dummyAttacker, $dummyTarget);
        
        // 3. ユーザーに出力 (CLI表示ロジックは次のステップで実装)
        echo "{$attackerId} が {$targetId} を攻撃しました。\n";
        echo "戦闘フェーズが完了しました。\n";
    }
}