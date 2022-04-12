<?php 

namespace davidglitch04\TopUpCards\UI;

use pocketmine\{
    player\Player,
    Server 
};
use davidglitch04\TopUpCards\{
    Main as TopUpCards,
    Task\TopUpTask
};
use jojoe77777\FormAPI\CustomForm;

class PluginForm{

    protected Player $player;

    private static array $telcos = array('Viettel', 'Vietnamobi', 'Vinaphone', 'Mobifone', 'Zing', 'Gate');
    
    private static array $amount = array(10000 => "10.000 VND", 20000 => "20.000 VND", 50000 => "50.000 VND", 100000 => "100.000 VND", 200000 => "200.000 VND", 500000 => "500.000 VND");

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->openForm($this->player);
    }

    public function getPlugin(): TopUpCards{
        return TopUpCards::getInstance();
    }

    private function openForm(Player $player){
       $form = new CustomForm(function (Player $player, $data){
           if(!isset($data)){
               return false;
           }
           if(!isset($data[0]) or !isset($data[1]) or !isset($data[2]) or !isset($data[3])){
               $player->sendMessage("Vui lòng điền đủ thông tin");
               return;
           }
           if(!is_numeric($data[2]) or !is_numeric($data[3])){
               $player->sendMessage("Pin và Serial phải là số!");
               return;
           }
           $telcos = self::$telcos[$data[0]];
           $amount = array_keys(self::$amount)[$data[1]];
           Server::getInstance()->getAsyncPool()->submitTask(new TopUpTask(
               array($this->getPlugin()->getPartnerId(),
               $this->getPlugin()->getPartnerKey()),
               array(strtoupper($telcos),
               $amount,
               $data[2],
               $data[3],
               $player->getName())
            ));
            $player->sendMessage("Checking...");
       });
       $form->setTitle("TopUpCards");
       $form->addDropdown("Nha Mang", self::$telcos);
       $form->addDropdown("Menh Gia", array_values(self::$amount));
       $form->addInput("Pin:", "Pin");
       $form->addInput("Serial:", "Serial");
       $player->sendForm($form);
       return $form;
    }
}
