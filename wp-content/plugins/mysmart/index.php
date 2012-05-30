<?php
/*
Plugin Name: My Smart Soft
Plugin URI: http://www.mysmart.com.ar
Description: Chat para soporte al cliente, multi session.
Version: 2.0
Author: Claudio Adrian Marrero, Gabriel Sevilla, Gaston Guidolin
Author URI: http://www.marreroclaudio.com.ar
License: GPL2
*/
/*  Copyright 2012  Claudio Adrian Marrero, Gabriel Sevilla, Gaston Guidolin  (email : info@mysmart.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//Constantes para el manejo de las direcciones de archivos y URLs
define('my_url', plugins_url().'/mysmart');
define('my_path',ABSPATH.'/wp-content/plugins/mysmart');
define('my_version','2.0');
/*
	* General Function
	* Class MyFunctions
	* Url: core/
	* Autor: Claudio Marrero
	* 22.05.12
*/
include(my_path.'/core/my-functions.php');
/*
	* List Tables
	* define constant names tables
	* Url: core/
	* Autor: Claudio Marrero
	* 22.05.12
*/
include(my_path.'/core/my-tables.php');
/*
	* Functions of My Accounts Settings
	* Class MyAccounts
	* Url: includes/
	* Autor: Claudio Marrero
	* 22.05.12
*/
include(my_path.'/includes/my-accounts.php');
/*
	* Functions of My products
	* Class MyProducts
	* Url: includes/
	* Autor: Claudio Marrero
	* 24.05.12
*/
include(my_path.'/includes/my-products.php');
/*
	* @Packpage: MySmartIni
	* Instance of below this document.
	* Init sistem in worpdress
	* Hereda de MyFunctions para tomar todas las funciones genericas
	* Autor: Claudio Marrero
	* 22.05.12
*/
class MySmartInit extends MyFunctions{

	//Constructor de la clase, lo usaremos para ejecutar los hooks, etc.
	/*
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function __construct(){
		//Agregamos los estilos al admin_head
		add_action('admin_head',array($this, 'my_jsAndCss'));			
		//Ejecuta la funcion que carga el idioma correspondiente segun la instalacion de wordpress.
		add_action('init', array($this, 'my_load_textdomain'));
		//Action para agregar el menu//Limpia el menu general de wordpress, cada modulo tiene esto en su constructor
		add_action('admin_menu',array($this, 'my_menu_set'));
		/*MODULO MY ACCOUNTS, lo inicializamos dentro del constructor para mostrar sus datos*/
		new MyAccounts;
		new MyProducts;
	}//End construct
	
	/*****************************************************************************************************************
	BACKEND - GESTION
	********************************************************************************************************************/	
	//ubicamos el archivo de lenguaje.
	/*
		* Autor: Claudio Marrero
		* 22.05.12
	*/
    public function my_load_textdomain()
    {
        load_plugin_textdomain('mysmart', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
    }//End load textdomain
	
	//Function para cargar los css y los javascript al admin de wordpress
	/*
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_jsAndCss(){
	?>
    <!-- This add to admin_head -->
        <link href="<?=my_url;?>/media/css/styles.css" rel="stylesheet" type="text/css" />
        <link href="<?=my_url;?>/media/js/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?=my_url;?>/media/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <script type="text/javascript" src="<?=my_url;?>/media/js/functions.js"></script>
        <script type="text/javascript" src="<?=my_url;?>/media/js/jquery.validate.min.js"></script>
    <!-- End styles and admin_head -->
    <?php
	}//End my_jsAndCss
	
	/***************************************************
	MENU PRINCIPAL DEL SISTEMA DIVIDO POR ROL DE USUARIO
	******************************************************/
	/*
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	public function my_menu_set(){
		global $menu;
		/*
		* If user is Administrator, do not clean menu wordpress
		* [0]=>administrator,[0]=>cliente,[0]=>admin,[0]=>operador
		*/
		if($this->my_getRole()!='Administrator'){
			//List of items
			$restricted = array( 
				__('Dashboard'),
				__('Posts'),
				__('Posts'),
				__('Media'),
				__('Links'),
				__('Pages'),
				__('Appearance'),
				__('Tools'),
				__('Users'),
				__('Settings'),
				__('Comments'),
				__('Plugins'),
				__('Profile')
				);
			end ($menu);
			//Walk items
			while (prev($menu)){
				$value = explode(' ',$menu[key($menu)][0]);
				if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
			}
		}//End if Role
	}//End menu
}//End init class
//Init plugin my smart
new MySmartInit;
?>