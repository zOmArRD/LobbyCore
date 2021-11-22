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

use Exception;
use pocketmine\command\CommandSender;
use zomarrd\core\commands\ISubCommand;
use zomarrd\core\modules\npc\Human;
use zomarrd\core\network\Network;
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
            $player->sendMessage(PREFIX . "§cUse: §7/npc create <string:server>");
            return;
        }

        if (!$player->hasPermission("greek.cmd.npc")) {
            $player->sendMessage(PREFIX . "§cYou can't do this, you don't have the necessary permissions!");
            return;
        }

        $npcName = $args[0];

        try {
            if ($npcName === "practice" || $npcName === "uhc" || $npcName === "hcf") {
                Human::spawn($npcName, $player);
                $player->sendMessage(PREFIX . "§a" . "entity $npcName has successfully spawned.");
            } else $player->sendMessage(PREFIX . "§cNpc id not found");
        } catch (Exception $ex) {
            if ($player->isOp()) $player->sendMessage("Error in line: {$ex->getLine()}, File: {$ex->getFile()} \n Error: {$ex->getMessage()}");
        }
    }
}