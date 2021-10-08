<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 1/10/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\commands\npc\subcmd;

use pocketmine\command\CommandSender;
use zomarrd\core\commands\ISubCommand;
use zomarrd\core\modules\npc\Human;
use zomarrd\core\network\player\NetworkPlayer;
use const zOmArRD\PREFIX;

final class NCreate implements ISubCommand
{
    /**
     * @param CommandSender $player
     * @param array         $args
     */
    public function executeSub(CommandSender $player, array $args): void
    {
        if (!$player instanceof NetworkPlayer) return;

        if (!isset($args[0])) {
            $player->sendMessage(PREFIX . "§cUse: §7/npc create <string:npcServer>");
            return;
        }

        if (!$player->hasPermission("greek.cmd.npc")) {
            $player->sendMessage(PREFIX . "§cYou can't do this, you don't have the necessary permissions!");
            return;
        }

        $npcName = $args[0];

        switch ($npcName) {
            case "hcf":
            case "practice":
                Human::spawn($npcName, $player);
                $player->sendMessage(PREFIX . "§a" . "entity $npcName has successfully spawned.");
                break;
            default:
                $player->sendMessage(PREFIX . "§cNpc id not found");
                break;
        }
    }
}