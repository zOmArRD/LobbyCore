<?php
/*
 * Created by PhpStorm
 *
 * User: zOmArRD
 * Date: 1/8/2021
 *
 * Copyright © 2021 - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\modules\mysql\query;

use Exception;
use mysqli;
use pocketmine\Server;
use zomarrd\core\LobbyCore;
use zomarrd\core\modules\mysql\AsyncQuery;

class SelectQuery extends AsyncQuery
{
    public mixed $rows;

    public string $query;

    public function __construct(string $sqlQuery)
    {
        $this->query = $sqlQuery;
    }

    public function query(mysqli $mysqli): void
    {
        $result = $mysqli->query($this->query);
        $rows = [];
        try {
            if ($result !== false) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
                $this->rows = serialize($rows);
            }
        } catch (Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    public function onCompletion(Server $server)
    {
        if ($this->rows === null) {
            LobbyCore::$logger->error("Error while executing query. Please check database settings and try again.");
            return;
        }
        $this->rows = unserialize($this->rows);
        parent::onCompletion($server);
    }
}