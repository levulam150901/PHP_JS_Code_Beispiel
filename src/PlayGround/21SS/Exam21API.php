<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Exam21API extends Page
{
    private $gameId;
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
        $array = array();
        $sql = "SELECT count(*) AS playing FROM gameDetails WHERE gameId = '$this->gameId'";
        $recordSet = $this->_database->query($sql);
        if(!$recordSet) {
            throw new Exception("keine Daten in der DB");
        }

        $count = 0;

        while ($record = $recordSet->fetch_assoc()) {
            $array[$count]= $record;
            $count++;
        }
        $recordSet->free();

        return $array;

    }

    protected function generateView():void
    {
        header("Content-Type: application/json; charset=UTF-8");
        $data = $this->getViewData();
        $serializedData = json_encode($data);
        echo $serializedData;
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if(isset($_GET["gameID"])){
            $this->gameId = $this->_database->real_escape_string($_GET["gameID"]);
        }
    }

    public static function main():void
    {
        try {
            $page = new Exam21API();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Exam21API::main();