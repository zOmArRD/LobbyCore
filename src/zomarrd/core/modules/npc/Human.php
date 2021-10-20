<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 1/10/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\modules\npc;

use Exception;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Server;
use zomarrd\core\modules\npc\entity\HumanEntity;
use zomarrd\core\network\Network;
use zomarrd\core\network\player\NetworkPlayer;
use const zOmArRD\Spawn_Data;

final class Human
{
    /**
     * This registers the entity, which allows it to stand & handle the events.
     */
    public static function register(): void
    {
        Entity::registerEntity(HumanEntity::class, true);
    }

    /**
     * @param string $name
     * @param NetworkPlayer $player
     */
    public static function spawn(string $name, NetworkPlayer $player): void
    {
        $level = $player->getLevel();
        $position = $player->getPosition();
        $skinData = $player->getSkin();

        foreach ($level->getEntities() as $entity) if ($entity instanceof HumanEntity) {
            if ($entity->getSkin()->getSkinId() == $name) $entity->kill();
        }

        $nbt = new CompoundTag("", [
            new ListTag("Pos", [
                new DoubleTag("", $position->getX()),
                new DoubleTag("", $position->getY()),
                new DoubleTag("", $position->getZ())
            ]),
            new ListTag("Motion", [
                new DoubleTag("", 0),
                new DoubleTag("", 0),
                new DoubleTag("", 0)
            ]),
            new ListTag("Rotation", [
                new FloatTag("", $player->getYaw()),
                new FloatTag("", $player->getPitch())
            ]),
            new CompoundTag("Skin", [
                new StringTag("Data", $skinData->getSkinData()),
                new StringTag("Name", $skinData->getSkinId()),
            ]),
        ]);

        self::setPosition($name, $player);
        $human = new HumanEntity($player->getLevel(), $nbt);
        $human->setSkin(new Skin($name, $skinData->getSkinData(), $skinData->getCapeData(), $skinData->getGeometryName(), $skinData->getGeometryData()));
        $human->setNameTagAlwaysVisible();
        $human->setNameTagVisible();
        $human->setNameTag("§c" . "server.not.found");
        $human->setImmobile();
        $human->spawnToAll();
    }

    /**
     * @param string $name
     * @param string $text
     */
    public static function applyName(string $name, string $text): void
    {
        foreach (Server::getInstance()->getLevels() as $level) {
            foreach ($level->getEntities() as $entity) if ($entity instanceof HumanEntity) if ($entity->getSkin()->getSkinId() == $name) $entity->setNameTag($text);
        }
    }

    /**
     * @param string $name
     */
    public static function purge(string $name): void
    {
        $level = Spawn_Data['is.enabled'] ? self::getNetwork()->getServerPM()->getLevelByName(Spawn_Data['world.name']) : self::getNetwork()->getServerPM()->getDefaultLevel()->getName();

        foreach ($level->getEntities() as $entity) if ($entity instanceof HumanEntity) if ($entity->getSkin()->getSkinId() == $name) $entity->kill();
    }

    public static function getId(HumanEntity $human): string
    {
        return $human->getSkin()->getSkinId();
    }

    /**
     * @return Network
     */
    public static function getNetwork(): Network
    {
        return new Network();
    }

    /**
     * @param string        $name
     * @param NetworkPlayer $player
     */
    public static function setPosition(string $name, NetworkPlayer $player): void
    {
        $config = (new Network())->getResourceManager()->getArchive("npc.data.yml");
        $cGet = $config->getAll();
        $cGet[$name]['X'] = $player->getX();
        $cGet[$name]['Y'] = $player->getY();
        $cGet[$name]['Z'] = $player->getZ();

        $config->setAll($cGet);
        $config->save();
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return int|float|null
     */
    public static function getPosition(string $name, string $type): int|float|null
    {
        $config = (new Network())->getResourceManager()->getArchive("npc.data.yml")->getAll();
        try {
            return $config[$name][$type];
        } catch (Exception) {
            return null;
        }
    }
}