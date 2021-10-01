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
use zomarrd\core\modules\lang\LangManager;
use zomarrd\core\network\Network;
use const zOmArRD\PREFIX;
use const zOmArRD\Spawn_Data;

final class ResourcesManager
{
    /** @var string */
    public string $prefix, $serverName;

    public function init(): void
    {
        @mkdir($this->getNetwork()->getPlugin()->getDataFolder());
        $configYml = "config.yml";

        foreach (['config.yml', 'spawn.data.yml', 'network.data.yml', 'scoreboard.yml', 'npc.data.yml'] as $data) {
            $this->getNetwork()->getPlugin()->saveResource($data);
        }

        $mainYml = $this->getArchive($configYml);
        $spawnData = $this->getArchive("spawn.data.yml");

        $this->prefix = $this->getNetwork()->getTextUtils()->replaceColor($mainYml->get('prefix'));
        $database = $mainYml->get("database");
        $spawn_options = $spawnData->get("spawn.data");

        define("zOmArRD\PREFIX", $this->prefix);
        define("zOmArRD\DB", $database);
        define("zOmArRD\Spawn_Data", $spawn_options);

        if (Spawn_Data['is.enabled']) {
            $level = Spawn_Data['world.name'];
            if (!$this->getNetwork()->getServerPM()->isLevelLoaded($level)){
                $this->getNetwork()->getServerPM()->loadLevel($level);
            }
            $this->getNetwork()->getServerPM()->getLevelByName($level)->setTime(Level::TIME_DAY);
            $this->getNetwork()->getServerPM()->getLevelByName($level)->stopTime();
        }

        LangManager::$config = $this->getArchive($configYml);

        foreach (LangManager::$config->get("languages") as $language) {
            $iso = $language["ISOCode"];
            $this->getNetwork()->getPlugin()->saveResource("lang/$iso.yml");
            LangManager::$lang[$iso] = new Config($this->getNetwork()->getPlugin()->getDataFolder() . "lang/$iso.yml");
            LobbyCore::$logger->info(PREFIX . "Lang $iso ". $this->getNetwork()->getTextUtils()->uDecode("0:&%S(&)E96X@;&]A9&5D(0```"));
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