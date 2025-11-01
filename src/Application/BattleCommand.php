<?php

namespace App\Application;

use App\Domain\Service\CombatService;
use App\Domain\Repository\CharacterRepositoryInterface;

// 💡 CLIからの入力を受け付ける機能は、executeメソッド内で STDIN/STDOUT を直接使うか、
// Symfony ConsoleのようなフレームワークのInput/Outputインターフェースを導入するかの選択肢があります。
// ここでは、executeに引数として渡されたIDを使うことで、テストの複雑化を防ぎます。

class BattleCommand
{
    public function __construct(
        private readonly CombatService $combatService,
        // 💥 新しい依存性の注入 💥
        private readonly CharacterRepositoryInterface $repository
    ) {}

    /**
     * CLIから呼び出される戦闘処理のエントリーポイント
     */
    public function execute(string $attackerId, string $targetId): void
    {
        // 1. リポジトリからエンティティを取得
        $attacker = $this->repository->find($attackerId);
        $target = $this->repository->find($targetId);

        // 2. 存在チェック (ドメイン層にロジックを渡す前のアプリケーション層のガード)
        if (!$attacker || !$target) {
            echo "エラー: 攻撃者またはターゲットが見つかりません。\n";
            return;
        }

        echo "--- バトル開始: {$attacker->getName()} vs {$target->getName()} ---\n";

        // 3. ドメインサービスを呼び出す
        $this->combatService->executeAttack($attacker, $target);
        
        // 4. バトル後の状態をリポジトリに保存 (永続化)
        $this->repository->save($target);

        // 5. ユーザーに出力
        echo "{$attacker->getName()} は {$target->getName()} に攻撃しました。\n";
        echo "{$target->getName()} の残りHP: {$target->getCurrentHp()}\n";
        
        if (!$target->isAbleToBattle()) {
             echo "{$target->getName()} は倒れた！\n";
        }
    }
}