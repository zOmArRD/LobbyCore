<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 18/10/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\commands\server;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use zomarrd\core\network\Network;

class ServerCmd extends Command
{
    public function __construct()
    {
        parent::__construct("server", "Show server information", "/server", ["info", "about"]);
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     *
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $br = "§r\n";
        $archive = $this->getNetwork()->getResourceManager()->getArchive("network.data.yml");

        $sender->sendMessage("§l§f» §6Greek §fNetwork §7| §r§aServer Info §l§f«". $br .
            "§bCurrent Server: §f{$archive->get("current.server")}" . $br.
            "§bPlayers: §f" . count($this->getNetwork()->getServerPM()->getOnlinePlayers()) . "/{$this->getNetwork()->getServerPM()->getMaxPlayers()}" . $br .
            "§bVersion: §f1.0.0" . $br .
            "§bProxy: §fNA-Proxy-01 (play.greekmc.net)");
    }

    /**
     * @return Network
     */
    public function getNetwork(): Network
    {
        return new Network();
    }
}