<?php
    class Library
    {
        private $id;
        private $name;
        private $books;
        private $checked_out;
        private $books_available;

        function __construct($name, $id = null)
        {
            $this->name = $name;
            $this->id = $id;
        }

        function getName()
        {
            return $this->name;
        }

        function setName($new_name)
        {
            $this->name = (string) $new_name;
        }

        function getBooks()
        {
            return $this->books;
        }

        function setBooks($new_books)
        {
            $this->book = $new_books;
        }

        function getCheckedOut()
        {
            return $this->checked_out;
        }

        function setCheckedOut($checked_out)
        {
            $this->checked_out = $checked_out;
        }

        function getBooksAvailable()
        {
            return $this->books_available;
        }

        function setBooksAvailable($books_available)
        {
            $this->books_available = $books_available;
        }

        function getId()
        {
            return $this->id;
        }

        function setId($id)
        {
            $this->id = $id;
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO libraries (name) VALUES ('{$this->getName()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll()
        {
            $all_libraries = array();
            $query = $GLOBALS['DB']->query("SELECT * FROM libraries;");
            foreach ($query as $library) {
                $name = $library['name'];
                $id = $library['id'];
                $new_library = new Library($name, $id);
                array_push($all_libraries, $new_library);
            }
            return $all_libraries;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM libraries;");
        }

    }

?>
