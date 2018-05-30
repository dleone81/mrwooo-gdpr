<?php 
class MRWOOO_LIBS_Users {

    public static function usersColumnRegister( $columns ) {
        $columns['user_registered'] = __('Registered date', 'mrwooo');
        return $columns;
    }

    public static function usersColumnDisplay( $empty, $column_name, $user_id ) {
        if ( 'user_registered' != $column_name )
            return $empty;
        
        $user_registered = get_userdata($user_id);
        return "$user_registered->user_registered";
    }
    
    public static function usersRegisteredColumnSortable( $columns ) {
        return wp_parse_args( array( 'user_registered' => 'user_registered' ), $columns );
    }
}
?>