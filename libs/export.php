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
        $data = array();

        foreach($users as $user){
            $id = $user->ID;
            $meta = get_user_meta($id);

            foreach($user as $i => $v){
                if(!is_array($i)){
                    $data[$id][$i] = $v;    
                }
            }

            foreach($meta as $kk => $ii){
                if(!(array_key_exists($kk, $fields)) && (!is_array($kk))){
                    $m = get_user_meta($id, $kk, true);
                    $data[$id][$kk] = $m;
                }
            }
        }
        status_header(200);
        
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');

        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');
        $headings = array();
        $i = 0;
        if($i == 0){
            foreach($data as $keys => $vals) {
                foreach($vals as $key => $val){
                    $headings[] = $key;
                }
                $i++;
            }
            fputcsv($file, $headings);  
            
        }
        $file = fopen('php://output', 'a');
        foreach ($data as $keys => $rows) {
            fputcsv($file, $rows, ','); 
        };
        fclose($file);
    }
}
?>