<?php
    class Book
    {
        private $id;
        private $title;
        private $authors;
        private $summary;
        private $category;

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
            return $this->id;
        }

        function setId($id)
        {
            $this->id = $id;
        }

        function save()
        {
            //insert summary and category into books_descriptions table
            $GLOBALS['DB']->exec("INSERT IGNORE INTO books_descriptions (title, summary, category) VALUES ('{$this->getTitle()}', '{$this->getSummary()}', '{$this->getCategory()}');");

            $query = $GLOBALS['DB']->query("SELECT id FROM books_descriptions WHERE title = '{$this->getTitle()}';");
            $rs = $query->fetchAll(PDO::FETCH_ASSOC);
            $book_id = $rs[0]['id'];


            //Parse out authors
            foreach($this->getAuthors() as $author => $value) {

                $first_name = $value['first_name'];
                //this will add first name into table if unique
                $GLOBALS['DB']->exec("INSERT IGNORE INTO first_names (first_name) VALUES ('{$first_name}');");
                $query = $GLOBALS['DB']->query("SELECT id FROM first_names WHERE first_name = '{$first_name}';");
                $rs = $query->fetchAll(PDO::FETCH_ASSOC);
                $first_name_id = $rs[0]['id'];

                $last_name = $value['last_name'];
                //this will add last name into table if unique
                $GLOBALS['DB']->exec("INSERT IGNORE INTO last_names (last_name) VALUES ('{$last_name}');");
                $query = $GLOBALS['DB']->query("SELECT id FROM last_names WHERE last_name = '{$last_name}';");
                $rs = $query->fetchAll(PDO::FETCH_ASSOC);
                $last_name_id = $rs[0]['id'];

                //insert first and last name IDs into authors_fullnames table
                $GLOBALS['DB']->exec("INSERT IGNORE INTO authors_fullnames (first_name_id, last_name_id) VALUES ({$first_name_id}, {$last_name_id});");

                //get author_id from authors_fullnames
                $query = $GLOBALS['DB']->query("SELECT id FROM authors_fullnames WHERE first_name_id = {$first_name_id} AND last_name_id = {$last_name_id};");
                $rs = $query->fetchAll(PDO::FETCH_ASSOC);
                $author_id = $rs[0]['id'];

                //insert book_description_id and author_id into books_authors table
                $GLOBALS['DB']->exec("INSERT IGNORE INTO books_authors (book_description_id, author_id) VALUES ({$book_id}, {$author_id});");

                //set Book object id
                $query = $GLOBALS['DB']->query("SELECT id FROM books_authors WHERE book_description_id = {$book_id};");
                $rs = $query->fetchAll(PDO::FETCH_ASSOC);

                $this->setId($rs[0]['id']);
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
                $book_authors_query = $GLOBALS['DB']->query("SELECT authors_fullnames.* FROM books_authors JOIN authors_fullnames ON (authors_fullnames.id=books_authors.author_id) WHERE books_authors.book_description_id={$book_description_id};");

                $book_authors = array();
                foreach ($book_authors_query as $author) {
                    $first_name_id = $author['first_name_id'];
                    $last_name_id = $author['last_name_id'];


                    $book_firstname_query = $GLOBALS['DB']->query("SELECT (first_names.first_name) FROM authors_fullnames  JOIN first_names ON (first_names.id=authors_fullnames.first_name_id) WHERE authors_fullnames.first_name_id={$first_name_id};");
                    $rs = $book_firstname_query->fetchAll(PDO::FETCH_ASSOC);
                    $first_name = $rs[0]['first_name'];

                    $book_lastname_query = $GLOBALS['DB']->query("SELECT (last_names.last_name) FROM authors_fullnames JOIN last_names ON (last_names.id=authors_fullnames.last_name_id) WHERE authors_fullnames.last_name_id={$last_name_id};");
                    $rs = $book_lastname_query->fetchAll(PDO::FETCH_ASSOC);
                    $last_name = $rs[0]['last_name'];

                    $full_name = $first_name . " " . $last_name;

                    $book_authors[$full_name] = array('first_name' => $first_name, 'last_name' => $last_name);

                }

                //grab first id for book in books_authors
                $query = $GLOBALS['DB']->query("SELECT id FROM books_authors WHERE book_description_id = {$book_description_id};");
                $rs = $query->fetchAll(PDO::FETCH_ASSOC);
                $book_id = $rs[0]['id'];

                $new_book = new Book($book_title, $book_authors, $book_summary, $book_category, $book_id);
                array_push($all_books, $new_book);
            }
            return $all_books;


        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM books_authors;");
            $GLOBALS['DB']->exec("DELETE FROM books_descriptions;");
            $GLOBALS['DB']->exec("DELETE FROM library_books;");
        }

        static function find($search_id)
        {
            //this is the book we're looking for
            $found_book = null;

            //get the title / summary / category
            $query_book_descriptions = $GLOBALS['DB']->query("SELECT books_descriptions.* FROM books_authors JOIN books_descriptions ON (books_descriptions.id = books_authors.book_description_id) WHERE books_authors.id = {$search_id};");

            foreach ($query_book_descriptions as $book_record) {
                $book_description_id = $book_record['id'];
                $book_title = $book_record['title'];
                $book_summary = $book_record['summary'];
                $book_category = $book_record['category'];
                //get the authors
                $book_authors_query = $GLOBALS['DB']->query("SELECT authors_fullnames.* FROM books_authors JOIN authors_fullnames ON (authors_fullnames.id=books_authors.author_id) WHERE books_authors.book_description_id={$book_description_id};");

                $book_authors = array();
                foreach ($book_authors_query as $author) {
                    $first_name_id = $author['first_name_id'];
                    $last_name_id = $author['last_name_id'];


                    $book_firstname_query = $GLOBALS['DB']->query("SELECT (first_names.first_name) FROM authors_fullnames  JOIN first_names ON (first_names.id=authors_fullnames.first_name_id) WHERE authors_fullnames.first_name_id={$first_name_id};");
                    $rs = $book_firstname_query->fetchAll(PDO::FETCH_ASSOC);
                    $first_name = $rs[0]['first_name'];

                    $book_lastname_query = $GLOBALS['DB']->query("SELECT (last_names.last_name) FROM authors_fullnames JOIN last_names ON (last_names.id=authors_fullnames.last_name_id) WHERE authors_fullnames.last_name_id={$last_name_id};");
                    $rs = $book_lastname_query->fetchAll(PDO::FETCH_ASSOC);
                    $last_name = $rs[0]['last_name'];

                    $full_name = $first_name . " " . $last_name;

                    $book_authors[$full_name] = array('first_name' => $first_name, 'last_name' => $last_name);

                }

                $found_book = new Book($book_title, $book_authors, $book_summary, $book_category, $search_id);
            }
            return $found_book;
        }



    }



?>
