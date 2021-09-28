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

use pocketmine\Player;
use zomarrd\core\modules\lang\LangManager;

final class NetworkPlayer extends Player
{
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
}