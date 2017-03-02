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
        return $app['twig']->render('index.html.twig', array('libraries'=>Library::getAll(), 'books'=>Book::getAll()));
    });

    $app->get("/add_book", function() use ($app){
        return $app['twig']->render('add_book.html.twig', array('libraries'=>Library::getAll(), 'books'=>Book::getAll()));
    });

    $app->get("/add_patron", function() use ($app){
        return $app['twig']->render('add_patron_html.twig', array('libraries'=>Library::getAll(), 'books'=>Book::getAll()));
    });

    $app->get("/add_library", function() use ($app){
        return $app['twig']->render('add_library.html.twig', array('libraries'=>Library::getAll(), 'books'=>Book::getAll()));
    });

    $app->get("/view_libraries", function() use ($app){

    });

    $app->post("/add_library", function() use ($app){

    });

    $app->get("/libraries/{id}", function($id) use ($app){

    });

    $app->get("/books/{id}", function($id) use ($app){

    });







    return $app;
?>
