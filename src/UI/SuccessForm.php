<?php 

namespace davidglitch04\TopUpCards\UI;

use pocketmine\{
    console\ConsoleCommandSender,
    player\Player,
    Server 
};
use davidglitch04\TopUpCards\Main as TopUpCards;
use jojoe77777\FormAPI\SimpleForm;

class SuccessForm{

    protected Player $player;

    protected string $txt;

    protected int $type, $value;

    public function __construct(Player $player, string $txt, int $value, int $type){
        $this->player = $player;
        $this->txt = $txt;
        $this->value = $value;
        if($type == 1){
            $this->TypeOne($player, $value);
        } elseif($type == 2){
            $this->TypeTwo($player, $txt);
        }
    }  

    public function getPlugin(): TopUpCards{
        return TopUpCards::getInstance();
    }
    
    private function TypeOne(Player $player, int $value){
        if($player->isConnected()){
            $amount = $this->getPlugin()->convertValue($value);
            $command = $this->getPlugin()->getCommand($player->getName(), $amount);
            $server = new Server::getInstance();
            $server->getCommandMap()->dispatch(new ConsoleCommandSender($server, $server->getLanguage()), $command);
            TopUpCards::getInstance()->getProvider()->Add($player, $amount);
            $txt = 
		    "Success\n\n".
		    "Amount: {$value} VND\n\n".
		    "Recive: {$amount} Coins\n\n".
		    "Thanks\n\n";
            $this->TypeTwo($player, $txt);
        }
    }

    private function TypeTwo(Player $player, string $txt){
        $form = new SimpleForm(function (Player $player, $data){
            if(!isset($data)){
                return false;
            }
        });
        $form->setTitle("TopUpCards Success");
        $form->setContent($txt);
        $form->addButton("Exit");
        $player->sendForm($form);
        return $form;
    }
}