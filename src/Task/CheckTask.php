<?php 

namespace davidglitch04\TopUpCards\Task;

use pocketmine\{
    scheduler\AsyncTask,
    player\Player,
    Server 
};
use davidglitch04\TopUpCards\{
    Main as TopUpCards,
    UI\SuccessForm
};

class checkTask extends AsyncTask
{
	protected array $arrayPost;

	protected Player $player;

	public function __construct(array $arrayPost, Player $player){
		$this->arrayPost = $arrayPost;
		$this->player = $player;
	}

    public function getPlugin(): TopUpCards{
        return TopUpCards::getInstance();
    }

    public function onRun(): void
    {
            $api_url = $this->getPlugin()->getUrl();
			$arrayPost = $this->arrayPost;
			$arrayPost["command"] = "check";
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
				"web_status" => $status,
				"result" => $result
			];
			$this->setResult($content);			
    }

    public function onCompletion(): void
    {
        $result = $this->getResult();
        if(!isset($result)){
            Server::getInstance()->getLogger()->info("Result not have any thing!");
        }
        $player = $this->player;
		if($result["web_status"] == 200){
			if($result["result"]["status"] == 1){
                $value = (int) $result["result"]["value"];
				new SuccessForm($player, "", $value, 2);
				return;
			}
			if($result["result"]["status"] == 99){
				Server::getInstance()->getAsyncPool()->submitTask(new CheckTask($this->arrayPost, $player));
				if(is_null($player)){		
					return;
				}	
				$player->sendTip("Checking...");
				return;
			}
			if(is_null($player)){		
                return;
            }	
			if($result["result"]["status"] == 2){
				$txt = 
				"Mệnh Giá Không Chính Xác\n\n".
				"Giá Trị Thực: ".$result["result"]["value"]."\n\n".
				"Giá Trị Được Chọn: ".$result["result"]["declared_value"]."\n\n".
                "Inbox Admin Để Được Hỗ Trợ!";
				$player->sendMessage($txt);				
				new SuccessForm($player, $txt, 0, 2);
			} else{
				$txt = 
				"Đã Có Lỗi Phát Sinh\n\n".
				"Mã Giao Dịch: ".$result["result"]["request_id"]."\n\n".
				"Thông Tin Lỗi: ".$result["result"]["message"]."\n\n".
				"Nguyên Nhân: Có thể bạn đã nhập sai seri, mã thẻ. Hãy kiểm tra lại\n\n".
                "Inbox Admin Để Được Hỗ Trợ!";
				$player->sendMessage($txt);			
				new SuccessForm($player, $txt, 0, 2);
			}
		}
    }
}