<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Book.php";
    require_once "src/Library.php";

    $server = 'mysql:host=localhost:8889;dbname=oracle_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BookTest extends PHPUnit_Framework_TestCase
    {

        protected function tearDown()
        {
            Book::deleteAll();
            Library::deleteAll();
        }

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

        function test_find()
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

            $title = "A Tale of Another City";
            $first_name = "Charles";
            $last_name = "Dickens";
            $full_name = $first_name . " " . $last_name;
            $authors2 = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
            $summary = "A story about something";
            $category = "fiction";

            $new_book2 = new Book($title, $authors2, $summary, $category);
            $new_book2->save();

            $result = Book::find($new_book2->getId());

            $this->assertEquals($new_book2, $result);
        }



    }

?>
