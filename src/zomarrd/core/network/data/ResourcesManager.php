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

namespace zomarrd\core\network\data;

use pocketmine\level\Level;
use pocketmine\utils\Config;
use zomarrd\core\LobbyCore;
use zomarrd\core\network\Network;
use const zOmArRD\PREFIX;
use const zOmArRD\Spawn_Data;

final class ResourcesManager
{
    /** @var string */
    public string $prefix, $serverName;

    /** @var array */
    private array $database, $spawn_options;

    public function init(): void
    {
        @mkdir($this->getNetwork()->getPlugin()->getDataFolder());
        $configYml = "config.yml";

        foreach (['config.yml', 'spawn.data.yml', 'network.data.yml'] as $data) {
            $this->getNetwork()->getPlugin()->saveResource($data);
        }

        $mainYml = $this->getArchive($configYml);
        $spawnData = $this->getArchive("spawn.data.yml");

        $this->prefix = $this->getNetwork()->getTextUtils()->replaceColor($mainYml->get('prefix'));
        $this->database = $mainYml->get("database");
        $this->spawn_options = $spawnData->get("spawn.data");

        define("zOmArRD\PREFIX", $this->prefix);
        define("zOmArRD\DB", $this->database);
        define("zOmArRD\Spawn_Data", $this->spawn_options);

        if (Spawn_Data['is.enabled']) {
            $level = Spawn_Data['world.name'];
            if (!$this->getNetwork()->getServerPM()->isLevelLoaded($level)){
                $this->getNetwork()->getServerPM()->loadLevel($level);
            }
            $this->getNetwork()->getServerPM()->getLevelByName($level)->setTime(Level::TIME_DAY);
            $this->getNetwork()->getServerPM()->getLevelByName($level)->stopTime();
        }

        LobbyCore::$logger->info(PREFIX . "§a" . "Variable values loaded correctly.");
    }

    private function getNetwork(): Network
    {
        return new Network();
    }

    /**
     * @param string $archive
     * @param int    $type
     *
     * @return Config
     */
    public function getArchive(string $archive, int $type = Config::YAML)
    {
        return new Config($this->getNetwork()->getPlugin()->getDataFolder() . $archive, $type);
    }
}