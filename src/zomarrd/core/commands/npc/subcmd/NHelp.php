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
use zomarrd\core\commands\npc\NpcCmd;
use const zOmArRD\PREFIX;

final class NHelp implements ISubCommand
{
    /**
     * @param CommandSender $player
     * @param array         $args
     */
    public function executeSub(CommandSender $player, array $args): void
    {
        $player->sendMessage(PREFIX . "§bList of subcommands for Npc System :");
        foreach (array_keys(NpcCmd::$subCmd) as $subCmd) $player->sendMessage("§7- §a/npc $subCmd");
    }
}