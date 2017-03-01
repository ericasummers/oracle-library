<?php
    class Book
    {
        private $id;
        private $title;
        private $authors;
        private $summary;
        private $category;
        //private $due_date;
        //private $patrons;

        //Title: string, Authors: array, Summary: string, Category: string, Id: integer
        function __construct($title, $authors, $summary, $category, $id = null)
        {
            $this->title = $title;
            $this->authors = $authors;
            $this->summary = $summary;
            $this->category = $category;
            $this->id = $id;
        }

        function getTitle()
        {
            return $this->title;
        }

        function setTitle($new_title)
        {
            $this->title = (string) $new_title;
        }

        function getAuthors()
        {
            return $this->authors;
        }

        function setAuthors($new_authors)
        {
            $this->authors = $new_authors;
        }

        function getSummary()
        {
            return $this->summary;
        }

        function setSummary($new_summary)
        {
            $this->summary = (string) $new_summary;
        }

        function getCategory()
        {
            return $this->category;
        }

        function setCategory($new_category)
        {
            $this->category = $new_category;
        }

        function getId()
        {
            return $this->id = $id;
        }

        function save()
        {
            //insert summary and category into books_descriptions table
            $GLOBALS['DB']->exec("INSERT IGNORE INTO books_descriptions (title, summary, category) VALUES ('{$this->getTitle()}', '{$this->getSummary()}', '{$this->getCategory()}');");

            $book_description_id = $GLOBALS['DB']->query("SELECT id FROM books_descriptions WHERE title = '{$this->getTitle()}';");
            $unique_book_id = null;
            foreach($book_description_id as $id) {
                $unique_book_id = $id['id'];
            }


            //Parse out authors
            foreach($this->getAuthors() as $author => $value) {

                $first_name = $value['first_name'];
                echo "First Name: " . $first_name . "\n";
                //this will add first name into table if unique
                $GLOBALS['DB']->exec("INSERT IGNORE INTO first_names (first_name) VALUES ('{$first_name}');");
                $first_name_id = $GLOBALS['DB']->query("SELECT id FROM first_names WHERE first_name = '{$first_name}';");

                $last_name = $value['last_name'];
                //this will add last name into table if unique
                $GLOBALS['DB']->exec("INSERT IGNORE INTO last_names (last_name) VALUES ('{$last_name}');");
                $last_name_id = $GLOBALS['DB']->query("SELECT id FROM last_names WHERE last_name = '{$last_name}';");

                //insert first and last name IDs into authors_fullnames table
                $GLOBALS['DB']->exec("INSERT IGNORE INTO authors_fullnames (first_name_id, last_name_id) VALUES ({$first_name_id}, {$last_name_id});");

                //get author_id from authors_fullnames
                $author_id = $GLOBALS['DB']->query("SELECT id FROM authors_fullnames WHERE first_name_id = {$first_name_id} AND last_name_id = {$last_name_id};");

                //insert book_description_id and author_id into books_authors table
                $GLOBALS['DB']->exec("INSERT IGNORE INTO books_authors (book_description_id, author_id) VALUES ({$unique_book_id}, {$author_id});");

            }


        }

        static function getAll()
        {
            //this is an array of book objects
            $all_books = array();

            //get the title / summary / category
            $query_book_descriptions = $GLOBALS['DB']->query("SELECT books_descriptions.* FROM books_authors JOIN books_descriptions ON (books_descriptions.id = books_authors.book_description_id);");
            foreach ($query_book_descriptions as $book_record) {
                $book_description_id = $book_record['id'];
                $book_title = $book_record['title'];
                $book_summary = $book_record['summary'];
                $book_category = $book_record['category'];
                //get the authors
                $book_authors_query = $GLOBALS['DB']->query("SELECT (first_names.first_name, last_names.lastname) FROM books_authors JOIN authors_fullnames ON (authors_fullnames.id=books_authors.author_id) JOIN first_names ON (first_names.id=authors_fullnames.first_name_id) JOIN last_names ON (last_names.id=authors_fullnames.last_name_id) WHERE books_authors.book_description_id={$book_description_id};");
                $book_authors = array();
                foreach ($book_authors_query as $author) {
                    $first_name = $author['first_name'];
                    $last_name = $author['last_name'];
                    $full_name = $first_name . " " . $last_name;
                    $name_array = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
                    array_push($book_authors, $name_array);
                }
                $book_id = $GLOBALS['DB']->query("SELECT id FROM books_authors WHERE book_description_id = {$book_description_id};");
                $new_book = new Book($book_title, $book_authors, $book_summary, $book_category, $book_id[0]);
                array($all_books, $new_book);
            }
            return $all_books;


        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM books_authors;");
            $GLOBALS['DB']->exec("DELETE FROM books_descriptions;");
        }
    }



?>
