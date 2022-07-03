<?php

namespace SlidexDE\AntiAFK\tasks;

use SlidexDE\AntiAFK\Main;
use SlidexDE\AntiAFK\api\DiscordWebhookAPI\Embed;
use SlidexDE\AntiAFK\api\DiscordWebhookAPI\Message;
use SlidexDE\AntiAFK\api\DiscordWebhookAPI\Webhook;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class AFKTask extends Task {

    private $main;

    public function __construct(Main $main){
        $this->main = $main;
    }

    public function onRun(): void { 
        foreach ($this->main->afkTime as $playername => $time){
            $player = Server::getInstance()->getPlayerExact($playername);
            if($player){
                $now = new \DateTime("now", new \DateTimeZone($this->main->getConfig()->get('time.zone')));
                if ($time < $now) {
                    if(!$player->hasPermission("antiafk.bypass")) {
                        $player->kick($this->main->getConfig()->get('kick.message'));
                        $webhook = new Webhook($this->main->getConfig()->get('webhook'));
            $embed = new Embed();
            $msg = new Message();
            $embed->setTitle($this->main->getConfig()->get('webhook.title'));
            $embed->setDescription(str_replace("{player}", $player->getName(), $this->main->getConfig()->get("webhook.message")));
            $embed->setFooter(str_replace("{date}", date("d.m.Y") . " at " . date("H:i"), $this->main->getConfig()->get("webhook.footer")));
            $embed->setColor($this->main->getConfig()->get('webhook.color'));
            $msg->addEmbed($embed);
            $webhook->send($msg);
                    }
                }
            }
        }
    }
}