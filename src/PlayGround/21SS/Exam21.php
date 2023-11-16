<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Exam21 extends Page
{

    protected function __construct()
    {
        parent::__construct();
    }


    public function __destruct()
    {
        parent::__destruct();
    }
    protected function checkStatus($data){
        $statusCheck = false;
        foreach ($data as $spiel){
            if(($spiel["status"] == 1) or ($spiel["status"] == 2)){
                $statusCheck = true;
            }
        }
        return $statusCheck;
    }

    protected function getViewData():array
    {
        $array = array();
        $sqlRequestCommand = "SELECT * FROM  games ORDER BY status DESC;";
        $recordSet = $this->_database->query($sqlRequestCommand);
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

    protected function generateSpielInfo($data){
        $status = htmlspecialchars($data["status"]);
        $datum = htmlspecialchars($data["datetime"]);
        $gegen = htmlspecialchars($data["opposingTeam"]);

        if($status == 0){
            $status = "zukünftig";
        }
        elseif ($status == 1){
            $status = "in Planung";
        }
        elseif ($status == 2){
            $status = "mit abgeschlossener Planung";
        }
        elseif ($status == 3){
            $status = "vorbei";
        }
        else{
            $status = "error";
        }

        echo <<< HTML
        <tr>
            <td>$datum</td>
            <td>$gegen</td>
            <td>$status</td>
        </tr>
HTML;
    }

    protected function generatePlanung($data){
        $gameID = htmlspecialchars($data["id"]);
        $datum = htmlspecialchars($data["datetime"]);
        $gegen = htmlspecialchars($data["opposingTeam"]);

        echo <<< HTML
        <h2>$datum gegen $gegen</h2>
        <p>Zusagen Spieler:innen <span id="zusage">?</span></p>
        <input type="hidden" name="gameID" id="gameID" value=$gameID>
        <input type="submit" name="finishGame" value="Planung abschließen">
        
HTML;
    }

    protected function generateView():void
    {
        $data = $this->getViewData();
        $this->generatePageHeader('Spiel');

        echo <<< HTML
    <body onload="pollData();">
        <img src="Logo.png" alt="fbi-logo">
        <h1>Spielplanung</h1>
HTML;

        if(sizeof($data) != 0){
            if(!$this->checkStatus($data)){
                echo <<< HTML
                <h2>kein aktuelles Spiel</h2>
HTML;
            }
        }

        echo <<< HTML
        <form action="Exam21.php" method="post" accept-charset="UTF-8">
            <div id="planung_container">
HTML;
        if(sizeof($data) != 0){
            foreach ($data as $spiel){
                $status = htmlspecialchars($spiel["status"]);
                if($status == 1 or $status == 2){
                    $this->generatePlanung($spiel);
                }
            }
        }

        echo <<< HTML
            </div>
        </form>
        <div id="spielInfo">
            <h2>Spiele</h2>
            <table>
                <tr>
                    <td>Datum</td>
                    <td>Team</td>
                    <td>Status</td>
                </tr>
HTML;

        if(sizeof($data) != 0){
            foreach ($data as $spiel){
                $this->generateSpielInfo($spiel);
            }
        }
        echo <<< HTML
            </table>
        </div>
    </body>
HTML;

    $this->generatePageFooter();
    }
    protected function processReceivedData():void
    {
        parent::processReceivedData();

        if(isset($_POST["gameID"]) and isset($_POST["finishGame"])){
            $gameId = $this->_database->real_escape_string($_POST["gameID"]);

            $sql = "SELECT * FROM games WHERE id = '$gameId';";
            $recordSet = $this->_database->query($sql);

            if ($recordSet->num_rows == 0) {
                $recordSet->free();
                throw new Exception("Keine Spiel gefunden!");
            }

            else{
                $sql = "UPDATE games SET status = 2 WHERE id = '$gameId'";
                $this->_database->query($sql);
            }
        }

    }

    public static function main():void
    {
        try {
            $page = new Exam21();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}


Exam21::main();

