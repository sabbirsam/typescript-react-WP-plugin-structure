<?php
/**
 * Plugin Name: Simple Form
 *
 * @author            Sabbir Sam, devsabbirahmed
 * @copyright         2022- Sabbir Sam, Kevin Chappell from fromBuilder
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Simple Form
 * Plugin URI: https://github.com/sabbirsam/Simple-Form/tree/free
 * Description: It's a simple contact form that lets you easily create forms via drag and drop feature. Its also free to collect all leads from the created forms. This plugin is not yet officially made for selling, it is mainly made for learning.
 * Version:           2.0.0
 * Requires at least: 5.9 or higher
 * Requires PHP:      5.4 or higher
 * Author:            SABBIRSAM
 * Author URI:        https://github.com/sabbirsam/
 * Text Domain:       simpleform
 * Domain Path: /languages/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * 
 */

defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');

define( 'SIMPLEFORM_VERSION', '2.0.0' );
define( 'SIMPLEFORM_BASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'SIMPLEFORM_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'SIMPLEFORM_PLUGIN_FILE', __FILE__ );
define( 'SIMPLEFORM_PLUGIN_NAME', 'Simple Form' );

// Define the class and the function.
require_once dirname( __FILE__ ) . './app/ESTT.php';

// Run the plugin.
ESTT();