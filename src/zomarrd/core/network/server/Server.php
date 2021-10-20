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

use zomarrd\core\modules\mysql\AsyncQueue;
use zomarrd\core\modules\mysql\query\SelectQuery;

class Server
{
    /** @var string */
    public string $name;

    /** @var int */
    public int $players;

    /** @var bool */
    public bool $isOnline, $isWhitelisted;

    /**
     * @param string $server
     * @param int    $players
     * @param bool   $isOnline
     * @param bool   $isWhitelisted
     */
    public function __construct(string $server = "Unknown", int $players = 0, bool $isOnline = false, bool $isWhitelisted = false)
    {
        $this->update($server, $players, $isOnline, $isWhitelisted);
    }

    /**
     * @param string $server
     * @param int $players
     * @param bool $isOnline
     * @param bool $isWhitelisted
     */
    public function update(string $server = "Unknown", int $players = 0, bool $isOnline = false, bool $isWhitelisted = false): void
    {
        $this->setName($server);
        $this->setPlayers($players);
        $this->setIsOnline($isOnline);
        $this->setIsWhitelisted($isWhitelisted);
    }

    /**
     * Synchronize server data from database.
     */
    public function sync(): void
    {
        AsyncQueue::submitQuery(new SelectQuery("SELECT * FROM servers WHERE ServerName='$this->name';"), function ($rows) {
            $row = $rows[0];
            if ($row !== null) {
                $this->setIsOnline((bool)$row["isOnline"]);
                $this->setPlayers((int)$row["Players"]);
                $this->setIsWhitelisted((bool)$row["isWhitelisted"]);
            } else {
                $this->setIsOnline((bool)0);
                $this->setPlayers(0);
                $this->setIsWhitelisted((bool)0);
            }
        });
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param int $players
     */
    public function setPlayers(int $players): void
    {
        $this->players = $players;
    }

    /**
     * @return int
     */
    public function getPlayers(): int
    {
        return $this->players;
    }

    /**
     * @param bool $isOnline
     */
    public function setIsOnline(bool $isOnline): void
    {
        $this->isOnline = $isOnline;
    }

    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->isOnline;
    }

    /**
     * @param bool $isWhitelisted
     */
    public function setIsWhitelisted(bool $isWhitelisted): void
    {
        $this->isWhitelisted = $isWhitelisted;
    }

    /**
     * @return bool
     */
    public function isWhitelisted(): bool
    {
        return $this->isWhitelisted;
    }
}