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

namespace zomarrd\core\commands;

use pocketmine\command\Command as PMCommand;
use zomarrd\core\commands\lang\LangCmd;
use zomarrd\core\commands\npc\NpcCmd;
use zomarrd\core\commands\server\ServerCmd;
use zomarrd\core\network\Network;

abstract class CommandManager
{

    /**
     * @return Network
     */
    public function getNetwork(): Network
    {
        return new Network();
    }

    /**
     * @param string    $prefix
     * @param PMCommand $command
     */
    private function register(string $prefix, PMCommand $command): void
    {
        $this->getNetwork()->getServerPM()->getCommandMap()->register($prefix, $command);
    }

    /**
     * It is responsible for registering the plugin commands.
     */
    public function load(): void
    {
        foreach (["npc" => new NpcCmd(), "lang" => new LangCmd(), "server" => new ServerCmd()] as $prefix => $command) $this->register($prefix, $command);
    }
}