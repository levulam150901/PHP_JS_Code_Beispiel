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
class News extends Page
{
    // to do: declare reference variables for members
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    private $JSON = false;
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
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

    protected function getLocalizedDate($date){
        $date = new DateTime($date);
        if
        (strpos($_SERVER['HTTP_ACCEPT_LANGUAGE']," de-DE")>-1){
            return $date->format("d.m.Y H:i:s"); }
        else { // English as default
            return $date->format("Y/m/d H:i:s"); }
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
        $sqlAbfrage = "SELECT * FROM news ORDER BY timestamp DESC;";
        $recordSet = $this->db->query($sqlAbfrage);

        if(!$recordSet){
            throw new Exception("keine News vorhanden in Datenbank");
        }

        $newsArray = array();
        $count = 0;

        while ($record = $recordSet->fetch_assoc()){
            $record["timestamp"] = $this->getLocalizedDate($record["timestamp"]);
            $newsArray[$count] = $record;
            $count++;
        }

        $recordSet->free();

        return $newsArray;
    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     */
    protected function generateJSONView () {
        header("Content-Type: application/json; charset=UTF-8");
        $data = $this->getViewData();
        $jsonView = json_encode($data);
        echo $jsonView;
    }
    protected function generateHTMLView():void
    {
        $data = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('News'); //to do: set optional parameters

        echo <<< HTML
        <body onload="pollNews();">
            <img src="logo.png" alt="HDA-Logo">
            <h1>Meine Zeitung</h1>
            <nav class="horizontal_nav">
                <ul>
                    <li class="horizontal-li">Home</a></li>
                    <li class="horizontal-li" >Mediathek</li>
                    <li class="horizontal-li">Politik</li>
                    <li class="horizontal-li">Sport</li>
                </ul>
            </nav> 

            <div id="news_container"></div>
            
            <form action="News.php" method="post" accept-charset="UTF-8">
                <div name="createNew">
                    <h1>Ihre News</h1>
                    <input type="text" name="titleOfNew" placeholder="Title Ihrer News">
                    <input type="text" name="textOfNew" placeholder="Ihrer News">
                    <input type="submit" value="Absenden">
                </div>
            </form>
        <footer><p>&copy; Fachbereich Informatik</p></footer>
        </body>
HTML;
        $this->generatePageFooter();
    }

    protected function generateView () {
        if($this->JSON){
            $this->generateJSONView();
        }
        else{
            $this->generateHTMLView();
        }
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

        if(isset($_GET["JSON"])){
            $this->JSON = true;
        }

        if(isset($_POST["titleOfNew"]) && isset($_POST["textOfNew"])){
            $title = $this->db->real_escape_string($_POST["titleOfNew"]);
            $text = $this->db->real_escape_string($_POST["textOfNew"]);

            if((strlen($title) <= 0) || (strlen($text) <= 0)){
                throw new Exception("Bitte Felder eingeben");
            }

            $currentDateTime = date("Y-m-d H:i:s");
            $datum = $this->db->real_escape_string($this->getLocalizedDate($currentDateTime));

            $sqlAbfrage = "INSERT INTO news (timestamp, title, text) VALUES ('$datum', '$title', '$text');";
            $this->db->query($sqlAbfrage);

            header('Location:news.php');
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
            $page = new News();
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
News::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >