<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Book.php";
    require_once "src/Library.php";
    require_once "src/Patron.php";

    $server = 'mysql:host=localhost:8889;dbname=oracle_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class PatronTest extends PHPUnit_Framework_TestCase
    {

        protected function tearDown()
        {
            Patron::deleteAll();
            Book::deleteAll();
            Library::deleteAll();
        }

        function test_saveAndGetAll()
        {
            $first_name = "Bob";
            $last_name = "Smith";
            $new_patron = new Patron($first_name, $last_name);
            $new_patron->save();

            $result = Patron::getAll();

            $this->assertEquals([$new_patron], $result);
        }

        function test_find()
        {
            $first_name = "Bob";
            $last_name = "Smith";
            $new_patron = new Patron($first_name, $last_name);
            $new_patron->save();

            $first_name = "Jane";
            $last_name = "Doe";
            $new_patron2 = new Patron($first_name, $last_name);
            $new_patron2->save();

            $result = Patron::find($new_patron2->getId());

            $this->assertEquals($new_patron2, $result);
        }

        function test_delete()
        {
            $first_name = "Bob";
            $last_name = "Smith";
            $new_patron = new Patron($first_name, $last_name);
            $new_patron->save();

            $first_name = "Jane";
            $last_name = "Doe";
            $new_patron2 = new Patron($first_name, $last_name);
            $new_patron2->save();

            $new_patron->delete();
            $result = Patron::getAll();

            $this->assertEquals($new_patron2, $result[0]);
        }



    }







?>
