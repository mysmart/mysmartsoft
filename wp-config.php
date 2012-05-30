<?php
/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'mysmart');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'root');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', '');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '4Cr?B(*!M,dNoxcK,:Hn8R%X6>H`ljPHj03BX5EKO `VcKH87l6.gx7H<Sr.O_z='); // Cambia esto por tu frase aleatoria.
define('SECURE_AUTH_KEY', 'gp/)LzovLYhI1!2jt*x^(IEELmpq2I{`t_.dUi}|^rb921EIFKmM]?%G{&}dQvz9'); // Cambia esto por tu frase aleatoria.
define('LOGGED_IN_KEY', 'sVPd]..EMpIw7a*^1<BD!Hwc7$UqK|bGy9*=LGot)/z-7k+Qe3wC^H+~0V4~*m@W'); // Cambia esto por tu frase aleatoria.
define('NONCE_KEY', 'J}8Bc=k1U/`PYB;8,$5:XRE%L` #pQ@y`W?&JJld4()vjvKX|-LXej5~)LQ3jJcT'); // Cambia esto por tu frase aleatoria.
define('AUTH_SALT', 'IGr!ViIXvXfMJ&.PQ-Pj^a#/.g =LJCH@3E4]?_-5~aW)R1(EI:dFgiHqD9>,,=_'); // Cambia esto por tu frase aleatoria.
define('SECURE_AUTH_SALT', 'Tg &vz&!g5}Gq)TQ34W>s$DU;^:6sv%PoQs,obvuJmzf7.8)c~%;}E_%6s<#3/v.'); // Cambia esto por tu frase aleatoria.
define('LOGGED_IN_SALT', 'e[WCwZH)meW<^d0RNr99 tq5$ou|Ae{.y>bXj]MPy2GUzQ0udeMET{>oJ:&E3(p;'); // Cambia esto por tu frase aleatoria.
define('NONCE_SALT', '<c!E$$9.)]o;07e1eZZUAlhtLH/(M4|psS+S(CP]V>11-J3PM{y,5<W(6zw%v}Z3'); // Cambia esto por tu frase aleatoria.

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';

/**
 * Idioma de WordPress.
 *
 * Cambia lo siguiente para tener WordPress en tu idioma. El correspondiente archivo MO
 * del lenguaje elegido debe encontrarse en wp-content/languages.
 * Por ejemplo, instala ca_ES.mo copiándolo a wp-content/languages y define WPLANG como 'ca_ES'
 * para traducir WordPress al catalán.
 */
define('WPLANG', 'es_ES');

/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

