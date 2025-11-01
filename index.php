<?php
// index.php

require __DIR__ . '/vendor/autoload.php';

use App\Application\BattleCommand;
use App\Domain\Service\DamageCalculator;
use App\Domain\Service\CombatService;
use App\Infrastructure\Persistence\JsonCharacterRepository; // 次に実装
use App\Domain\Model\{HitPoint, AttackPower, DefensePower};

// --- 1. インフラストラクチャ層の実装 ---
// 💡 ダミーリポジトリの実装 (動作確認用)
// 実際の JSONCharacterRepository を実装するまでは、手動でキャラクターを作成します。
$repository = new class implements \App\Domain\Repository\CharacterRepositoryInterface {
    public function find(string $id): ?\App\Domain\Model\Character
    {
        if ($id === 'player') {
            return new \App\Domain\Model\Character(
                'player', '勇者', new HitPoint(100, 100), new AttackPower(20), new DefensePower(10)
            );
        }
        if ($id === 'monster') {
            return new \App\Domain\Model\Character(
                'monster', 'スライム', new HitPoint(30, 30), new AttackPower(5), new DefensePower(5)
            );
        }
        return null;
    }
    public function save(\App\Domain\Model\Character $character): void
    {
        // 実際の保存ロジック（JSONファイル書き込み）はJsonCharacterRepositoryで実装
        echo " [保存ログ: {$character->getName()} のHPが {$character->getCurrentHp()} に更新されました]\n";
    }
};

// --- 2. ドメインサービスの組み立て ---
$damageCalculator = new DamageCalculator();
$combatService = new CombatService($damageCalculator);

// --- 3. アプリケーション層の初期化 ---
// 💡 本来、このDIはDIコンテナで行われますが、今回は手動でインスタンスを組み立てます。
$battleCommand = new BattleCommand($combatService, $repository);

// --- 4. CLI引数の処理 ---
// 実行例: php index.php player monster
if (!isset($argv[1]) || !isset($argv[2])) {
    echo "使用法: php index.php [攻撃者ID] [ターゲットID]\n";
    echo "例: php index.php player monster\n";
    exit(1);
}

$attackerId = $argv[1];
$targetId = $argv[2];

// 実行
$battleCommand->execute($attackerId, $targetId);