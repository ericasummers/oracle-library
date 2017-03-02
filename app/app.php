<?php
    date_default_timezone_set('America/Los_Angeles');
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Book.php";
    require_once __DIR__."/../src/Library.php";
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
        $name = "Portland Library";
        $new_library = new Library($name);
        $new_library->save();
        $name = "Beaverton Library";
        $new_library2 = new Library($name);
        $new_library2->save();

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

        return $app['twig']->render('index.html.twig', array('libraries'=>Library::getAll(), 'books'=>Book::getAll()));
    });

    return $app;
?>
