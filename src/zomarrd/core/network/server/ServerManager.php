<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 20/7/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\network\server;

use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
use zomarrd\core\LobbyCore;
use zomarrd\core\modules\mysql\AsyncQueue;
use zomarrd\core\modules\mysql\query\RegisterServerQuery;
use zomarrd\core\modules\mysql\query\SelectQuery;
use zomarrd\core\modules\mysql\query\UpdateRowQuery;
use zomarrd\core\network\Network;
use const zOmArRD\PREFIX;

final class ServerManager
{
    /** @var int */
    protected const REFRESH_TICKS = 60;

    /** @var Server[] */
    public static array $servers = [];

    /** @var Server */
    private static Server $currentServer;

    /**
     * @return Network
     */
    private function getNetwork(): Network
    {
        return new Network();
    }

    /**
     * @return Config
     */
    private function getConfig(): Config
    {
        return $this->getNetwork()->getResourceManager()->getArchive("network.data.yml");
    }

    /**
     * It is the most important function...
     * <b>It is in charge of registering the servers and verifying them </b>
     */
    public function init(): void
    {
        /** @var string $currentServerName */
        $currentServerName = $this->getConfig()->get('current.server');
        AsyncQueue::submitQuery(new RegisterServerQuery($currentServerName));
        LobbyCore::$logger->info(PREFIX . "Registering the server in the database");
        sleep(1); // I DON'T KNOW REALlY BRO
        $this->reloadServers();
        $this->getNetwork()->getTaskScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($currentServerName): void {
            $players = count(LobbyCore::getInstance()->getServer()->getOnlinePlayers());
            $isWhitelist = (LobbyCore::getInstance()->getServer()->hasWhitelist() ? 1 : 0);
            AsyncQueue::submitQuery(new UpdateRowQuery(["Players" => "$players", "isWhitelisted" => "$isWhitelist"], "ServerName", $currentServerName, "servers"));

            foreach (self::getServers() as $server) {
                $server->sync();
            }
        }), self::REFRESH_TICKS);
    }

    /**
     * Reloads the server array data from the database.
     * <br><br>
     * Useful for when more servers are added to the database.
     */
    public function reloadServers(): void
    {
        self::$servers = [];

        /** @var string $currentServerName */
        $currentServerName = self::getConfig()->get('current.server');
        AsyncQueue::submitQuery(new SelectQuery("SELECT * FROM servers"), function ($rows) use ($currentServerName) {
            foreach ($rows as $row) {
                $server = new Server($row["server"], (int)$row["players"], (bool)$row["isOnline"], (bool)$row["isWhitelisted"]);
                if ($row["server"] === $currentServerName) {
                    self::$currentServer = $server;
                    LobbyCore::$logger->info(PREFIX . "The server ($currentServerName) has been registered in the database.");
                } else {
                    self::$servers[] = $server;
                    LobbyCore::$logger->notice(PREFIX . "A new server has been registered | ($server->name)");
                }
            }
        });
    }

    /**
     * @return Server
     */
    public function getCurrentServer(): Server
    {
        return self::$currentServer;
    }

    /**
     * @return Server[]
     */
    public function getServers(): array
    {
        return self::$servers;
    }

    /**
     * @param string $name
     *
     * @return Server|null
     */
    public function getServerByName(string $name): ?Server
    {
        foreach (self::getServers() as $server) {
            return ($server->getName() === $name) ? $server : null;
        }
        return null;
    }

    /**
     * Get all the players that are in the network.
     *
     * @return int
     */
    public function getNetworkPlayers(): int
    {
        $players = 0;
        foreach (self::getServers() as $server) $players += $server->getPlayers();

        $players += count($this->getNetwork()->getServerPM()->getOnlinePlayers());

        return $players;
    }

    /**
     * @param string $target
     *
     * @return string
     */
    public static function getServerPlayers(string $target): string
    {
        $servers = (new ServerManager)->getServers();

        foreach ($servers as $server) if ($server->getName() == $target) {
            return $server->isOnline() ? ("§a" . "PLAYING: §f" . $server->getPlayers()) : ("§c" . "OFFLINE");
        }
        return "§c" . "server.not.found";
    }
}