<?php
class MRWOOO_LIBS_Export {
    /*
    * This method return a list of all users
    * $include array of user IDs
    * ref: https://codex.wordpress.org/Function_Reference/get_users
    */
    public static function usersData($include = NULL, $fields= NULL){
        if(is_null($include)){
            $include = array();
        }

        $fields = array(
            'ID',
            'user_login',
            'user_nicename',
            'user_email',
            'user_url',
            'user_registered',
            'display_name',
        );

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

        $users = get_users($args);        

        // heading of csv
        $headings = array();

        foreach($fields as $field){
            array_push($headings, $field);
        }
        
        foreach($users as $user) {
            $id = $user->ID;
            $meta = get_user_meta($id);

            foreach($meta as $key => $value) {
                if(!in_array($key, $headings)){
                    array_push($headings, $key);
                }
            }
        }
        
        // collect data
        $data = array();
        
        foreach($users as $user){
            $id = $user->ID;
            foreach($headings as $meta){
                $value = get_user_meta($id, $meta, true);
                if($meta == 'ID' || array_search($meta, $headings)){
                    $data[$id][$meta] = $user->$meta;                    
                }else if ($value == true) {
                    $data[$id][$meta] = true;                    
                } else {
                    $data[$id][$meta] = false;                    
                }
            }
        }

        status_header(200);
        
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');

        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');

        fputcsv($file, $headings);

        $file = fopen('php://output', 'a');

        foreach ($data as $rows => $row) {
            fputcsv($file, $row, ','); 
        };
        fclose($file);
    }
}
?>