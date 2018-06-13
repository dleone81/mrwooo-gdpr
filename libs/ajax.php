<?php
/*
*   This class contains all AJAX scripts
*/
class MRWOOO_GDPR_Ajax {
    function loader(){ ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                // create a loader div
                var loader = $('<div>');
                loader.addClass('mrwooo ajaxloader');
                $('body').prepend(loader);
                $('.mrwooo.ajaxloader').hide();

                $('.mrwooo').ajaxStart(function() {
                    $('.mrwooo.ajaxloader').fadeIn(1000);
                });
                $('.mrwooo').ajaxStop(function() {
                    $('.mrwooo.ajaxloader').fadeOut(500);
                });
            });
        </script>
    <? }
    /*
    *   This method upload CSV file to be imported in users data registry
    */
    function importUsersData(){ ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){

                $('#import_users_data').on('click', function(event){
                    event.preventDefault();

                    // admin notice
                    var container = $('<div>');
                    container.addClass('notice');

                    var p = $('<p>');
                    container.append(p);

                    var formData = new FormData();
                    formData.append('action', 'importUsersData');

                    var metakey = $('input[name="metakey"]').val();
                    formData.append('metakey', metakey);
                    
                    var importFile = $('#filename')[0].files[0];
                    formData.append('import', importFile);

                    $.ajax({
                        url: ajaxurl,
                        data: formData,
                        dataType: "text",
                        type: "POST",
                        success: function(data, textStatus, xhr){
                            // empty
                        },
                        error: function(xhr, textStatus, errorThrown){
                            // empty
                        },
                        complete: function(xhr, textStatus){
                            var status = JSON.stringify(xhr.status);
                            var msg = xhr.responseText;

                            /* 
                            *   notice class wp legacy
                            *   notice-info (blue)
                            *   notice-success (green)
                            *   notice-warning (orange)
                            *   notice-error (red)
                            */

                            switch(status){
                                case '202':
                                    container.removeClass('notice-warning');
                                    container.removeClass('notice-error');

                                    container.addClass('notice-success');
                                    $(p).html(msg);
                                    $('div#wpbody-content').prepend(container);
                                case '400':
                                    container.removeClass('notice-success');
                                    container.removeClass('notice-error');

                                    container.addClass('notice-warning');
                                    $(p).html(msg);
                                    $('div#wpbody-content').prepend(container);
                                case '406':
                                    container.removeClass('notice-success');
                                    container.removeClass('notice-warning');

                                    container.addClass('notice-error');
                                    $(p).html(msg);
                                    $('div#wpbody-content').prepend(container);
                                default:
                                    return false;
                            }
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

                var notice = $('div.notice');
                if(notice.length > 0)
                    notice.remove();

                // admin notice
                var container = $('<div>');
                container.addClass('notice');

                var p = $('<p>');
                container.append(p);

                $('#export_users_data').on('click', function(event){
                    event.preventDefault();

                    var formData = new FormData();
                    formData.append('action', 'exportUsersData');

                    $.ajax({
                        url: ajaxurl,
                        data: formData,
                        dataType: "text",
                        type: "POST",
                        success: function(data, textStatus, xhr){

                            var status = JSON.stringify(xhr.status);

                            /* 
                            *   notice class wp legacy
                            *   notice-info (blue)
                            *   notice-success (green)
                            *   notice-warning (orange)
                            *   notice-error (red)
                            */

                            switch(status){
                                case '201':
                                    var msg = 'File ready. Download now!';
                                    var a = $('<a>');

                                    container.addClass('notice-info');
                                    a.html(msg);
                                    p.append(a);
                                    $('div#wpbody-content').prepend(container);

                                    csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(data);
                                    $(".notice a").attr({
                                        "href": csvData,
                                        "download": "export.csv"
                                    });
                                default:
                                    /*var msg = 'Something went wrong';
                                    container.addClass('notice-error');
                                    $(p).html(msg);
                                    $('div#wpbody-content').prepend(container);*/
                            }
                        },
                        error: function(xhr, textStatus, errorThrown){
                            // empty
                        },
                        complete: function(xhr, textStatus){
                            // empty
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