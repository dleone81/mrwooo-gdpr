<?php
class MRWOOO_LIBS_Export {
    private static $event = '/export/users-data-registry';

    public static function exportUsersData(){

        $fields = array(
            'ID',
            'user_login',
            'user_nicename',
            'user_email',
            'user_url',
            'user_registered',
            'display_name',
        );

        $users = MRWOOO_LIBS_Users::getUsers();

        // headings of csv
        $headings = array();

        foreach($fields as $field){
            array_push($headings, $field);
        }
        
        foreach($users as $user) {
            $id = $user->ID;
            $meta = get_user_meta($id);

            foreach($meta as $key => $value) {
                // add meta fields as headings in addition to fields
                if(!in_array($key, $headings)){
                    array_push($headings, $key);
                }
            }
        }
        
        // collect data
        $data = array();
        foreach($users as $user){
            $id = $user->ID;
            foreach ($fields as $field) {
                $data[$id][$field] = $user->$field;                
            }
            foreach($headings as $meta) {
                if(!in_array($meta, $fields)){
                    $value = get_user_meta($id, $meta, true);
                    $val = ($value == true ? intval(1) : intval(0));
                    $data[$id][$meta] = $val;
                }
            }
        }
        
        header('HTTP/1.1 201 Created');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="export_'.time().'.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');

        fputcsv($file, $headings);

        $file = fopen('php://output', 'a');

        foreach ($data as $rows => $row) {
            fputcsv($file, $row, ','); 
        };
        fclose($file);

        // status_header(201);

        // log
        $user = wp_get_current_user();
        $admin = $user->ID;

        MRWOOO_DB_Logger::create($admin, self::$event, '201', $_SERVER['REMOTE_ADDR']);
    }
}
?>