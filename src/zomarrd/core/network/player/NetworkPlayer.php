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

namespace zomarrd\core\network\player;

use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use zomarrd\core\modules\lang\LangManager;
use zomarrd\core\network\Network;

final class NetworkPlayer extends Player
{
    /**
     * @return Network
     */
    public function getNetwork(): Network
    {
        return new Network();
    }

    /** @var LangManager */
    public LangManager $langSession;

    /**
     * Sets the Language Session to the player.
     */
    public function setLangSession(): void
    {
        $this->langSession = new LangManager($this);
    }

    /**
     * Returns the session of the player's Lang class.
     *
     * @return LangManager
     */
    public function getLangSession(): LangManager
    {
        return $this->langSession;
    }

    /**
     * @param string $idString
     *
     * @return string
     */
    public function getLangTranslated(string $idString): string
    {
        $session = $this->getLangSession();
        return $this->getNetwork()->getTextUtils()->replaceColor($session->getString($idString));
    }

    public function handleLevelSoundEvent(LevelSoundEventPacket $packet): bool
    {
        return true;
    }
}