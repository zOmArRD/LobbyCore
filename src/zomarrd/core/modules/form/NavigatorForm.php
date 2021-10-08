<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 29/9/2021
 *
 * Copyright © 2021 Greek Network - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\core\modules\form;

use Exception;
use zomarrd\core\modules\form\lib\SimpleForm;
use zomarrd\core\network\Network;
use zomarrd\core\network\player\NetworkPlayer;
use zomarrd\core\network\server\ServerManager;
use zomarrd\core\network\utils\TextUtils;

final class NavigatorForm
{
    public function __construct(NetworkPlayer $player)
    {
        $this->show($player);
    }

    /**
     * @param NetworkPlayer $player
     */
    public function show(NetworkPlayer $player): void
    {
        $form = new SimpleForm(function (NetworkPlayer $player, $data) {
            if (isset($data)) {
                if ($data === "close") return;
                switch ($data) {
                    case "close":
                        return;
                    case "HCF":
                    case "Practice":
                        $player->transferServer($data);
                        break;
                }
            }
        });

        $images = ["close" => "textures/gui/newgui/anvil-crossout"];

        $form->setTitle(TextUtils::replaceColor($player->getLangTranslated("form.title.navigator")));
        $form->setContent(TextUtils::replaceVars($player->getLangTranslated("form.content.navigator"), ["{player.get.name}" => $player->getName()]));
        $config = (new Network())->getResourceManager()->getArchive("network.data.yml");
        try {
            foreach ($config->get("servers.availables") as $button) {
                $form->addButton("§6{$button['server.name']}" . "\n" . ServerManager::getServerPlayers($button['server.name']), $button['image.type'], $button['image.link'], $button['server.name']);
            }
        } catch (Exception) {
        }
        $form->addButton(TextUtils::replaceColor($player->getLangTranslated('form.button.close')), 0, $images['close'], 'close');
        $player->sendForm($form);
    }
}