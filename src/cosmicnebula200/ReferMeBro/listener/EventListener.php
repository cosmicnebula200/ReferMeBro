<?php

namespace cosmicnebula200\ReferMeBro\listener;

use cosmicnebula200\ReferMeBro\ReferMeBro;
use cosmicnebula200\ReferMeBro\Utils;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\scheduler\ClosureTask;

class EventListener implements Listener
{

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        ReferMeBro::getInstance()->getPlayerManager()->loadPlayer($player);
        if (!$player->hasPlayedBefore()) {
            Utils::sendReferForm($player);
        }
        ReferMeBro::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
            $p = ReferMeBro::getInstance()->getPlayerManager()->getPlayer($player);
            if (count($p->getCmds()) !== 0) {
                foreach ($p->getCmds() as $cmd) {
                    $server = ReferMeBro::getInstance()->getServer();
                    $server->dispatchCommand(new ConsoleCommandSender($server, $server->getLanguage()), str_replace("{PLAYER}", $p->getUsername(), $cmd));
                }
                $p->setCmds([]);
            }
        }), 20);
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onLeave(PlayerQuitEvent $event): void
    {
        ReferMeBro::getInstance()->getPlayerManager()->unloadPlayer($event->getPlayer());
    }

}
