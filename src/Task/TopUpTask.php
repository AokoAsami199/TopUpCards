<?php 

namespace davidglitch04\TopUpCards\Task;

use pocketmine\{
    scheduler\AsyncTask,
    Server 
};
use davidglitch04\TopUpCards\{
    Main as TopUpCards,
    UI\SuccessForm
};

class TopUpTask extends AsyncTask{

    protected array $website;

    protected array $info;

    public function __construct(array $website, array $info)
    {
        $this->website = $website;
        $this->info = $info;
    }

    public function getPlugin(): TopUpCards{
        return TopUpCards::getInstance();
    }

    public function onRun(): void
    {
        $api_url = $this->getPlugin()->getUrl();
        $api_url = $api_url->getUrl();
			$data_sign = md5($this->website[1] . $this->info[2] . $this->seri[3]);		
			$arrayPost = array(
	            "telco" => $this->info[0],
	            "code" => $this->info[2],
	            "serial" => $this->info[3],
	            "amount" => $this->info[1],
	            "request_id" => intval(time()),
	            "partner_id" => $this->website[0],
	            "sign" => $data_sign,
	            "command" => "charging"
            );
        $curl = curl_init($api_url);
            curl_setopt_array($curl, array(
            CURLOPT_POST => true,
            CURLOPT_HEADER => false,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => http_build_query($arrayPost)
        ));
        $data = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($data, true);
        $content= [
            "arrayPost" => $arrayPost,
            "web_status" => $status,
            "result" => $result
        ];
        $this->setResult($content);	
    }

    public function onCompletion(): void
    {
        $result = $this->getResult();
        $player = Server::getInstance()->getPlayerByPrefix($this->info[4]);
        if(!isset($result)){
            Server::getInstance()->getLogger()->info("Result not have any thing!");
        }
        if($result["result"] == false){
            $txt = "Website error!";
			if($player == null){
				return;
			}
			$player->sendMessage($txt);			
            new SuccessForm($player, $txt, 0, 2);
        } 
        if($result["web_status"] == 200){
            if($result["result"]["status"] == 99){
                Server::getInstance()->getAsyncPool()->submitTask(new CheckTask($result["arrayPost"], $player));
				if(is_null($player)){		
					return;
				}	
				$player->sendTip("Checking...");
				return;
            }
            if(is_null($player)){
                return;
            }
            if($result["result"]["status"] == 4){
                $txt = "Telcos is under maintenance!";
                $player->sendMessage($txt);
                new SuccessForm($player, $txt, 0, 2);
            } elseif($result["result"]["status"] == 100){
                $txt = 
                "Telcos ".$this->info[0]."\n\n".
                "Serial number ".$this->info[3]."\n\n".
                "Pin number ".$this->info[2]."\n\n".
                "Amount ".$this->info[1]."\n\n".
                "Error: ".$result["result"]["message"]."n\n\n";
                $player->sendMessage($txt);
                new SuccessForm($player, $txt, 0, 2);
            }else{
                $txt = "Inbox with admin";
                $player->sendMessage($txt);
                new SuccessForm($player, $txt, 0, 2);
            }
        }
    }
}