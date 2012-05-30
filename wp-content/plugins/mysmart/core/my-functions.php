<?php
/*
	* @Packpage: MyFunctions
	* Include in /index.php
	* Contiene todas las funciones abstractas que se usen en diferentes lugares
	* del sistema
	* Autor: Claudio Marrero
	* 22.05.12
*/
class MyFunctions {
	//Constructor de la clase, lo usaremos para ejecutar los hooks, etc.
	/*
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function __construct(){
		//Load modulo for check user if exist in the sistem,
		/*
		* Autor: Claudio Marrero
		* 22.05.12
		*/
		add_action('wp_ajax_checkUser', array($this, 'my_checkUser'));
	}//End construct
	
	//Merge settings, toma un array de datos, y lo remplaza por otro 
	//que tenga la misma key, devuelve un objeto o un array()
	/*
		* @return: objet or array()
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function Merge($default, $options, $array=FALSE) {
		  if (is_array($options)) {
			  $settings = array_merge($default, $options);
		  } else {
			  parse_str($options, $output);
			  $settings = array_merge($default, $output);
		  }
  
		  return ($array) ? $settings : (Object) $settings;
	}//End merge settings
	
	/*
		* 
		* Returns the translated role of the current user. If that user has 
		* no role for the current blog, it returns false. 
		* 
		* @return string The name of the current role 
		* Autor: Claudio Marrero
		* 22.05.12
	*/ 
	public function my_getRole(){ 
		global $wp_roles; 
		$current_user = wp_get_current_user(); 
		$roles = $current_user->roles;
		$role = array_shift($roles); 
		return isset($wp_roles->role_names[$role])?$wp_roles->role_names[$role]:false; 
	} //End my_getRole
	
	/*
		* View for Tabs
		* @return html for tabs 
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	
	public function my_tabView($options=array()){
		$tabs = $options['tabs'];
	?>
        <h2 class="nav-tab-wrapper">
            <?php foreach($tabs as $k => $t):?>
            <a id="tab<?=$k;?>" href="<?=$t['link'];?>" class="nav-tab <?=($t['done'])?'nav-tab-active':'';?>"><?=$t['name'];?></a>
            <?php endforeach; ?>
        </h2>
    <?php
	}//End my_tabView
	
	/*
		* Identifica cual es el tab activo, si se le pasa INIT toma por defecto el primero.
		* @return html for tabs 
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_tabActive($tab){
		if(isset($_GET['tab'])){
			$getTab = $_GET['tab'];
			if($getTab==$tab){
				return true;
			}else{
				return false;
			}
		}else{
			if($tab=='init'){
				return true;
			}else{
				return false;
			}
		}
	}//End my_tabActive
	
	/*
		* Toma un email y devuelve true si no existe, si existe devuelve un json con el error
		* @return true, json 
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_checkUser(){
		$email = $_POST['email'];
		$equalemail = true;
		if(isset($_POST['equalemail'])){
			$equalemail = $_POST['equalemail'];
			if($email == $equalemail){
				$equalemail = false;
			}else{
				$equalemail = true;
			}
		}
		if(email_exists($email) and $equalemail){
			echo json_encode(__('Ya existe en nuestra base este email ','mysmart'));
		}else{
			echo 'true';
		}
	die();
	}//end my_checkUser;
	
	/*
		* Recorre todo lo que entra por el argumento y crea un array sin los elementos -nonce y action, para luego
		* retornar un array formado de tal manera que permite utilizarlo para insertar en la base de datos.
		* @return array()
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_finalData($post){
		$data = array();
		foreach($post as $key => $value){
			if($key != 'action' and !strpos($key, '-nonce')){
				$data[$key] = $value; 
			}
		}
		return $data;
	}//End my_finalData
	
}//MyGeneralFunctions
?>