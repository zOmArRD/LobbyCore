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
    /**
     * @return Network
     */
    public function getNetwork(): Network
    {
        return new Network();
    }

    /**
     * @return PluginManager
     */
    private function getPluginManager(): PluginManager
    {
        return $this->getNetwork()->getPluginManager();
    }

    /**
     * @return LobbyCore
     */
    private function getPlugin(): LobbyCore
    {
        return $this->getNetwork()->getPlugin();
    }

    /**
     * @param Listener $event
     */
    public function register(Listener $event): void
    {
        $this->getPluginManager()->registerEvents($event, $this->getPlugin());
    }

    abstract public function loadEvents(): void;
}