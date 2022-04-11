<?php 

namespace davidglitch04\TopUpCards\UI;

use pocketmine\player\Player;
use davidglitch04\TopUpCards\Main as TopUpCards;
use jojoe77777\FormAPI\CustomForm;

class TopForm{

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->openForm($this->player);
    }

    public function getPlugin(): TopUpCards{
        return TopUpCards::getInstance();
    }

    private function openForm(Player $player){
        $all = $this->getPlugin()->getProvider()->getAll();
		$form = new CustomForm(function(Player $player, $data) use ($all){
            if(!isset($data)){
                return false;
            }
        });
        $form->setTitle("Top Nap The");
		arsort($all);
		$i = 1;
		foreach($all as $name => $vnd){
			$form->addLabel("Top ".$i.": ".$name." donate ".$vnd." VNĐ\n");
			if($i >= 10) break;
			++$i;
		}
		$player->sendForm($form);
    }
}