<?php

namespace App\Domain\Repository;

use App\Domain\Model\Character;

interface CharacterRepositoryInterface
{
    public function find(string $id): ?Character;
    public function save(Character $character): void;
}