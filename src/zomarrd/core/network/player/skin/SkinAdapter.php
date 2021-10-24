<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 24/10/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\network\player\skin;

use Exception;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\types\LegacySkinAdapter;
use pocketmine\network\mcpe\protocol\types\SkinData;

final class SkinAdapter extends LegacySkinAdapter
{
    /** @var array */
    private array $personaSkins = [];

    /**
     * @param SkinData $data
     *
     * @return Skin
     * @throws Exception
     */
    public function fromSkinData(SkinData $data): Skin
    {
        if ($data->isPersona()) {
            $id = $data->getSkinId();
            $this->personaSkins[$id] = $data;
            return new Skin($id, str_repeat(random_bytes(3) . "\xff", 2048));
        }
        return parent::fromSkinData($data);
    }

    /**
     * @param Skin $skin
     *
     * @return SkinData
     */
    public function toSkinData(Skin $skin): SkinData
    {
        return $this->personaSkins[$skin->getSkinId()] ?? parent::toSkinData($skin);
    }
}