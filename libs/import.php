<?php
    class MRWOOO_LIBS_Import {
        private static $event = '/import/users-data-registry';

        public static function importUsersData(){

            // vars
            $metakey = $_POST['metakey'];
            $file = $_FILES['import']['tmp_name'];

            // check
            $check = array();
            $mime = mime_content_type($file);
            $mime = ($mime == 'text/plain' ? true : false);
            $check['mime'] = $mime;

            $size = filesize($file);
            $size = ($size > 0 ? true : false);
            $check['size'] = $size;

            if(!array_search(false, $check)){
                $error = array();
                $row = 0;
                if (($handle = fopen($file, "r")) !== false) {

                    while (($data = fgetcsv($handle, 1000, ',', '"')) !== false) {
                        $num = count($data);
                        if($row == 0){
                            $heading = $data;
                            if(!array_search('user_email', $heading) == false) {
                                return false;
                            }
                        } else {
                            $i = ($row-1);
                            $update = array();
                            foreach($data as $k => $v){
                                $key = $heading[$k];
                                if($key == 'user_email'){
                                    $email = trim($v);
                                    $check = filter_var($email, FILTER_VALIDATE_EMAIL);
                                    if(!$check) {
                                        $msg = sprintf(__('File partially imported! Skipped at row contains %s as user_email. Fixing that and reimport all'), $email);

                                        header('HTTP/1.1 400 Bad Request');
                                        header('Content-Type: application/json; charset=UTF-8');
                                        die(json_encode($msg));
                                    }
                                }
                                $update[$key] = $v;
                            }
                            $update['last_update'] = current_time('mysql');

                            // check if user exists
                            $email = $update['user_email'];
                            $user = get_user_by('email', $update['user_email']);
                            if($user) {
                                $id = $user->ID;
                                update_user_meta($id, $metakey, $update);
                            } else {
                                // create user with external role
                                $prefix = 'mrwooo____';
                                $password = wp_generate_password( $length=12, $include_standard_special_chars=false );
                                $user = wp_create_user( $prefix.$email, $password, $prefix.$email );

                                // assign user role
                                $u = new WP_User($user);
                                $u->set_role('external');

                                $id = $u->ID;
                                update_user_meta($id, $metakey, $update);
                            }
                        }
                        $row++;
                    }
                    fclose($handle);
                } else {
                    $msg = __('Upload a valid file', 'mrwooo');

                    header('HTTP/1.1 406 Not Acceptable');
                    header('Content-Type: application/json; charset=UTF-8');
                    die(json_encode($msg));
                }
            }
            $msg = __('File imported. Great!', 'mrwooo');

            header('HTTP/1.1 202 Accepted');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode($msg));
        }
    }
?>