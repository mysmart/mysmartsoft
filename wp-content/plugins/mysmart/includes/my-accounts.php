<?php
/*
	* @packpage: MyAccounts
	* Modulo Cuentas o My Accounts, permite crear nuevas cuentas y configurar las ya existentes
	* Agregar nuevos planes y modulos.
	* Hereda de MyFunctions para tomar metodos genericos necesarios
	* Autor: Claudio Marrero
	* 22.05.12
*/
class MyAccounts extends MyFunctions{
	
	//Constructor de la clase, lo usaremos para ejecutar los hooks, etc.
	/*
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function __construct(){
		//Action para agregar el menu de myaccount
		add_action('admin_menu',array($this, 'my_menu_set'));
		//Al pinchar sobre Nueva Cuenta, llama al formulario por ajax
		add_action('wp_ajax_FormAddAccount', array($this, 'my_formAddAccount'));
		//Cuando envia el formulario de nueva cuenta, llama la funcion por ajax
		add_action('wp_ajax_addAccount', array($this, 'my_addAccount'));
		//Editamos las cuentas
		add_action('wp_ajax_editAccount', array($this, 'my_editAccount'));
		//Eliminas las cuentas
		add_action('wp_ajax_deleteAccount', array($this, 'my_deleteAccount'));
		//Llama al formulario de nuevo plan por ajax
		add_action('wp_ajax_FormAddPlan', array($this, 'my_formAddPlan'));
		//Envia el formulario de nuevo plan por ajax
		add_action('wp_ajax_addPlan', array($this, 'my_addPlan'));
		//Eliminas llos planes
		add_action('wp_ajax_deletePlan', array($this, 'my_deletePlan'));
		//Llama al formulario de nuevo modulo por ajax
		add_action('wp_ajax_FormAddModulo', array($this, 'my_formAddModule'));
		//Carga en la base de datos la informacion del formulario del modulo
		add_action('wp_ajax_addModule', array($this, 'my_addModule'));
		//Eliminas llos modulos
		add_action('wp_ajax_deleteModule', array($this, 'my_deleteModule'));
		//Chequea si existe o no el email del cliente en la base de datos por ajax
		add_action('wp_ajax_checkUser', array($this, 'my_checkUser'));
	}//End construct
	
	
	/*
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	/***************************************************
	MENU PRINCIPAL DEL SISTEMA DIVIDO POR ROL DE USUARIO
	******************************************************/
	public function my_menu_set(){
		/*
		* Menu For Accounts
		* If user edit pages, is Administrator of the accounts.
		* Las cuentas dan de alta nuevos servicios a clientes.
		*/
		if($this->my_getRole()=='Administrator'){
			$accounts = add_menu_page('mysmart',__('Cuentas','mysmart'), 'edit_pages','my-accounts',array($this, 'my_accounts'));
			$settings = add_submenu_page('my-accounts',__('Configuracion','mysmart'),__('Configuracion','mysmart'),'edit_pages', 'my-accounts', array($this, 'my_accounts') );
		}
	}//End menu
	
