<?php

namespace cosmicnebula200\ReferMeBro\player;

use pocketmine\player\Player as P;
use cosmicnebula200\ReferMeBro\ReferMeBro;
use Ramsey\Uuid\Uuid;

class PlayerManager
{

    /** @var Player[] */
    private array $players = [], $codes = [];

    public function loadPlayer(P $player): void
    {
        ReferMeBro::getInstance()->getDataBase()->executeSelect(
            'refermebro.load',
            [
                'uuid' => $player->getUniqueId()->toString()
            ],
            function (array $rows) use ($player): void
            {
                if (count($rows) == 0) {
                    $this->createPlayer($player);
                    return;
                }
                $this->players[$rows[0]['name']] = new Player($rows[0]['uuid'] , $rows[0]['name'], $rows[0]['referral'], (array)json_decode($rows[0]['cmds']), (bool)$rows[0]['referred'], $rows[0]['refers']);
            }
        );
    }

    public function loadPlayerFromCode(string $code): void
    {
        ReferMeBro::getInstance()->getDataBase()->executeSelect(
            'refermebro.loadfromcode',
            [
                'referral' => $code
            ],
            function (array $rows): void
            {
                if (count($rows) == 0) {
                    return;
                }
                $this->codes[$rows[0]['referral']] = new Player($rows[0]['uuid'] , $rows[0]['name'], $rows[0]['referral'], (array)json_decode($rows[0]['cmds']), (bool)$rows[0]['referred'], $rows[0]['refers']);
            }
        );
    }

    public function unloadPlayer(P $player): void
    {
        if (isset($this->players[$player->getName()]))
            unset($this->players[$player->getName()]);
    }

    public function createPlayer(P $player): void
    {
        $referral = substr(str_replace("-", "", Uuid::uuid4()->toString()), 0, ReferMeBro::getInstance()->getConfig()->get('code-size', 8));
        ReferMeBro::getInstance()->getDataBase()->executeInsert(
            'refermebro.create',
            [
                'uuid' => $player->getUniqueId()->toString(),
                'name' => $player->getName(),
                'referral' => $referral,
                'cmds' => json_encode([]),
                'refers' => 0
            ]);
        $this->players[$player->getName()] = new Player($player->getUniqueId()->toString(), $player->getName(), $referral, [], false, 0);
    }

    public function getPlayer(P $player): Player
    {
        return $this->players[$player->getName()];
    }

    public function getPlayerByPrefix(string $name): ?Player
    {
        return $this->players[$name]?? null;
    }

    public function getPlayerByCode(string $code): ?Player
    {
        return $this->codes[$code] ?? null;
    }

}
