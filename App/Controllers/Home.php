<?php 

namespace App\Controllers;

class Home extends \Core\Controller {

	protected function before(){
		echo "(before) ";
		return false;
	}

	protected function after(){
		echo " (after)";
	}

	public function indexAction(){
		echo 'Hello from the index action in the Home controller!';
	}
}

?>