<?php 
class MRWOOO_LIBS_Users {
    /*
    * This method return a list of all users
    * $include array of user IDs
    * ref: https://codex.wordpress.org/Function_Reference/get_users
    */
    public static function getUsers($include = NULL, $fields = NULL) {
        if(is_null($include)){
            // list of ID that will be extract
            $include = array();
        }

        if(is_null($fields)){
            $fields = array(
                'ID',
                'user_login',
                'user_nicename',
                'user_email',
                'user_url',
                'user_registered',
                'display_name',
            );
        }

        // users
        $args = array(
            'blog_id'      => '',
            'role'         => '',
            'role__in'     => array(),
            'role__not_in' => array(),
            'meta_key'     => '',
            'meta_value'   => '',
            'meta_compare' => '',
            'meta_query'   => array(),
            'date_query'   => array(),        
            'include'      => $include,
            'exclude'      => array(),
            'orderby'      => 'login',
            'order'        => 'ASC',
            'offset'       => '',
            'search'       => '',
            'number'       => '',
            'count_total'  => false,
            'fields'       => $fields,
            'who'          => '',
        );

        return get_users($args);
    }
    /*
    *   This method register new column to Users grid
    */
    public static function usersColumnRegister( $columns ) {
        $columns['user_registered'] = __('Registered date', 'mrwooo');
        return $columns;
    }
    /*
    *   This method add new column to Users grid
    */
    public static function usersColumnDisplay( $empty, $column_name, $user_id ) {
        if ( 'user_registered' != $column_name )
            return $empty;
        
        $user_registered = get_userdata($user_id);
        return "$user_registered->user_registered";
    }
    /*
    *   This method make sortable new column added 
    */
    public static function usersRegisteredColumnSortable( $columns ) {
        return wp_parse_args( array( 'user_registered' => 'user_registered' ), $columns );
    }
    /*
    *   This method add new action in drop down menu
    */
    function userDataRegistryAction($bulk_actions) {
        $bulk_actions['export_user_data_registry'] = __( 'User data registry', 'mrwooo');
        return $bulk_actions;
    }
    function userDataRegistryHandler( $redirect_to, $doaction, $post_ids ) {
        if ( $doaction !== 'export_user_data_registry' ) {
          return $redirect_to;
        }
        foreach ( $post_ids as $post_id ) {
          // Perform action for each post.
          echo 'ciao';
        }
        $redirect_to = add_query_arg( 'exported_user_data_registry', count( $post_ids ), $redirect_to );
        return $redirect_to;
    }
    /*
    *   This method is hooked to user_register
    * check if user exists as external, then update meta on new users and delete that
    */
    public static function checkUpdateDelete( $user_id ) {

    if ( isset( $_POST['user_email'] ) )
        $email = $_POST['user_email'];
        $prefix = 'mrwooo____';
        $user = get_user_by('email', $prefix.$email);
        if($user){
            $__id = $user->ID;
            $meta = get_user_meta($__id);

            // unset
            $keys = array(
                'nickname',
                'first_name',
                'last_name',
                'description',
                'rich_editing',
                'syntax_highlighting',
                'comment_shortcuts',
                'admin_color',
                'use_ssl',
                'show_admin_bar_front',
                'locale',
                'wp_capabilities',
                'wp_user_level',
                'dismissed_wp_pointers'
            );

            foreach($keys as $key){
                if(array_key_exists($key, $meta)){
                    unset($meta[$key]);
                }
            }

            $user = get_user_by('email', $email);
            if($user){
                $id = $user->ID;
                update_user_meta($id, '__mrwooo', $meta);
                wp_delete_user( $__id, $id );
            }
        }
    }
}
?>