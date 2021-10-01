<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 11/5/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\modules\npc\entity;

use pocketmine\entity\Human as PMHuman;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class HumanEntity extends PMHuman
{
    /**
     * @param Level       $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);
        $this->propertyManager->setFloat(self::DATA_SCALE, 1.00);
    }
}