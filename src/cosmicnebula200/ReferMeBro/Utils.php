<?php

namespace cosmicnebula200\ReferMeBro;

use cosmicnebula200\ReferMeBro\player\Player;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player as P;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use Vecnavium\FormsUI\CustomForm;

class Utils
{

    public static function sendReferForm(P $P): void
    {
        $form = new CustomForm(function (P $player, ?array $data): void {
            $code = null;
            if(isset($data[1]))
                $code = $data[1];
            if ($code === null)
                return;

            ReferMeBro::getInstance()->getPlayerManager()->LoadPlayerFromCode($code);

            ReferMeBro::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $code): void {
                $p = ReferMeBro::getInstance()->getPlayerManager()->getPlayerByCode($code);

                if (!$p instanceof Player)
                {
                    $player->sendMessage(ReferMeBro::$messages->getMessage("invalid-code"));
                    return;
                }

                $pl = ReferMeBro::getInstance()->getPlayerManager()->getPlayer($player);
                if ($p->getReferral() === $pl->getReferral() or $p->hasReferred())
                    return;
                $newRefers = $p->getRefers() + 1;
                $p->setRefers($newRefers);
                $pl->setRefered(true);
                $player->sendMessage(ReferMeBro::$messages->getMessage("refer-use"));

                if (!ReferMeBro::getInstance()->getConfig()->getNested("refers.$newRefers"))
                    return;

                ReferMeBro::getInstance()->getPlayerManager()->loadPlayerFromCode($code);
                $pPlayer = ReferMeBro::getInstance()->getServer()->getPlayerByPrefix($p->getUsername());

                if ($pPlayer instanceof P)
                {
                    foreach(ReferMeBro::getInstance()->getConfig()->getNested("refers.$newRefers") as $cmd)
                    {
                        $server = ReferMeBro::getInstance()->getServer();
                        $server->dispatchCommand(new ConsoleCommandSender($server, $server->getLanguage()), str_replace("{PLAYER}", $p->getUsername(), $cmd));
                    }
                    $pPlayer->sendMessage(ReferMeBro::$messages->getMessage("referred-user", [
                        "refers" => $newRefers
                    ]));
                } else {
                    $p->setCmds(ReferMeBro::getInstance()->getConfig()->getNested("refers.$newRefers"));
                }

                foreach (ReferMeBro::getInstance()->getConfig()->get("refer-use") as $cmd)
                {
                    $server = ReferMeBro::getInstance()->getServer();
                    $server->dispatchCommand(new ConsoleCommandSender($server, $server->getLanguage()), str_replace("{PLAYER}", $pl->getUsername(), $cmd));
                }
            }), 20);
        });
        $form->setTitle(TextFormat::colorize(ReferMeBro::$forms->getNested('refer.title')));
        $form->addLabel(TextFormat::colorize(ReferMeBro::$forms->getNested('refer.content')));
        $form->addInput("Code");
        $form->sendToPlayer($P);
    }

}