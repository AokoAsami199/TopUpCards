<?php 

namespace davidglitch04\TopUpCards\Utils;

use davidglitch04\TopUpCards\Main as TopUpCards;

class Utils{

    public static function getPlugin(): TopUpCards{
        return TopUpCards::getInstance();
    }

    public static function convertValue(int $value): int{
        $config = self::getPlugin()->getProvider()->config->getAll();
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

    public static function getCommand(string $playername, int $amount): string{
        $subject = self::getPlugin()->getProvider()->config->getAll()["Command"];
        $search = ["{player}", "{amount}"];
        $replace = [$playername, $amount];
        return str_replace($search, $replace, $subject);
    }

    public static function getUrl(): string{
        $config = self::getPlugin()->getProvider()->config->getAll();
        return "https://{$config["Website"]}//chargingws/v2";
    }
}