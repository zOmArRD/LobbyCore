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

final class NPurge implements ISubCommand
{
    /**
     * @param CommandSender $player
     * @param array         $args
     */
    public function executeSub(CommandSender $player, array $args): void
    {
        if (!$player instanceof NetworkPlayer) return;

        if (!isset($args[0])) {
            $player->sendMessage(PREFIX . "§cUse: §7/npc purge <string:npcServer>");
            return;
        }

        if (!$player->hasPermission("greek.cmd.npc")) {
            $player->sendMessage(PREFIX . "§cYou can't do this, you don't have the necessary permissions!");
            return;
        }

        $npcName = $args[0];

        if ($npcName == "all") {
            foreach ((new Network())->getServerPM()->getLevels() as $level) {
                foreach ($level->getEntities() as $entity) {
                    if (!$entity instanceof NetworkPlayer) {
                        $entity->kill();
                        $player->sendMessage(PREFIX . "§a" . "all entities were successfully removed!");
                        return;
                    }
                }
            }
        }

        $config = (new Network())->getResourceManager()->getArchive("network.data.yml");

        try {
            foreach ($config->get("servers.availables") as $serverData) {
                if ($npcName == $serverData['npc.id']) {
                    Human::purge($npcName);
                    $player->sendMessage(PREFIX . "§a" . "$npcName entity has been removed");
                } else {
                    $player->sendMessage(PREFIX . "§cNpc id not found");
                }
                return;
            }
        } catch (Exception) {
            /* todo: check this. */
        }
    }
}