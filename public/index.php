<?php 

/**
* Front controller
*/

/**
* TWIG
**/
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
* Autoloader
*/
spl_autoload_register(function ($class) {
	$root = dirname(__DIR__); // get the parent directory
	$file = $root . '/' . str_replace('\\', '/', $class) . '.php';
	if (is_readable($file)) {
		require $root . '/' . str_replace('\\', '/', $class) . '.php';
	}
});


/**
* Routing
*/
require '../Core/Router.php';

$router = new Core\Router();

// adiciona as rotas
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

// mostra a tabela de rotas
// echo '<pre>';
// var_dump($router->getRoutes());
// echo '</pre>';

// Confere a rota requisitada
// $url = $_SERVER['QUERY_STRING'];

// if ($router->match($url)){
// 	echo '<pre>';
// 	var_dump($router->getParams());
// 	echo '</pre>';
// }else{
// 	echo "Nenhuma rota encontrada para a URL '$url'";
// }

$router->dispatch($_SERVER['QUERY_STRING']);

?>