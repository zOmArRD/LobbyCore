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

namespace zomarrd\core\network;

use pocketmine\plugin\PluginManager;
use pocketmine\scheduler\TaskScheduler;
use zomarrd\core\LobbyCore;

final class Network
{
    /**
     * @return LobbyCore
     */
    public function getPlugin(): LobbyCore
    {
        return LobbyCore::getInstance();
    }

    /**
     * @return PluginManager
     */
    public function getPluginManager(): PluginManager
    {
        return $this->getPlugin()->getServer()->getPluginManager();
    }

    /**
     * @return TaskScheduler
     */
    public function getTaskManager(): TaskScheduler
    {
        return $this->getPlugin()->getScheduler();
    }
}