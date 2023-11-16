<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     PageTemplate.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.1
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 */
class PosterShop extends Page
{
    // to do: declare reference variables for members
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    private $heading = "Finde das passende Poster für dein Wohnzimmer";
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
        session_start();
        if(!isset($_SESSION['zimmer'])){
            $_SESSION['zimmer'] = "Background.jpg";
        }

        if (!isset($_SESSION['kunde'])) {
            $_SESSION['kunde'] = 1;
        } else {
            $_SESSION['kunde'] = $_SESSION['kunde'] + 1;
        }
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array An array containing the requested data.
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData():array
    {
        // to do: fetch data for this view from the database
        // to do: return array containing data
        $sqlAbfrage = "SELECT * FROM poster;";
        $recordSet = $this->db->query($sqlAbfrage);

        if(!$recordSet){
            throw new Exception("Keine vorhandene Poster");
        }

        $posterArray = array();
        $count = 0;

        while($record = $recordSet->fetch_assoc()){
            $posterArray[$count] = $record["datei"];
            $count++;
        }

        $recordSet->free();

        return $posterArray;
    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     */

    protected function generatePoster($datei){

        echo <<< HTML
            <option>{$datei}</option>
HTML;

    }
    protected function generateView():void
    {
        $data = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('PosterShop'); //to do: set optional parameters

            echo <<< HTML
        <h1>$this->heading</h1>
        <div name="image_container">
            <img id="zimmer" src="Images/{$_SESSION['zimmer']}" alt="LivingRoomImg">
            <img id="poster" src="" alt="SelectedImg">
        </div>
        <div name="poster_container">
            <form action="PosterShop.php" method="post" accept-charset="UTF-8">
                <select name="posterSelect" id="posterSelect" style="display: none">
HTML;
        if(sizeof($data) != 0) {
            foreach ($data as $poster) {
            $this->generatePoster($poster);
            }
        }

        echo <<< HTML
                </select>
                <input type="button" value="Nächstes Poster" onclick="nextPoster();"/>
                <input type="submit" value="Poster bestellen"/>
            </form>
        </div>
HTML;

        // to do: output view of this page
        $this->generatePageFooter();
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
     * @return void
     */
    protected function processReceivedData():void
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members
        if(isset($_POST["posterSelect"])){
            $posterSelected = $this->db->real_escape_string($_POST["posterSelect"]);

            $sqlAbfrage = "INSERT INTO bestellung (kunde, datei) VALUES ('{$_SESSION['kunde']}', '$posterSelected')";
            $this->db->query($sqlAbfrage);

            $this->heading = "Vielen Dank für Ihre Bestellung";
        }
    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     * @return void
     */
    public static function main():void
    {
        try {
            $page = new PosterShop();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page.
// That is input is processed and output is created.
PosterShop::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >