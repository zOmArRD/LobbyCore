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

    private function getNetwork(): Network
    {
        return new Network();
    }

    private function getConfig(): Config
    {
        return $this->getNetwork()->getResourceManager()->getArchive("network.data.yml");
    }

    public function init(): void
    {
        /** @var string $currentServerName */
        $currentServerName = $this->getConfig()->get('current.server');
        AsyncQueue::submitQuery(new RegisterServerQuery($currentServerName));
        LobbyCore::$logger->info(PREFIX . "Registering the server in the database");
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

    public function reloadServers(): void
    {
        self::$servers = [];

        /** @var string $currentServerName */
        $currentServerName = self::getConfig()->get('current.server');
        AsyncQueue::submitQuery(new SelectQuery("SELECT * FROM servers"), function ($rows) use ($currentServerName) {
            foreach ($rows as $row) {
                $server = new Server($row["ServerName"], (int)$row["Players"], (bool)$row["isOnline"], (bool)$row["isWhitelisted"]);
                if ($row["ServerName"] === $currentServerName) {
                    self::$currentServer = $server;
                    LobbyCore::$logger->info(PREFIX . "The server has been registered in the database.");
                } else {
                    self::$servers[] = $server;
                    LobbyCore::$logger->notice(PREFIX . "A new server has been registered | ($server->name)");
                }
            }
        });
    }

    /**
     * @param string $serverName
     * @param int    $players
     * @param bool   $isOnline
     * @param bool   $isWhitelisted
     *
     * @deprecated Function not tested, possibly not used.
     */
    public function updateServerData(string $serverName, int $players = 0, bool $isOnline = false, bool $isWhitelisted = false)
    {
        if (!isset(self::$servers[$serverName])) {
            self::$servers[$serverName] = new Server($serverName, $players, $isOnline, $isWhitelisted);
            LobbyCore::$logger->notice("A new server has been registered | ($serverName)");
            return;
        }

        self::$servers[$serverName]->update($serverName, $players, $isOnline, $isWhitelisted);
    }

    /**
     * @param string $name
     *
     * @return Server|null
     * @deprecated Function not tested, possibly not used.
     */
    public function getServer(string $name): ?Server
    {
        return self::$servers[$name] ?? null;
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
        $finalServer = null;
        foreach (self::getServers() as $server) {
            if ($server->getName() === $name) {
                $finalServer = $server;
            }
        }
        return $finalServer;
    }

    /**
     * @return int
     */
    public function getPracticePlayers(): int
    {
        $players = 0;
        foreach (self::getServers() as $server) {
            if ($server->getName() === self::getConfig()->get("downstream.server")) {
                $players = +$server->getPlayers();
            }
        }
        return (int)$players;
    }

    /**
     * Get all the players that are in the network.
     *
     * @return int
     */
    public function getNetworkPlayers(): int
    {
        $players = 0;
        foreach (self::getServers() as $server) {
            $players += $server->getPlayers();
        }

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

        foreach ($servers as $server) {
            if ($server->getName() == $target) {
                if ($server->isOnline) {
                    return "§a" . "players: §f" . $server->getPlayers();
                } else {
                    return "§c" . "OFFLINE";
                }
            }
        }
        return "loading...";
    }
}