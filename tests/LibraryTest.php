<?php
    /**
    * @backupGlobals disabled
    * #backupStaticAttributes disabled
    */

    require_once "src/Book.php";
    require_once "src/Library.php";

    $server = 'mysql:host=localhost:8889;dbname=oracle_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class LibraryTest extends PHPUnit_Framework_TestCase
    {

        protected function tearDown()
        {
            Library::deleteAll();
            Book::deleteAll();
        }

        function test_saveAndGetAll()
        {
            $name = "Portland Library";
            $new_library = new Library($name);
            $new_library->save();

            $result = Library::getAll();

            $this->assertEquals([$new_library], $result);
        }







    }

?>
