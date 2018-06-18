<?php
/*
*   This class contains all AJAX scripts
*/
class MRWOOO_GDPR_Ajax {
    /*
    *   This method upload CSV file to be imported in users data registry
    */
    function importUsersData(){ ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){

                $('#import_users_data').on('click', function(event){
                    event.preventDefault();

                    var formData = new FormData();
                    formData.append('action', 'importUsersData');

                    var metakey = $('input[name="metakey"]').val();
                    formData.append('metakey', metakey);
                    
                    var importFile = $('#filename')[0].files[0];
                    formData.append('import', importFile);

                    $.ajax({
                        url: ajaxurl,
                        data: formData,
                        dataType: "json",
                        type: "POST",
                        success: function(data, textStatus, xhr){
                            // empty
                        },
                        error: function(xhr, textStatus, errorThrown){
                            // empty
                        },
                        complete: function(xhr, textStatus){

                            var method = 'importUsersData';
                            var status = JSON.stringify(xhr.status);
                            var msg = xhr.responseText;

                            notice.getNotice(method, status, msg);
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                });
            })
        </script>
    <? }
    /*
    *   This method export all users data report
    */
    function exportUsersData() { ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('#export_users_data').on('click', function(event){
                    event.preventDefault();

                    var formData = new FormData();
                    formData.append('action', 'exportUsersData');

                    $.ajax({
                        url: ajaxurl,
                        data: formData,
                        dataType: "json",
                        type: "POST",
                        success: function(data, textStatus, xhr){
                            // empty
                        },
                        error: function(xhr, textStatus, errorThrown){
                            // empty
                        },
                        complete: function(xhr, textStatus){

                            var method = 'exportUsersData';
                            var status = JSON.stringify(xhr.status);
                            var message = null;
                            var data = xhr.responseText;

                            // notice
                            notice.getNotice(method, status, message, data);
                        },
                        cache: false,
                        contentType: false,
                        processData: false

                    });
                });
            });
        </script>
    <? }
}
?>