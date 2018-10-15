<?php 
namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class Category extends Model{

	

public static function listAll(){
	
	$sql = new Sql();
	return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");

	
}

public function save(){
	$sql = new Sql();

	$result = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", 
		array(
			":idcategory"=>$this->getidcategory(),
			":descategory"=>$this->getdescategory()
		));

	 if(isset($result[0])) 
	 	{
	 		$this->setData($result[0]);
	 	}

}


public function get($idcategory)
{

	$sql = new Sql();

	$result = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", array(
			"idcategory"=>$idcategory
		));
	$this->setData($result[0]);

}

public function update()
{

	$sql = new Sql();

	$result = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", 
		array(
			"iduser"=>$this->getiduser(),
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

	 if(isset($result[0])) 
	 	{
	 		$this->setData($result[0]);
	 	}

}

public function delete()
{

	$sql = new Sql();

	$sql->query("DELETE FROM tb_categories WHERE idcategory= :idcategory", [
		":idcategory"=>$this->getidcategory()
	]);

}

public static function getForgot($email)
{

	$sql = new Sql();

	$result = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email", array(
			":email"=>$email
		));
		

	 if(count($result) === 0){
		throw new \Exception("Não foi possível recuperar a senha");
		
	}else{
		$data = $result[0];

		$result2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
			"iduser"=>$data["iduser"],
			"desip"=>$_SERVER["REMOTE_ADDR"]
		));

		if(count($result2) === 0){
			throw new \Exception("Não foi possível recuperar a senha");
			
		}else{
			$dataRecovery = $result2[0];

			$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

			$link = "http://www.rafacommerce.com.br/admin/forgot/reset?code=$code";

		$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir senha Executiva", "forgot", array(
				"name"=>$data["desperson"],
				"link"=>$link
			));

			$mailer->send();

			return $data; 
		}
	} 
}

}

?>