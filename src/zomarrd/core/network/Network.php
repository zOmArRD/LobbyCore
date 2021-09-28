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
use zomarrd\core\network\data\ResourcesManager;
use zomarrd\core\network\server\ServerManager;
use zomarrd\core\network\utils\TextUtils;

final class Network
{
    /**
     * @return LobbyCore
     */
    public function getPlugin(): LobbyCore
    {
        return LobbyCore::getInstance();
    }

    public function getServerPM()
    {
        return $this->getPlugin()->getServer();
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
    public function getTaskScheduler(): TaskScheduler
    {
        return $this->getPlugin()->getScheduler();
    }

    /**
     * @return ResourcesManager
     */
    public function getResourceManager(): ResourcesManager
    {
        return new ResourcesManager();
    }

    /**
     * @return ServerManager
     */
    public function getServerManager(): ServerManager
    {
        return new ServerManager();
    }

    public function getTextUtils(): TextUtils
    {
        return new TextUtils();
    }
}