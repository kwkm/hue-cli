<?php
/**
 * Created by PhpStorm.
 * User: kwkm
 * Date: 15/04/26
 * Time: 23:09
 */

use GuzzleHttp\Client;
use Kwkm\OptParser\OptionParser;

require_once __DIR__ . '/vendor/autoload.php';

class LightsOn
{
    /**
     * @var Kwkm\OptParser\OptionParser
     */
    private $arg;
    private $host;
    private $api;

    public function run()
    {
        $this->checkArg();
        $lists = $this->getLightLists();

        $client = new Client();
        foreach ($lists as $id => $name) {
            $client->put(
                "http://{$this->host}/api/{$this->api}/lights/{$id}/state",
                ['json' => ['on' => true]]
            );
            echo "{$name}...on" . PHP_EOL;
        }
    }

    private function getLightLists()
    {
        $client = new Client();
        $response = $client->get("http://{$this->host}/api/{$this->api}/lights")->json();

        $result = array();
        foreach ($response as $id => $value) {
            $result[$id] = $value['name'];
        }

        return $result;
    }

    private function checkArg()
    {
        $arg = $this->arg->getOption();

        if ((!isset($arg['-H'])) or (!isset($arg['-u']))) {
            $this->showUsage();
        }

        $this->host = $arg['-H'];
        $this->api = $arg['-u'];
    }

    private function showUsage()
    {
        echo 'Usage: php LightsOn.php -H <IP> -u <username>' . PHP_EOL;
        exit;
    }

    public function __construct($argv)
    {
        $this->arg = new OptionParser($argv);
    }
}

$hue = new LightsOn($argv);
$hue->run();
