<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
require_once './Page.php';

class CalculateHash extends Page
{

    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData():array
    {
        $sqlAbfrage = "SELECT * FROM hash2URL;";
        $recordSet = $this->_database->query($sqlAbfrage);

        if(!$recordSet){
            throw new Exception("Keine Daten in Datenbank");
        }

        $hashURLLinkArray = array();
        $elementInfo = array();
        $count = 0;

        while ($record = $recordSet->fetch_assoc()){
            $elementInfo["id"] = $record["id"];
            $elementInfo["timestamp"] = $record["timestamp"];
            $elementInfo["url"] = $record["url"];
            $elementInfo["hash"] = $record["hash"];
            $hashURLLinkArray[$count] = $elementInfo;
            $count++;
        }

        $recordSet->free();

        return $hashURLLinkArray;

    }

    protected function generateView():void
    {

    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if(isset($_GET["url"])){
            $url = $_GET["url"];
            $_SESSION[$url] = hash('cr23', $url);
        }
    }

    public static function main():void
    {
        try {
            $page = new CalculateHash();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

CalculateHash::main();
