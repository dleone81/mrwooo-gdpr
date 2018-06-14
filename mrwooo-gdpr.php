<?php
/*
Plugin Name:  MrWooo GDPR
Plugin URI:   https://gdpr.mrwooo.com
Description:  This plugin add new features to Wordpress GDPR compliance tools.
Version:      0.1.0
Author:       Domenico Leone
Author URI:   https://github.com/dleone81
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  mrwooop-api
Domain Path:  /languages
*/

add_action( 'admin_init', 'mrwooo_gdpr_parent' );
function mrwooo_gdpr_parent() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'mrwooo-api-core/mrwooo-api-core.php' ) ) {
        add_action( 'admin_notices', 'mrwooo_gdpr_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function mrwooo_gdpr_activate() {
    $cap = array('read' => true);
    add_role('external', __('External', 'mrwooo'), $cap);
}
register_activation_hook( __FILE__, 'mrwooo_gdpr_activate' );

function mrwooo_gdpr_notice(){
    ?><div class="error"><p><?php _e('Sorry, Mr.Wooo GDPR requires Mr.Wooo API Core to be installed and active.', 'mrwoo'); ?></p></div><?php
}

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require_once (ABSPATH . 'wp-admin/includes/user.php');

// define
define( 'MRWOOOGDPR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// libs
require_once(MRWOOOGDPR_PLUGIN_DIR. 'libs/ajax.php');
require_once(MRWOOOGDPR_PLUGIN_DIR. 'libs/export.php');
require_once(MRWOOOGDPR_PLUGIN_DIR. 'libs/import.php');
require_once(MRWOOOGDPR_PLUGIN_DIR. 'libs/users.php');

// i18n
load_plugin_textdomain('mrwooo', false, basename( dirname( __FILE__ ) ) . '/languages' );

// action
add_action( 'mrwooo_gdpr_import_export', array('MRWOOO_GDPR_Ajax', 'importUsersData'));
add_action( 'mrwooo_gdpr_import_export', array('MRWOOO_GDPR_Ajax', 'exportUsersData'));
add_action( 'wp_ajax_importUsersData', array('MRWOOO_LIBS_Import', 'importUsersData'));
add_action( 'wp_ajax_exportUsersData', array('MRWOOO_LIBS_Export', 'exportUsersData'));
add_action( 'user_register', array('MRWOOO_LIBS_Users', 'checkUpdateDelete'), 10, 1 );

// filter
add_filter( 'manage_users_columns', array('MRWOOO_LIBS_Users', 'usersColumnRegister'));
add_filter( 'manage_users_custom_column', array('MRWOOO_LIBS_Users', 'usersColumnDisplay'), 10, 3 );
add_filter( 'manage_users_sortable_columns', array('MRWOOO_LIBS_Users', 'usersRegisteredColumnSortable'));
add_filter( 'bulk_actions-users', array('MRWOOO_LIBS_Users', 'userDataRegistryAction'));
add_filter( 'handle_bulk_actions-users', array('MRWOOO_LIBS_Users', 'userDataRegistryHandler'));
?>