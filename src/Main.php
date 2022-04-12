<?php

namespace davidglitch04\TopUpCards;

use pocketmine\{
    plugin\PluginBase,
    event\Listener,
    Server
};
use davidglitch04\TopUpCards\{
    Command\NapThe,
    Provider\Provider
};
use davidglitch04\TopUpCards\Command\TopNapThe;
use pocketmine\event\player\PlayerJoinEvent;

class Main extends PluginBase implements Listener{

    protected Provider $provider;

    private static $instance;

    protected function onLoad(): void
    {
        $this->provider = new Provider($this);
        self::$instance = $this;
    }

    protected function onEnable(): void
    {
        $this->provider->open();
        Server::getInstance()->getCommandMap()->register('napthe', new NapThe($this));
        Server::getInstance()->getCommandMap()->register('topnapthe', new TopNapThe($this));
        Server::getInstance()->getPluginManager()->registerEvents($this, $this);
    }

    public static function getInstance(): self{
        return self::$instance;
    }

    protected function onDisable(): void
    {
        $this->getProvider()->save();
    }

    public function getProvider(): Provider{
        return $this->provider;
    }

    public function getPartnerId(): string{
        $partnerid = $this->getProvider()->config->get("PartnerId", '');
        if($partnerid == ''){
            return 'PartnerIdHack';
        } else{
            return $partnerid;
        }
    }

    public function getPartnerKey(): string{
        $partnerkey = $this->getProvider()->config->get("PartnerKey", '');
        if($partnerkey == ''){
            return 'PartnerKeyHack';
        } else{
            return $partnerkey;
        }
    }

    public function onJoin(PlayerJoinEvent $event): void{
        $player = $event->getPlayer();
        $this->getProvider()->createData($player);
    }

    public function convertValue(int $value): int{
        $config = $this->getProvider()->config->getAll();
        $array = [
            "10k" => $config["10k"],
            "20k" => $config["20k"],
            "30k" => $config["30k"],
            "50k" => $config["50k"],
            "100k" => $config["100k"],
            "200k" => $config["200k"],
            "500k" => $config["500k"]
        ];
        $value = (int) $value/1000;
        $convert = $array[$value."k"];
        $core = (int) $config["Event"];
        return (int) $convert*$core;
    }

    public function getCommand(string $playername, int $amount): string{
        $subject = $this->getProvider()->config->getAll()["Command"];
        $search = ["{player}", "{amount}"];
        $replace = [$playername, $amount];
        return str_replace($search, $replace, $subject);
    }

    public function getUrl(): string{
        $config = $this->getProvider()->config->getAll();
        return "https://{$config["Website"]}//chargingws/v2";
    }
}
