<?php 

namespace Core;

class Router{
	// array de rotas (tabela de rotas)
	protected $routes = [];

	//parâmetros da rota conferida/combinada
	protected $params = [];

	// adiciona uma rota na tabela de rotas
	// $route == URL da rota
	// $params são os parâmetros (controller, action, etc)
	public function add($route, $params = []){
		// converte a rota para uma expressão regular: escapando as barras '/'
		$route = preg_replace('/\//', '\\/', $route);

		// converte as variáveis. ex {controller}
		$route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

		// converte as variáveis com expressões regulares customizadas. ex {id:\d+}
		$route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

		// Adiciona delimitadores para o começo e o final, e a flag de case insensitive /i
		$route = '/^' . $route . '$/i';

		$this->routes[$route] = $params;
	}

	// pega todas as rotas da tabela de rotas
	public function getRoutes(){
		return $this->routes;
	}

	// confere a rota com as rotas da tabela de rotas, setando a propriedade $params se uma rota for encontrada
	public function match($url){

		// Confere com o formato de URL /controller/action
		// $reg_exp = "/^(?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)$/";

		foreach ($this->routes as $route => $params) {
			if (preg_match($route, $url, $matches)){
				// Pega os group values caputadores
				// $params = [];

				foreach ($matches as $key => $match) {
					if (is_string($key)) {
						$params[$key] = $match;
					}
				}

				$this->params = $params;
				return true;
			}
		}

		return false;
	}

	// pega os parâmetros combinados
	public function getParams(){
		return $this->params;
	}

	public function dispatch($url){

		$url = $this->removeQueryStringVariables($url);

		if ($this->match($url)) {
			$controller = $this->params['controller'];
			$controller = $this->convertToStudlyCaps($controller);
			// $controller = "App\Controllers\\$controller";
			$controller = $this->getNamespace() . $controller;

			if (class_exists($controller)) {
				$controller_object = new $controller($this->params);

				$action = $this->params['action'];
				$action = $this->convertToCamelCase($action);

				if (preg_match('/action$/i', $action) == 0) {
				    $controller_object->$action();
				}else{
				    throw new \Exception("Method $action in controller $controller cannot be called directly - remove the Action suffix to call this method");
				}
			}else{
				echo "Controler class $controller not found";
			}
		}else{
			echo 'No route matched.';
		}
	}

	protected function convertToStudlyCaps($string){
		return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
	}

	protected function convertToCamelCase($string){
		return lcfirst($this->convertToStudlyCaps($string));
	}

	protected function removeQueryStringVariables($url){
		if ($url != ''){
			$parts = explode('&', $url, 2);

			if (strpos($parts[0], '=') === false){
				$url = $parts[0];
			}else{
				$url = '';
			}
		}

		return $url;
	}

	protected function getNamespace(){
		$namespace = 'App\Controllers\\';

		if(array_key_exists('namespace', $this->params)){
			$namespace .= $this->params['namespace'] . '\\';
		}

		return $namespace;
	}
}


?>