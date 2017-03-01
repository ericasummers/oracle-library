<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Book.php";

    $server = 'mysql:host=localhost:8889;dbname=oracle_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BookTest extends PHPUnit_Framework_TestCase
    {

        // protected function tearDown()
        // {
        //     Book::deleteAll();
        // }

        function test_saveAndGetAll()
        {
            $title = "A Tale of Two Cities";
            $first_name = "Charles";
            $last_name = "Dickens";
            $full_name = $first_name . " " . $last_name;
            $authors = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
            $summary = "A story about the French revolution";
            $category = "fiction";

            $new_book = new Book($title, $authors, $summary, $category);
            $new_book->save();

            $result = Book::getAll();

            $this->assertEquals([$new_book], $result);
        }



    }

?>
