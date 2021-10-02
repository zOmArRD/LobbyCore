<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 28/9/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\task;

use Exception;
use pocketmine\scheduler\Task;
use zomarrd\core\LobbyCore;
use zomarrd\core\modules\npc\Human;
use zomarrd\core\network\Network;
use zomarrd\core\network\player\NetworkPlayer;
use zomarrd\core\network\server\ServerManager;

final class GlobalTask extends Task
{
    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick): void
    {
        if ($currentTick % 30 === 0) {
            /* Scoreboard Section */
            foreach ($this->getNetwork()->getServerPM()->getOnlinePlayers() as $player) {
                if (!$player instanceof NetworkPlayer) return;
                try {
                    $player->getScoreboardSession()->set();
                } catch (Exception $ex) {
                    LobbyCore::$logger->error($ex->getMessage() . "\n" . $ex->getFile() . "\n" . $ex->getLine());
                }
            }

            /* Npc Section */
            Human::applyName("hcf", ServerManager::getServerPlayers("HCF"));
        }
    }

    /**
     * @return Network
     */
    public function getNetwork(): Network
    {
        return new Network();
    }
}