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

namespace zomarrd\core\commands\npc;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use zomarrd\core\commands\ISubCommand;
use zomarrd\core\commands\npc\subcmd\NCreate;
use zomarrd\core\commands\npc\subcmd\NHelp;
use zomarrd\core\commands\npc\subcmd\NPurge;
use zomarrd\core\network\player\NetworkPlayer;

final class NpcCmd extends Command
{
    /** @var ISubCommand[] */
    public static array $subCmd = [];

    public function __construct()
    {
        parent::__construct("npc", "NPC System", "/npc help", []);
        $this->registerSub();
    }

    private function registerSub(): void
    {
        foreach (["help" => new NHelp(), "create" => new NCreate(), "purge" => new NPurge()] as $prefix => $subCmd) {
            self::$subCmd[$prefix] = $subCmd;
        }
    }

    private function getSub(string $prefix): ?string
    {
        return match ($prefix) {
            "help" => "help",
            "create" => "create",
            "purge" => "purge",
            default => null,
        };
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof NetworkPlayer) return;

        if (!isset($args[0])) {
            self::$subCmd[$this->getSub("help")]->executeSub($sender, []);
            return;
        }

        $prefix = $args[0];

        if ($this->getSub($prefix) === null) {
            self::$subCmd[$this->getSub("help")]->executeSub($sender, []);
            return;
        }

        array_shift($args);
        $subCmd = self::$subCmd[$this->getSub($prefix)];
        $subCmd->executeSub($sender, $args);
    }
}