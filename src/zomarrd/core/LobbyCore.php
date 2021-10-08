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

namespace zomarrd\core;

use pocketmine\network\mcpe\RakLibInterface;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLogger;
use zomarrd\core\commands\Command;
use zomarrd\core\events\EventsManager;
use zomarrd\core\modules\mysql\AsyncQueue;
use zomarrd\core\modules\mysql\query\InsertQuery;
use zomarrd\core\modules\mysql\query\UpdateRowQuery;
use zomarrd\core\modules\npc\Human;
use zomarrd\core\network\Network;
use zomarrd\core\network\server\ServerManager;
use zomarrd\core\task\TaskManager;
use const zOmArRD\PREFIX;

final class LobbyCore extends PluginBase
{
    /** @var LobbyCore */
    public static LobbyCore $instance;

    /** @var PluginLogger */
    public static PluginLogger $logger;

    public function onLoad(): void
    {
        self::setInstance($this);
        self::setLogger($this->getLogger());
        $this->getNetwork()->getResourceManager()->init();
        $this->checkDb();
        $this->getNetwork()->getServerManager()->init();
    }

    public function onEnable(): void
    {
        /* It is in charge of registering the plugin events. */
        new EventsManager();

        /* It is responsible for registering the tasks, and loading it. */
        new TaskManager();

        /* Register the plugin commands. */
        new Command();

        /* Register the custom entity of Human. */
        Human::register();

        /* Avoid some network crashes when transferring packets */
        foreach ($this->getServer()->getNetwork()->getInterfaces() as $interface) {
            if ($interface instanceof RakLibInterface) {
                $interface->setPacketLimit(PHP_INT_MAX);
            }
        }

        self::$logger->info(PREFIX . "§a" . $this->getNetwork()->getTextUtils()->uDecode("-<&QU9VEN(&QO861E9````"));
    }

    public function onDisable(): void
    {
        AsyncQueue::submitQuery(new UpdateRowQuery(["isOnline" => 0, "Players" => 0], "ServerName", $this->getNetwork()->getServerManager()->getCurrentServer()->getName(), "servers"));
    }

    /**
     * @param LobbyCore $instance
     */
    public static function setInstance(LobbyCore $instance): void
    {
        self::$instance = $instance;
    }

    /**
     * @return LobbyCore
     */
    public static function getInstance(): LobbyCore
    {
        return self::$instance;
    }

    /**
     * @param PluginLogger $logger
     */
    public static function setLogger(PluginLogger $logger): void
    {
        self::$logger = $logger;
    }

    /**
     * @return Network
     */
    public function getNetwork(): Network
    {
        return new Network();
    }

    private function checkDb(): void
    {
        self::$logger->info(PREFIX . "checking the database...");
        AsyncQueue::submitQuery(new InsertQuery("CREATE TABLE IF NOT EXISTS servers(ServerName VARCHAR(50) UNIQUE, Players INT DEFAULT 0, isOnline SMALLINT DEFAULT 0, isWhitelisted SMALLINT DEFAULT  0);"));
        AsyncQueue::submitQuery(new InsertQuery("CREATE TABLE IF NOT EXISTS settings(player VARCHAR(50) UNIQUE, language TEXT, scoreboard SMALLINT DEFAULT 1);"));
    }
}