<?php
/*
	* @packpage: MyProducts
	* Modulo para la gestion de productos del sistema y de la tienda
	* Hereda de MyFunctions para tomar metodos genericos necesarios
	* Autor: Claudio Marrero
	* 24.05.12
*/
class MyProducts extends MyFunctions{
	
	//Constructor de la clase, lo usaremos para ejecutar los hooks, etc.
	/*
		* Autor: Claudio Marrero
		* 24.05.12
	*/
	public function __construct(){
		//Action para agregar el menu de my-products
		add_action('admin_menu',array($this, 'my_menu_set'));

	}//End construct
	
	
	/*
		* Autor: Claudio Marrero
		* 24.05.12
	*/
	/***************************************************
	MENU PRINCIPAL DEL SISTEMA DIVIDO POR ROL DE USUARIO
	******************************************************/
	public function my_menu_set(){
		/*
		* Menu For Productos
		* If user edit pages, is Administrator of the accounts.
		* [0]=>administrator,[0]=>cliente,[0]=>admin,[0]=>operador
		*/
		if($this->my_getRole()=='Admin'){
			$accounts = add_menu_page('mysmart',__('Productos','mysmart'), 'edit_pages','my-products',array($this, 'my_products'));
			$settings = add_submenu_page('my-products',__('Listado','mysmart'),__('Listado','mysmart'),'edit_pages', 'my-products', array($this, 'my_products') );
		}
	}//End menu
	
	/*
		* Productos:
		* Autor: Claudio Marrero
		* 24.05.12
	*/
	//Display this function with click in the menu item
	public function my_products(){
		//Traemos todas los productos de la cuenta existente
		$products = $this->my_getProducts('productId=');
		//Array de tabs para mostrar en pantalla
		$viewOptions['tabs'] = array(
			array('link'=>'admin.php?page=my-products','done'=>$this->my_tabActive('init'),'name'=>__('Productos','mysmart')),
			array('link'=>'admin.php?page=my-products&tab=settings','done'=>$this->my_tabActive('settings'),'name'=>__('Opciones','mysmart'))
			);
		?>
        <!-- Inicio de la clase principal -->
        <div class="wrap">
        	<!-- Mostramos las tabs -->
			<?php $this->my_tabView($viewOptions);?>
            <!-- De acuerdo a la tab activa, mostramos la seccion y el cotenido -->
            <?php if($this->my_tabActive('init')):?>
            	<!-- titulo de la seccion -->
            	<h3><? _e('Lista de productos','mysmart'); ?>
                <!-- Boton para agregar nuevos productos -->
                <a href="#" action="FormAddProduct" form="1" class="add-new-h2 addNewElement"><? _e('Nuevo Producto','mysmart');?></a></h3>
                	<!-- este div en blanco, resuelve las llamadas por ajax -->
                	<div id="responseAjax1" class="addFormElement"></div>
                    <!-- Listado de cuentas -->
                	<table class="widefat">
                    	<thead>
                            <tr>
                                <th><? _e('Code','mysmart');?></th>
                                <th><? _e('Producto','mysmart');?></th>
                                <th><? _e('Descripcion','mysmart');?></th>
                                <th><? _e('En stock','mysmart');?></th>
                                <th><? _e('Cod. Barra','mysmart');?></th>
                                <th><? _e('Nombre','mysmart');?></th>
                                <th><? _e('Nombre','mysmart');?></th>
                                <th>...</th>
                            </tr>
                        </thead>
                        <tbody id="listAccounts">
                <!-- si existen cuentas, realiza el foreach, si no, mostramos que no hay -->
                <?php if($products):?>
                	<?php foreach($products as $p):?>
                    		<tr id="ae<?=$a->productId;?>">
	                            <td><?=$p->productId;?></td>
                            	<td><?=$p->name;?></td>
                                <td><?=$p->description;?></td>
                                <td><?=$p->inStock;?></td>
                                <td><?=$p->barCode;?></td>
                                <td><?=$p->productCode;?></td>
                                <td><?=$p->productCost;?></td>
                                <td><?=$p->productMargin;?></td>
                                <td><?=$p->productPrice;?></td>
                                <td><?=$p->productPromotion;?></td>
                                <td>
                                    <a href="<?=$p->productId;?>" class="editElement" action="FormAddProduct"><? _e('Editar','mysmart');?></a> / 
                                    <a href="<?=$p->productId;?>" class="deleteElement" form="1" action="deleteProduct"><? _e('Eliminar','mysmart');?></a>
                                </td>
                            </tr>
                	<?php endforeach;?>
                    <?php else: ?>
                    	<tr>
                        	<td colspan="8"><? _e('No hay productos creados hasta el momento, desea crear uno?','mysmart');?></td>
                        </tr>
                 <?php endif; ?>
                        </tbody>
                            <tfoot>
                                <tr>
                                    <th><? _e('Nombre','mysmart');?></th>
                                    <th><? _e('Nombre','mysmart');?></th>
                                    <th><? _e('Nombre','mysmart');?></th>
                                    <th><? _e('Nombre','mysmart');?></th>
                                    <th><? _e('Nombre','mysmart');?></th>
                                    <th><? _e('Nombre','mysmart');?></th>
                                    <th><? _e('Nombre','mysmart');?></th>
                                    <th>...</th>
                                </tr>
                            </tfoot>
                        </table>
                 <!-- Terminamos con el listado de productos -->
            <?php endif; ?>
            <!-- Si el modulo es de configuracion -->
            <?php if($this->my_tabActive('settings')):?>
            	<!-- Titulos -->
            	<h3><? _e('Opciones generales','mysmart'); ?></h3>
                
            <?php endif; ?>
        </div>
        <?php
	}//End my_products
	
	/*
		* Get All Products in DB
		* @return = array, @return = false
		* Autor: Claudio Marrero
		* 24.05.12
	*/
	public function my_getProducts($options=array()){
		global $wpdb;
		$default['productId']  = '';
		$settings = $this->Merge($default,$options);
		$join = $filter = '';
		$colums = '
			p.productId,
			p.accountId,
			p.name,
			p.description,
			p.inStock,
			p.barCode,
			p.productCode,
			p.productCost,
			p.productMargin,
			p.productPrice,
			p.productPromotion
		';
		//Consultamos las sessiones por medio del ID de la session
		if(!empty($settings->productId)){
			$filter.= 'AND p.productId = \''.$settings->productId.'\'';
		}
		
		$query = 'SELECT '.$colums.' FROM '.REPOPRODUCTS.' a '.$join.' WHERE 1=1 '.$filter;
		$products = $wpdb->get_results($query);
		
	return $products;
	}//End my_getProducts;

	
	/*
		* Muestra el formulario de carga de productos
		* @return = html
		* Autor: Claudio Marrero
		* 24.05.12
	*/
	public function my_formAddProduct(){
		//Trae los planes de la bd, para completar el combo select del formuiario
		$plans = $this->my_getPlans();
		$a = false;
		//Consultamos si viene el id, para identificar si cargamos los datos o no en el formulario
		if(isset($_POST['elementId'])){
			$productId = $_POST['elementId'];
			$products = $this->my_getProducts('productId='.$productId);
			$p = $products[0];
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
	}//End my_formAddProduct
	
	/*
		* Recibe los datos del formulario, y agrega nuevas cuentas a la bd
		* @return = r.error, r.conent
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_addProduct(){
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
		* Elimina products
		* @return = r.error, r.conent
		* Autor: Claudio Marrero
		* 24.05.12
	*/
	public function my_deleteProduct(){
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

}//End class MyAccounts
?>