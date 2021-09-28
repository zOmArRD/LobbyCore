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

namespace zomarrd\core\modules\mysql\query;

use mysqli;
use zomarrd\core\LobbyCore;
use zomarrd\core\modules\mysql\AsyncQuery;

class UpdateRowQuery extends AsyncQuery
{
    /** @var string|null */
    public ?string $table, $updates, $conditionKey, $conditionValue;

    public function __construct(array $updates, string $conditionKey, string $conditionValue, string $table = null)
    {
        $this->updates = serialize($updates);
        $this->conditionKey = $conditionKey;
        $this->conditionValue = $conditionValue;

        if ($table === null) {
            LobbyCore::$logger->error("Unable to update the changes in the database");
            return;
        }
        $this->table = $table;
    }

    /**
     * @param mysqli $mysqli
     */
    public function query(mysqli $mysqli): void
    {
        $updates = [];
        foreach (unserialize($this->updates) as $k => $v) {
            $updates[] = "$k='$v'";
        }
        $mysqli->query("UPDATE $this->table SET " . implode(",", $updates) . " WHERE $this->conditionKey='$this->conditionValue';");
    }
}