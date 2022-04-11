<?php 

namespace davidglitch04\TopUpCards\Command;

use pocketmine\{
    player\Player,
    command\Command,
    command\CommandSender
};
use davidglitch04\TopUpCards\{
    Main as TopUpCards,
    UI\TopForm
};

class TopNapThe extends Command{

    protected TopUpCards $plugin;

    public function __construct(TopUpCards $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("topnapthe");
        $this->setDescription("TopNapThe Form");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            new TopForm($sender);
        } else{
            $sender->sendMessage("Please use this command in game!");
        }
    }
}