	/*
		* Accounts:
		* GetAccounts, AddAccounts, EditAccounts, DeleteAccounts, Planes y Modulos
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	//Display this function with click in the menu item
	public function my_accounts(){
		//Traemos todas las cuentas que existen la base de datos, sin filtro
		$accounts = $this->my_getAccounts();
		//Array de tabs para mostrar en pantalla
		$viewOptions['tabs'] = array(
			array('link'=>'admin.php?page=my-accounts','done'=>$this->my_tabActive('init'),'name'=>__('Cuentas','mysmart')),
			array('link'=>'admin.php?page=my-accounts&tab=settings','done'=>$this->my_tabActive('settings'),'name'=>__('Configuracion','mysmart'))
			);
		?>
        <!-- Inicio de la clase principal -->
        <div class="wrap">
        	<!-- Mostramos las tabs -->
			<?php $this->my_tabView($viewOptions);?>
            <!-- De acuerdo a la tab activa, mostramos la seccion y el cotenido -->
            <?php if($this->my_tabActive('init')):?>
            	<!-- titulo de la seccion -->
            	<h3><? _e('Lista de cuentas','mysmart'); ?>
                <!-- Boton para agregar nuevas cuentas -->
                <a href="#" action="FormAddAccount" form="1" class="add-new-h2 addNewElement"><? _e('Nueva Cuenta','mysmart');?></a></h3>
                	<!-- este div en blanco, resuelve las llamadas por ajax -->
                	<div id="responseAjax1" class="addFormElement"></div>
                    <?=$this->my_getRole(); ?>
                    <!-- Listado de cuentas -->
                	<table class="widefat">
                    	<thead>
                            <tr>
                                <th><? _e('Codigo','mysmart');?></th>
                                <th><? _e('Cliente','mysmart');?></th>
                                <th><? _e('Email','mysmart');?></th>
                                <th><? _e('Telefono','mysmart');?></th>
                                <th><? _e('Direccion','mysmart');?></th>
                                <th><? _e('Plan','mysmart');?></th>
                                <th><? _e('Usuario','mysmart');?></th>
                                <th>...</th>
                            </tr>
                        </thead>
                        <tbody id="listAccounts">
                <!-- si existen cuentas, realiza el foreach, si no, mostramos que no hay -->
                <?php if($accounts):?>
                	<?php foreach($accounts as $a):?>
                    		<tr id="ae<?=$a->accountId;?>">
                            	<td><?=$a->accountId;?></td>
                                <td><?=$a->name;?></td>
                                <td><?=$a->email;?></td>
                                <td><?=$a->phone;?></td>
                                <td><?=$a->adress;?></td>
                                <td><?=$a->plan;?></td>
                                <td><?=$a->user_login;?></td>
                                <td>
                                    <a href="<?=$a->accountId;?>" class="editElement" action="FormAddAccount"><? _e('Editar','mysmart');?></a> / 
                                    <a href="<?=$a->accountId;?>" class="deleteElement" form="1" action="deleteAccount"><? _e('Eliminar','mysmart');?></a>
                                </td>
                            </tr>
                	<?php endforeach;?>
                    <?php else: ?>
                    	<tr>
                        	<td colspan="8"><? _e('No hay cuentas creadas hasta el momento, desea crear una?','mysmart');?></td>
                        </tr>
                 <?php endif; ?>
                        </tbody>
                            <tfoot>
                                <tr>
                                    <th><? _e('Codigo','mysmart');?></th>
                                    <th><? _e('Cliente','mysmart');?></th>
                                    <th><? _e('Email','mysmart');?></th>
                                    <th><? _e('Telefono','mysmart');?></th>
                                    <th><? _e('Direccion','mysmart');?></th>
                                    <th><? _e('Plan','mysmart');?></th>
                                    <th><? _e('Usuario','mysmart');?></th>
                                    <th>...</th>
                                </tr>
                            </tfoot>
                        </table>
                 <!-- Terminamos con el listado de cuentas -->
            <?php endif; ?>
            <!-- Si el modulo es de configuracion -->
            <?php if($this->my_tabActive('settings')):?>
            <?php 
				//Cargamos los planes y modulos que hay en la base de datos
				$plans = $this->my_getPlans();
				$modules = $this->my_getModules();
			?>
            	<!-- Titulos -->
            	<h3><? _e('Configuracion general de cuentas','mysmart'); ?></h3>
                <h4><? _e('Planes','mysmart'); ?> 
                <a href="#" action="FormAddPlan" form="1" class="add-new-h2 addNewElement"><? _e('Nuevo Plan','mysmart');?></a></h4>
                <!-- este div recibe las llamadas por ajax -->
                <div id="responseAjax1" class="addFormElement"></div>
                <!-- listado de planes -->
                	<table class="widefat">
                    	<thead>
                            <tr>
                                <th><? _e('Plan ID','mysmart');?></th>
                                <th><? _e('Plan','mysmart');?></th>
                                <th><? _e('Description','mysmart');?></th>
                                <th>...</th>
                            </tr>
                        </thead>
                        <tbody id="listPlans">
                  <!-- si hay planes, mostramos los datos -->
                <?php if($plans):?>
                	<?php foreach($plans as $p):?>
                    		<tr id="ae<?=$p->planId;?>">
                            	<td><?=$p->planId;?></td>
                                <td><?=$p->plan;?></td>
                                <td><?=$p->description;?></td>
                                <td><a href="<?=$p->planId;?>" class="editElement" form="1" action="FormAddPlan"><? _e('Editar','mysmart');?></a> /
                                <a href="<?=$p->planId;?>"  class="deleteElement" form="1" action="deletePlan"><? _e('Eliminar','mysmart');?></a></td>
                            </tr>
                	<?php endforeach;?>
                    <?php else: ?>
                    	<tr>
                        	<td colspan="4"><? _e('No hay planes creados hasta el momento, desea crear una?','mysmart');?></td>
                        </tr>
                 <?php endif; ?>
                        </tbody>
                            <tfoot>
                                <tr>
                                    <th><? _e('Plan ID','mysmart');?></th>
                                	<th><? _e('Plan','mysmart');?></th>
                                	<th><? _e('Description','mysmart');?></th>
                                    <th>...</th>
                                </tr>
                            </tfoot>
                        </table>
                  <!-- fin de los planes -->
                  <!-- Comenzamos con los modulos -->
                 <h4><? _e('Modulos disponibles en el sistema','mysmart'); ?>
                 <a href="#" action="FormAddModulo" form="2" class="add-new-h2 addNewElement"><? _e('Nuevo Modulo','mysmart');?></a></h4>
                 <!-- este div recibe las llamadas por ajax del modulo -->
                <div id="responseAjax2" class="addFormElement"></div>
                <!-- tabla para el listado de moudlos -->
                	<table class="widefat">
                    	<thead>
                            <tr>
                                <th><? _e('Module ID','mysmart');?></th>
                                <th><? _e('Modulo','mysmart');?></th>
                                <th><? _e('Descripcion','mysmart');?></th>
                                <th>...</th>
                            </tr>
                        </thead>
                        <tbody id="listModules">
                 <!-- Si hay modulos hacemos el foreach y los mostramos -->
                <?php if($modules):?>
                	<?php foreach($modules as $m):?>
                    		<tr id="ae<?=$m->moduleId;?>">
                            	<td><?=$m->moduleId;?></td>
                                <td><?=$m->module;?></td>
                                <td><?=$m->description;?></td>
                                <td><a href="<?=$m->moduleId;?>" class="editElement" form="2" action="FormAddModulo"><? _e('Editar','mysmart');?></a> / 
                                <a href="<?=$m->moduleId;?>" class="deleteElement" form="2" action="deleteModule"><? _e('Eliminar','mysmart');?></a></td>
                            </tr>
                	<?php endforeach;?>
                    <?php else: ?>
                    	<tr>
                        	<td colspan="4"><? _e('No hay modulos creados hasta el momento, desea crear una?','mysmart');?></td>
                        </tr>
                 <?php endif; ?>
                    </tbody>
                        <tfoot>
                            <tr>
                                <th><? _e('Module ID','mysmart');?></th>
                                <th><? _e('Modulo','mysmart');?></th>
                                <th><? _e('Descripcion','mysmart');?></th>
                                <th>...</th>
                            </tr>
                        </tfoot>
                    </table>
              <!-- Terminamos con modulos -->
            <?php endif; ?>
        </div>
        <?php
	}//End my_accounts
	
	/*
		* Get All Accounts in DB
		* @return = array, @return = false
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_getAccounts($options=array()){
		global $wpdb;
		$default['accountId']  = '';
		$settings = $this->Merge($default,$options);
		$join = $filter = '';
		$colums = '
			a.accountId,
			a.userId,
			a.planId,
			a.name,
			a.email,
			a.phone,
			a.adress,
			p.plan,
			u.user_login
		';
		//Consultamos las sessiones por medio del ID de la session
		if(!empty($settings->accountId)){
			$filter.= 'AND a.accountId = \''.$settings->accountId.'\'';
		}
		
		$join.= ' LEFT JOIN '.MYPLANS.' p ON p.planId = a.planId';
		$join.= ' LEFT JOIN '.MYUSERS.' u ON u.ID = a.userId';
		
		$query = 'SELECT '.$colums.' FROM '.MYACCOUNTS.' a '.$join.' WHERE 1=1 '.$filter;
		$accounts = $wpdb->get_results($query);
		
	return $accounts;
	}//End my_getAccounts;

	/*
		* Get All Plans in DB
		* @return = array, @return = false
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_getPlans($options=array()){
		global $wpdb;
		$default[]  = '';
		$settings = $this->Merge($default,$options);
		$join = $filter = '';
		$colums = '
			p.planId,
			p.plan,
			p.description
		';
		//Consultamos las sessiones por medio del ID de la session
		if(!empty($settings->planId)){
			$filter.= 'AND p.planId = \''.$settings->planId.'\'';
		}
		
		
		$query = 'SELECT '.$colums.' FROM '.MYPLANS.' p '.$join.' WHERE 1=1 '.$filter;
		$plan = $wpdb->get_results($query);
		
	return $plan;
	}//End my_getPlans;

	/*
		* Get All Modules in DB
		* @return = array, @return = false
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_getModules($options=array()){
		global $wpdb;
		$default[]  = '';
		$settings = $this->Merge($default,$options);
		$join = $filter = '';
		$colums = '
			m.moduleId,
			m.module,
			m.description
		';
		//Consultamos las sessiones por medio del ID de la session
		if(!empty($settings->moduleId)){
			$filter.= 'AND a.moduleId = \''.$settings->moduleId.'\'';
		}
			
		$query = 'SELECT '.$colums.' FROM '.MYMODULES.' m '.$join.' WHERE 1=1 '.$filter;
		$modules = $wpdb->get_results($query);
		
	return $modules;
	}//End my_getModules;
	
	/*
		* Muestra el formulario de cuentas
		* @return = html
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_formAddAccount(){
		//Trae los planes de la bd, para completar el combo select del formuiario
		$plans = $this->my_getPlans();
		$a = false;
		//Consultamos si viene el id, para identificar si cargamos los datos o no en el formulario
		if(isset($_POST['elementId'])){
			$accountId = $_POST['elementId'];
			$accounts = $this->my_getAccounts('accountId='.$accountId);
			$a = $accounts[0];
		}
		?>
            <form action="#" id="<?=($a)?'formEditAccount':'formAddAccount';?>" name="<?=($a)?'formEditAccount':'formAddAccount';?>" class="form-element" method="post">
            <?php if($a):?>
            	<input type="hidden" name="accountId" id="accountId" value="<?=($a)?$a->accountId:'';?>"/>
                <input type="hidden" name="equalemail-nonce" id="equalemail-nonce" class="input-text" value="<?=$a->email;?>"  />
            <?php endif; ?>
        	<table class="widefat form-table">
            	<tbody>
                	<tr>
                    	<th><label for="username-nonce"><? _e('Usuario:','mysmart');?></label></th>
                        <td><input type="text" name="username-nonce" id="username-nonce" class="input-text" <?=($a)?'readonly="readonly" value="'.$a->user_login.'"':'';?> /></td>
                    </tr>
                	<tr>
                    	<th><label for="name"><? _e('Plan asignado:','mysmart');?></label></th>
                        <td>
                        	<select name="planId" id="planId">
							<?php foreach($plans as $p):?>
                        		<option value="<?=$p->planId;?>" <?=($a and $a->planId==$p->planId)?'selected="selected"':'';?>><?=$p->plan;?></option>
							<?php endforeach;?>
                            </select>
                        </td>
                    </tr>
                	<tr>
                    	<th><label for="name"><? _e('Nombre completo','mysmart');?></label></th>
                        <td><input type="text" name="name" id="name" class="input-text" <?=($a)?'value="'.$a->name.'"':'';?> /></td>
                    </tr>
                    <tr>
                    	<th><label for="email"><? _e('Email','mysmart');?></label></th>
                        <td><input type="text" name="email" id="email" class="input-text" <?=($a)?'readonly="readonly" value="'.$a->email.'"':'';?> /></td>
                    </tr>
                    <tr>
                    	<th><label for="phone"><? _e('Telefono','mysmart');?></label></th>
                        <td><input type="text" name="phone" id="phone" class="input-text" <?=($a)?'value="'.$a->phone.'"':'';?> /></td>
                    </tr>
                    <tr>
                    	<th><label for="adress"><? _e('Dirección','mysmart');?></label></th>
                        <td><textarea name="adress" id="adress" class="textarea-adress"><?=($a)?$a->adress:'';?></textarea></td>
                    </tr>
                </tbody>
            </table>
            	<p class="submit">
                	<input type="submit" name="sendFormAdd-nonce" id="sendFormAdd-nonce" value="<?=($a)?__('Editar cuenta','mysmart'):__('Crear nueva cuenta','mysmart');?>" class="button-primary" />
                    <input type="reset" name="cancelForm-nonce" id="cancelForm-nonce" from="1" value="<? _e('Cancelar','mysmart');?>" class="button" />
                </p>
                <?php if($a):?>
            		<input type="hidden" name="action" value="editAccount" />
                <?php else:?>
	                <input type="hidden" name="action" value="addAccount" />
            	<?php endif; ?>
            </form>
        <?php
		die();
	}//End my_formAddAccount
	
	/*
		* Recibe los datos del formulario, y agrega nuevas cuentas a la bd
		* @return = r.error, r.conent
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_addAccount(){
		global $wpdb;
		//Si se agrega correctamente el usuario, devuelve el ID, si no, devuelve false
		$_POST['userId'] = $this->my_addUser();
		//Si es un ID, pasa el if como true
		if($_POST['userId']){
			//Organizamos el array para insertarlo en la bd
			$data = $this->my_finalData($_POST);
			//Cargamos los datos a la bd
			$retorno = $wpdb->insert(MYACCOUNTS,$data);
			//Si se cargo correctamente, mostramos que cargo
			if($retorno){
				//El ultimo id insertado en mysql es este
				$accountId = mysql_insert_id();
				//Consultamos las cuentas en base al ID insertado
				$accounts = $this->my_getAccounts('accountId='.$accountId);
				//$a tiene los datos de la cuenta cargada
				$a = $accounts[0];
				//response devuelve un error 0 y el contenido a mostrar
				$response = array('error'=>0,'content'=>'<tr>
									<td>'.$a->accountId.'</td>
									<td>'.$a->name.'</td>
									<td>'.$a->email.'</td>
									<td>'.$a->phone.'</td>
									<td>'.$a->adress.'</td>
									<td>'.$a->plan.'</td>
									<td>'.$a->user_login.'</td>
								</tr>');
			}else{
				//Si el retorno dio false, devolvemos un error 1, y un texto de por que
				$response = array('error'=>1,'content'=>__('No se pudo cargar la cuenta correctamente','mysmart'));
			}//End retorno
			
		}else{
			//Si el ID del usuario dio false, devolvemos el error
			$response = array('error'=>0,'content'=>__('El usuario no fue creado','mysmart'));
		}//End post userId;
		//En cualquier caso, damos un echo codificado con json para que lo tome wordpress pro ajax
		echo json_encode($response);
		//Es importante el die(); ya que dwe otro modo, wordpress no tomara la funcion como parte de la API de ajax
		die();
	}//End my_addAccount
	
	/*
		* Recibe los datos del formulario, y agrega nuevas cuentas a la bd
		* @return = r.error, r.conent
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_editAccount(){
		global $wpdb;
		//Si es un ID, pasa el if como true
		if(isset($_POST['accountId'])){
			//Organizamos el array para actualizar en la bd en la bd
			$data = $this->my_finalData($_POST);
			//Actualizamos los datos a la bd
			$retorno = $wpdb->update(MYACCOUNTS,$data,array('accountId'=>$_POST['accountId']));
			//Si se actualizo correctamente, mostramos que cargo
			if($retorno){
				//Consultamos las cuentas en base al ID actualizado
				$accounts = $this->my_getAccounts('accountId='.$_POST['accountId']);
				//$a tiene los datos de la cuenta cargada
				$a = $accounts[0];
				//response devuelve un error 0 y el contenido a mostrar
				$response = array('error'=>0,'content'=>'
									<td>'.$a->accountId.'</td>
									<td>'.$a->name.'</td>
									<td>'.$a->email.'</td>
									<td>'.$a->phone.'</td>
									<td>'.$a->adress.'</td>
									<td>'.$a->plan.'</td>
									<td>'.$a->user_login.'</td>
									<td>'.__('Refresque para editar','mysmart').'</td>');
			}else{
				//Si el retorno dio false, devolvemos un error 1, y un texto de por que
				$response = array('error'=>1,'content'=>__('No se pudo cargar la cuenta correctamente','mysmart'));
			}//End retorno
			
		}else{
			//Si el ID del usuario dio false, devolvemos el error
			$response = array('error'=>0,'content'=>__('El usuario no fue creado','mysmart'));
		}//End post userId;
		//En cualquier caso, damos un echo codificado con json para que lo tome wordpress pro ajax
		echo json_encode($response);
		//Es importante el die(); ya que dwe otro modo, wordpress no tomara la funcion como parte de la API de ajax
		die();
	}//End my_addAccount
	
	/*
		* Elimina cuentas
		* @return = r.error, r.conent
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_deleteAccount(){
		global $wpdb;
		//Si viene accountID por post
		if(isset($_POST['elementId'])){
			//Eliminamos
			$retorno = $wpdb->get_results('DELETE FROM '.MYACCOUNTS.' WHERE accountId = '.$_POST['elementId']);
			//Si el ID del usuario dio false, devolvemos el error
			$response = array('error'=>0,'content'=>__('Se elimino la cuenta','mysmart'));
		}else{
			//Si el ID del usuario dio false, devolvemos el error
			$response = array('error'=>1,'content'=>__('El usuario no fue creado','mysmart'));
		}//End post userId;
		//En cualquier caso, damos un echo codificado con json para que lo tome wordpress pro ajax
		echo json_encode($response);
		//Es importante el die(); ya que dwe otro modo, wordpress no tomara la funcion como parte de la API de ajax
		die();
	}//End my_deleteAccount
	
	/*
		* Chequea los datos que se cargan para un nuevo usuario, y de ser todo correcto lo agrega a la bd, si no devuelve false
		* @return = userID, o False
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_addUser(){
	/* Check if users can register. */
		$registration = get_option( 'users_can_register' );
		$new_user = true;
		/* If user registered, input info. */
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'addAccount' ) {
			$user_pass = wp_generate_password();
			$userdata = array(
				'user_pass' => $user_pass,
				'user_login' => esc_attr( $_POST['username-nonce'] ),
				'first_name' => esc_attr( $_POST['name'] ),
				'last_name' => esc_attr( ''),
				'nickname' => esc_attr( $_POST['name'] ),
				'user_email' => esc_attr( $_POST['email'] ),
				'user_url' => esc_attr(''),
				'aim' => esc_attr(''),
				'yim' => esc_attr(''),
				'jabber' => esc_attr(''),
				'description' => esc_attr(''),
				'role' => 'admin',
			);
			
			if ( !$_POST['username-nonce']){
				return false;
			}elseif ( username_exists($_POST['username-nonce']) ){
				return false;
			}elseif ( !is_email($_POST['email'], true) ){
				return false;
			}elseif ( email_exists($_POST['email']) ){
				return false;
			}else{
				$new_user = wp_insert_user( $userdata );
				wp_new_user_notification($new_user, $user_pass);
				$user = get_userdatabylogin($_POST['username-nonce']);
				return (int)$user->ID;
			}
		}else{
			$new_user = false;
		}
	return false;
	}//End my_addUser
	
	/*
		* Muestra el formulario para cargar nuevos planes
		* @return = html
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_formAddPlan(){	
		?>
            <form action="#" id="formAddPlan" name="formAddPlan" class="form-element" method="post">
        	<table class="widefat form-table">
            	<tbody>
                	<tr>
                    	<th><label for="plan"><? _e('Nombre del Plan','mysmart');?></label></th>
                        <td><input type="text" name="plan" id="plan" class="input-text" /></td>
                    </tr>
                    <tr>
                    	<th><label for="description"><? _e('Descripcion','mysmart');?></label></th>
                        <td><textarea name="description" id="description" class="textarea-adress"></textarea></td>
                    </tr>
                </tbody>
            </table>
            	<p class="submit">
                	<input type="submit" name="sendFormAdd" id="sendFormAdd" value="<? _e('Crear nuevo plan','mysmart');?>" class="button-primary" />
                    <input type="reset" name="cancelForm" id="cancelForm" from="1" value="<? _e('Cancelar','mysmart');?>" class="button" />
                </p>
                <input type="hidden" name="action" value="addPlan" />
            </form>
        <?php
		die();
	}//End my_formAddPlan
	
	/*
		* Toma los datos del formulario de planes y los carga a la base de datos
		* @return = json
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_addPlan(){
		global $wpdb;
		//Todo lo que entra por post, lo convierte para cargarlo a la bd
		$data = $this->my_finalData($_POST);
		//Inserta los datos en mysql
		$retorno = $wpdb->insert(MYPLANS,$data);
		//Si todo va bien, devuelve los datos.
		if($retorno){
			$response = array('error'=>0,'content'=>'<tr><td></td><td>'.$_POST['plan'].'</td><td>'.$_POST['description'].'</td></tr>');
		}else{
			//Si algo salio mal, indica que algo salio mal
			$response = array('error'=>1,'content'=>__('No se pudo cargar correctamente','mysmart'));
		}//End retorno
		//En todos los casos devolvemos un json
		echo json_encode($response);
		die();
	}//End my_addPlan

	/*
		* Elimina Planes
		* @return = r.error, r.conent
		* Autor: Claudio Marrero
		* 24.05.12
	*/
	public function my_deletePlan(){
		global $wpdb;
		//Si viene planId por post
		if(isset($_POST['elementId'])){
			//Eliminamos
			$retorno = $wpdb->get_results('DELETE FROM '.MYPLANS.' WHERE planId = '.$_POST['elementId']);
			$response = array('error'=>0,'content'=>__('Se elimino el plan','mysmart'));
		}else{
			$response = array('error'=>1,'content'=>__('El plan no fue eliminado','mysmart'));
		}//End post elementId;
		//En cualquier caso, damos un echo codificado con json para que lo tome wordpress pro ajax
		echo json_encode($response);
		//Es importante el die(); ya que dwe otro modo, wordpress no tomara la funcion como parte de la API de ajax
		die();
	}//End my_deleteAccount
	
	/*
		* Muestra el formulario para cargar nuevos Modulos
		* @return = html
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_formAddModule(){	
		?>
            <form action="#" id="formAddModule" name="formAddModule" class="form-element" method="post">
        	<table class="widefat form-table">
            	<tbody>
                	<tr>
                    	<th><label for="module"><? _e('Nombre del Modulo','mysmart');?></label></th>
                        <td><input type="text" name="module" id="module" class="input-text" /></td>
                    </tr>
                    <tr>
                    	<th><label for="description"><? _e('Descripcion','mysmart');?></label></th>
                        <td><textarea name="description" id="description" class="textarea-adress"></textarea></td>
                    </tr>
                </tbody>
            </table>
            	<p class="submit">
                	<input type="submit" name="sendFormAdd" id="sendFormAdd" value="<? _e('Crear nuevo modulo','mysmart');?>" class="button-primary" />
                    <input type="reset" name="cancelForm" id="cancelForm" from="2" value="<? _e('Cancelar','mysmart');?>" class="button" />
                </p>
                <input type="hidden" name="action" value="addModule" />
            </form>
        <?php
		die();
	}//my_formAddPlan
	
	/*
		* Toma los datos del formulario de modulos y los carga a la base de datos
		* @return = json
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_addModule(){
		global $wpdb;
		//Toma los datos, y prepara el array para insertarlo en la bd
		$data = $this->my_finalData($_POST);
		//Inserta los datos en la bd
		$retorno = $wpdb->insert(MYMODULES,$data);
		//Si todo va bien, carga y mustra los datos
		if($retorno){
			$response = array('error'=>0,'content'=>'<tr><td></td><td>'.$_POST['module'].'</td><td>'.$_POST['description'].'</td></tr>');
		}else{
			//Si algo salio mal, devuelve error
			$response = array('error'=>1,'content'=>__('No se pudo cargar correctamente','mysmart'));
		}//End retorno
		//en todos los casos devolvemos un json
		echo json_encode($response);
		die();
	}//End my_addModule
	
	/*
		* Elimina Module
		* @return = r.error, r.conent
		* Autor: Claudio Marrero
		* 24.05.12
	*/
	public function my_deleteModule(){
		global $wpdb;
		//Si viene moduleId por post
		if(isset($_POST['elementId'])){
			//Eliminamos
			$retorno = $wpdb->get_results('DELETE FROM '.MYMODULES.' WHERE moduleId = '.$_POST['elementId']);
			$response = array('error'=>0,'content'=>__('Se elimino el modulo correctamente','mysmart'));
		}else{
			$response = array('error'=>1,'content'=>__('El modulo no fue eliminado','mysmart'));
		}//End post elementId;
		//En cualquier caso, damos un echo codificado con json para que lo tome wordpress pro ajax
		echo json_encode($response);
		//Es importante el die(); ya que dwe otro modo, wordpress no tomara la funcion como parte de la API de ajax
		die();
	}//End my_deleteAccount
}//End class MyAccounts
?>