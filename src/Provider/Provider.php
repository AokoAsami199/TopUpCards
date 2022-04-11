<?php 

namespace davidglitch04\TopUpCards\Provider;

use pocketmine\{
    player\Player,
    utils\Config
};
use davidglitch04\TopUpCards\Main as TopUpCards;

class Provider {

    protected TopUpCards $plugin;

    public Config $config, $list;

    public function __construct(TopUpCards $plugin)
    {
        $this->plugin = $plugin;
    }

    public function open(): void{
        $this->plugin->saveDefaultConfig();
        $this->config = $this->plugin->getConfig();
        $this->list = new Config($this->plugin->getDataFolder()."list.yml", Config::YAML);
    }

    public function DataExists(string $username): bool{
        if($this->list->exists($username)){
            return true;
        } else{
            return false;
        }
    }

    public function createData(Player $player): void{
        $username = strtolower($player->getName());
        if(!$this->DataExists($username)){
            $this->list->set($username, 0);
            $this->list->save();
        }
    }

    public function getValue(Player $player): int{
        $username = strtolower($player->getName());
        return $this->getAll()[$username];
    }

    public function Add(Player $player, $value = 0): void{
        $username = strtolower($player->getName());
        $this->list->set($username, $this->getValue($player)+$value);
        $this->list->save();
    }

    public function getAll(): array{
        return (array) $this->list->getAll();
    }

    public function save(): void{
        $this->list->save();
    }
}