<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 26/9/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\events;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginManager;
use zomarrd\core\LobbyCore;
use zomarrd\core\network\Network;

abstract class Events
{
    public function getNetwork(): Network
    {
        return new Network();
    }

    private function getPluginManager(): PluginManager
    {
        return $this->getNetwork()->getPluginManager();
    }

    private function getPlugin(): LobbyCore
    {
        return $this->getNetwork()->getPlugin();
    }

    public function register(Listener $event): void
    {
        $this->getPluginManager()->registerEvents($event, $this->getPlugin());
    }

    abstract public function loadEvents(): void;
}