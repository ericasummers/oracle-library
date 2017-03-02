<?php
    date_default_timezone_set('America/Los_Angeles');
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Book.php";
    require_once __DIR__."/../src/Library.php";
    require_once __DIR__."/../src/Patron.php";

    $app = new Silex\Application();
    $server = 'mysql:host=localhost:8889;dbname=oracle';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);
    $app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../views'));
    $app['debug'] = true;
    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig', array('libraries'=>Library::getAll()));
    });

    $app->post("/view_libraries", function() use ($app){
        $library_id = $_POST['libraries_list'];
        $new_library = Library::find($library_id);

        return $app['twig']->render('library.html.twig', array('patrons'=>$new_library->getLibraryPatrons(), 'books'=>$new_library->getLibraryBooks(), 'library' => $new_library, 'libraries'=>Library::getAll()));
    });

    $app->post("/add_library", function() use ($app){
        $library_name = $_POST['name'];
        $new_library = new Library($library_name);
        $new_library->save();

        return $app['twig']->render('index.html.twig', array('libraries'=>Library::getAll()));
    });

    $app->get("/libraries/{id}", function($id) use ($app){
        $new_library = Library::find($id);

        return $app['twig']->render('library.html.twig', array('patrons'=>$new_library->getLibraryPatrons(), 'books'=>$new_library->getLibraryBooks(), 'library' => $new_library, 'libraries'=>Library::getAll()));
    });

    $app->post("/add_patron", function() use ($app){
        $patron_first_name = $_POST['first_name'];
        $patron_last_name = $_POST['last_name'];
        $new_patron = new Patron($patron_first_name, $patron_last_name);
        $new_patron->save();

        $new_library = Library::find($_POST['library_id']);
        $new_library->addPatron($new_patron);

        return $app['twig']->render('library.html.twig', array('library'=>$new_library, 'books'=>$new_library->getLibraryBooks(), 'patrons'=>$new_library->getLibraryPatrons(), 'libraries'=>Library::getAll()));
    });

    $app->post("/add_book", function() use ($app){
        $title = $_POST['title'];
        $first_name = $_POST['author_first_name'];
        $last_name = $_POST['author_last_name'];
        $full_name = $first_name . " " . $last_name;
        $authors = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
        $summary = $_POST['summary'];
        $category = $_POST['categories'];

        $new_book = new Book($title, $authors, $summary, $category);
        $new_book->save();

        $new_library = Library::find($_POST['library_id']);
        $new_library->addBook($new_book);

        return $app['twig']->render('library.html.twig', array('library'=>$new_library, 'books'=>$new_library->getLibraryBooks(), 'patrons'=>$new_library->getLibraryPatrons(), 'libraries'=>Library::getAll()));
    });









    return $app;
?>
