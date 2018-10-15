<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

$app = new Slim();

$app->config("debug", true);

//----------Rota padrão do site!

$app->get("/", function() {
	
	$page = new Page();

	$page->setTpl("index");
});

// -------Rota para o Administrativo-------

$app->get("/admin", function() {

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");
});

// ---------Rota para a página de LOGIN --------

$app->get("/admin/login", function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");
});

// ------- Rota POST para Login do usuário na área Administrativa-----

$app->post("/admin/login", function() {
	
	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;
});

// --------Rota para o LOGOUT da SESSION do sistema ------

$app->get("/admin/logout", function() {
	
	User::logout();
	
	header("Location: /admin/login");
	exit;
});


// --------Rota para listar os usuários no sistema -----

$app->get("/admin/users", function(){
	
	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();
	$page->setTpl("users", array("users"=>$users));
});



// ------Rota para mostrar o layout de inserção de usuário no sistema -----

$app->get("/admin/users/create", function(){
	User::verifyLogin();
	
	$page = new PageAdmin();
	$page->setTpl("/users-create");
});

// ---- Rota POST para INSERIR um usuário no sistema-------

$app->post("/admin/users/create", function(){
	User::verifyLogin();
	
	$users = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$users->setData($_POST);
	
	$users->save();

	header("Location: /admin/users");
	exit;
	
});

// Rota para deletar um Usuário do sistema, RECEBE o ID do Usuário-----

$app->get("/admin/users/:iduser/delete", function($iduser){
	User::verifyLogin();
	
	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;


});




// ------ Rota para mostrar o Layout de UPDATE de usuário no sistema, RECEBE o ID do Usuário------


$app->get("/admin/users/:iduser", function($iduser){
	
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();
	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));
});


$app->post("/admin/users/:iduser", function($iduser){
	
	User::verifyLogin();
	
	$user = new User();
	
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);
	
	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

});

// rota para recuperar a senha

$app->get("/admin/forgot", function()
{
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");
});



$app->post("/admin/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;
});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset");

});

$app->get("/admin/categories", function(){

	User::verifyLogin();

	$categories = Category::listAll();



	$page = new PageAdmin([
		"header"=>true,
		"footer"=>true
	]);

	$page->setTpl("categories", [
		"categories"=>$categories
	]);



});

$app->get("/admin/categories/create", function(){

	User::verifyLogin();

	$categories = Category::listAll();

	

	$page = new PageAdmin([
		"header"=>true,
		"footer"=>true
	]);

	$page->setTpl("categories-create");



});

$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$categories = new Category();
	$categories->setData($_POST);

	$categories->save();

	header("Location: /admin/categories");
	exit;
});


$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	User::verifyLogin();

	$categories = new Category();
	$categories->get((int)$idcategory);

	$categories->delete();

	header("Location: /admin/categories");
	exit;
});

$app->get("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();
	$category->get((int)$idcategory);


	$page = new PageAdmin([
		"header"=>true,
		"footer"=>true
	]);

	$page->setTpl("categories-update", [
		'category'=>$category->getValues()
	]);
});

$app->post("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();
	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();
	
	header("Location: /admin/categories");
	exit;

});

$app->get("/category/:idcategory", function($idcategory){


	$category = new Category();
	$category->get((int)$idcategory);

	$page = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>[]
	]);
});



$app->run();

 ?>