<?php

namespace davidglitch04\TopUpCards;

use pocketmine\{
    plugin\PluginBase,
    event\Listener,
    utils\SingletonTrait,
    Server
};
use davidglitch04\TopUpCards\{
    Command\NapThe,
    Provider\Provider
};
use davidglitch04\TopUpCards\Command\TopNapThe;
use pocketmine\event\player\PlayerJoinEvent;

class Main extends PluginBase implements Listener{

    use SingletonTrait;

    protected Provider $provider;

    protected function onLoad(): void
    {
        $this->provider = new Provider($this);
        self::setInstance($this); 
    }

    protected function onEnable(): void
    {
        $this->provider->open();
        Server::getInstance()->getCommandMap()->register('napthe', new NapThe($this));
        Server::getInstance()->getCommandMap()->register('topnapthe', new TopNapThe($this));
        Server::getInstance()->getPluginManager()->registerEvents($this, $this);
    }

    protected function onDisable(): void
    {
        $this->getProvider()->save();
    }

    public function getProvider(): Provider{
        return $this->provider;
    }

    public function getPartnerId(): string{
        return $this->getProvider()->config->get("PartnerId", '');
    }

    public function getPartnerKey(): string{
        return $this->getProvider()->config->get("PartnerKey", '');
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->getProvider()->createData($player);
    }
}