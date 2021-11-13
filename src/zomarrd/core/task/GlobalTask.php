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
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\particle\WaterParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use zomarrd\core\LobbyCore;
use zomarrd\core\modules\cosmetics\Cosmetics;
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
            Human::applyName("hcf", ServerManager::getServer("HCF")->getStatus());
            Human::applyName("practice", ServerManager::getServer("Practice")->getStatus());

            /* Particles Section */
            if ($currentTick % 15 === 0) {
                $this->setParticle("lava.splash");
            }

            /* todo: finalize this! */
        }
    }

    /**
     * @return Network
     */
    private function getNetwork(): Network
    {
        return new Network();
    }

    private function setParticle(string $particle): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $level = $player->getLevel();
            $x = $player->getX(); $y = $player->getY(); $z = $player->getZ();
            $location = $player->getLocation();
            $pn = $player->getName();

            if (isset(Cosmetics::$particles[$pn])) switch ($particle) {
                case "lava.splash":
                    if (Cosmetics::$particles[$pn] === "lava.splash") {
                        $center = new Vector3($x, $y, $z);
                        for ($yaw = 0; $yaw <= 10; $yaw += (M_PI * 2) / 20) {
                            $x = -sin($yaw) + $center->x;
                            $z = cos($yaw) + $center->z;
                            $y = $center->y;
                            $level->addParticle(new LavaParticle(new Vector3($x, $y + 1.5, $z)));
                        }
                    }
                    break;
                case "water.splash":
                    if (Cosmetics::$particles[$pn] === "water.splash") {
                        $center = new Vector3($x, $y, $z);
                        for ($yaw = 0; $yaw <= 10; $yaw += (M_PI * 2) / 20) {
                            $x = -sin($yaw) + $center->x;
                            $z = cos($yaw) + $center->z;
                            $y = $center->y;
                            $level->addParticle(new WaterParticle(new Vector3($x, $y + 1.5, $z)));
                        }
                    }
                    break;
            }
        }
    }
}