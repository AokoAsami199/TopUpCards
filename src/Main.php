<?php

namespace davidglitch04\TopUpCards;

use pocketmine\{
    plugin\PluginBase,
    utils\SingletonTrait,
    Server
};
use davidglitch04\TopUpCards\{
    Command\NapThe,
    Provider\Provider
};

class Main extends PluginBase{

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
}