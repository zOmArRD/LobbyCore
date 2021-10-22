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

namespace zomarrd\core\modules\floatingtext;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\utils\UUID;
use zomarrd\core\modules\npc\Human;
use zomarrd\core\network\player\NetworkPlayer;

abstract class FloatingText
{
    /** @var array */
    private static array $texts = [];

    /**
     * @param Vector3 $vector3
     *
     * @return int
     */
    public function create(Vector3 $vector3): int
    {
        $eid = Entity::$entityCount++;

        $pk = new AddPlayerPacket();
        $pk->username = "loading...";
        $pk->uuid = UUID::fromRandom();
        $pk->entityRuntimeId = $eid;
        $pk->entityUniqueId = $eid;
        $pk->position = $vector3;
        $pk->item = ItemStackWrapper::legacy(Item::get(0));
        $pk->metadata = [Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, 1 << Entity::DATA_FLAG_IMMOBILE], Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0]];

        self::$texts[$eid] = $pk;

        return $eid;
    }

    /**
     * @param int           $eid
     * @param NetworkPlayer $player
     * @param string        $text
     */
    public function send(int $eid, NetworkPlayer $player, string $text): void
    {
        /** @var AddPlayerPacket $pk */
        $pk = clone self::$texts[$eid];
        $pk->username = $text;

        $player->directDataPacket($pk);
    }

    /**
     * @param int           $eid
     * @param NetworkPlayer $player
     */
    public function purge(int $eid, NetworkPlayer $player): void
    {
        $pk = new RemoveActorPacket();
        $pk->entityUniqueId = $eid;

        $player->directDataPacket($pk);
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return int|float|null
     */
    public static function getNpcPosition(string $name, string $type): int|float|null
    {
        return Human::getPosition($name, $type);
    }
}