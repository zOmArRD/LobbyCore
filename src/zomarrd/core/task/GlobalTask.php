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

use pocketmine\scheduler\Task;
use zomarrd\core\network\Network;
use zomarrd\core\network\player\NetworkPlayer;

final class GlobalTask extends Task
{

    public function onRun(int $currentTick)
    {
        if ($currentTick % 30 === 0) {
            foreach ($this->getNetwork()->getServerPM()->getOnlinePlayers() as $player) {
                if (!$player instanceof NetworkPlayer) return;
                $player->getScoreboardSession()->set();
            }
        }
    }

    public function getNetwork(): Network
    {
        return new Network();
    }
}