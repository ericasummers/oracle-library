<?php
    class Library
    {
        private $id;
        private $name;
        private $checked_out;
        private $books_available;

        function __construct($name, $id = null)
        {
            $this->name = $name;
            $this->id = $id;
            $this->checked_out = array();
            $this->books_available = array();
        }

        function getName()
        {
            return $this->name;
        }

        function setName($new_name)
        {
            $this->name = (string) $new_name;
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

        //add book object to library_books
        function addBook($new_book)
        {
            $library_id = $this->getId();
            $book_id = $new_book->getId();

            $GLOBALS['DB']->exec("INSERT library_books (library_id, book_id) VALUES ({$library_id}, {$book_id});");

        }

        function getLibraryBooks()
        {
            $books_query = $GLOBALS['DB']->query("SELECT book_id FROM library_books WHERE library_id = {$this->getId()};");
            $library_books = array();
            foreach($books_query as $book_id) {
                $new_book = Book::find($book_id['book_id']);
                array_push($library_books, $new_book);
            }

            return $library_books;
        }

        function deleteBook($book_object)
        {
            $library_id = $this->getId();
            $book_id = $book_object->getId();

            $GLOBALS['DB']->exec("DELETE FROM library_books WHERE book_id = {$book_id};");


        }


    }

?>
