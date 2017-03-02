<?php
    class Patron
    {
        private $first_name;
        private $last_name;
        private $id;

        function __construct($first_name, $last_name, $id = null)
        {
            $this->first_name = $first_name;
            $this->last_name = $last_name;
            $this->id = $id;
        }

        function getFirstName()
        {
            return $this->first_name;
        }

        function setFirstName($new_name)
        {
            $this->first_name = (string) $new_name;
        }

        function getLastName()
        {
            return $this->last_name;
        }

        function setLastName($new_name)
        {
            $this->last_name = (string) $new_name;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            //insert first and last names into tables if they don't already exist
            $GLOBALS['DB']->exec("INSERT IGNORE INTO first_names (first_name) VALUES ('{$this->getFirstName()}');");
            $GLOBALS['DB']->exec("INSERT IGNORE INTO last_names (last_name) VALUES ('{$this->getLastName()}');");
            //get ids for first and last names
            $query = $GLOBALS['DB']->query("SELECT id FROM first_names WHERE first_name = '{$this->getFirstName()}';");
            $rs = $query->fetchAll(PDO::FETCH_ASSOC);
            $first_name_id = $rs[0]['id'];

            $query = $GLOBALS['DB']->query("SELECT id FROM last_names WHERE last_name = '{$this->getLastName()}';");
            $rs = $query->fetchAll(PDO::FETCH_ASSOC);
            $last_name_id = $rs[0]['id'];

            $GLOBALS['DB']->exec("INSERT INTO patrons (first_name_id, last_name_id) VALUES ({$first_name_id}, {$last_name_id});");
            $this->id = $GLOBALS['DB']->lastInsertId();

        }

        static function getAll()
        {
            $returned_patrons = $GLOBALS['DB']->query("SELECT * FROM patrons;");
            $all_patrons = array();
            foreach($returned_patrons as $patron) {
                $first_name_id = $patron['first_name_id'];
                $last_name_id = $patron['last_name_id'];
                $id = $patron['id'];
                $query = $GLOBALS['DB']->query("SELECT first_name FROM first_names WHERE id = {$first_name_id};");
                $rs = $query->fetchAll(PDO::FETCH_ASSOC);
                $first_name = $rs[0]['first_name'];

                $query = $GLOBALS['DB']->query("SELECT last_name FROM last_names WHERE id = {$last_name_id};");
                $rs = $query->fetchAll(PDO::FETCH_ASSOC);
                $last_name = $rs[0]['last_name'];

                $new_patron = new Patron($first_name, $last_name, $id);
                array_push($all_patrons, $new_patron);
            }
            return $all_patrons;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM patrons;");
        }

        static function find($search_id)
        {
            $query = $GLOBALS['DB']->query("SELECT * FROM patrons WHERE id = {$search_id};");
            $rs = $query->fetchAll(PDO::FETCH_ASSOC);
            $first_name_id = $rs[0]['first_name_id'];
            $last_name_id = $rs[0]['last_name_id'];

            $query = $GLOBALS['DB']->query("SELECT first_name FROM first_names WHERE id = {$first_name_id};");
            $rs = $query->fetchAll(PDO::FETCH_ASSOC);
            $first_name = $rs[0]['first_name'];

            $query = $GLOBALS['DB']->query("SELECT last_name FROM last_names WHERE id = {$last_name_id};");
            $rs = $query->fetchAll(PDO::FETCH_ASSOC);
            $last_name = $rs[0]['last_name'];

            return $new_patron = new Patron($first_name, $last_name, $search_id);
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM patrons WHERE id = {$this->getId()};");
        }

        function getCheckedoutBooks($library)
        {
            $patron_books = array();
            $query = $GLOBALS['DB']->query("SELECT book_id FROM library_books WHERE checkout_patron_id = {$this->getId()} AND library_id = {$library->getId()};");
            foreach($query as $book_id) {
                $id = $book_id['book_id'];
                $new_book = Book::find($id);
                array_push($patron_books, $new_book);
            }
            return $patron_books;
        }


    }


?>
