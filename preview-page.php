<?php

$absolute_path = __FILE__;
$path_to_file = explode( 'wp-content', $absolute_path );
$path_to_wp = $path_to_file[0];

// Access WordPress
require_once( $path_to_wp . '/wp-load.php' );

?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Responsive Preview</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Load FontAwesome -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<!-- Load Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700" rel="stylesheet" type="text/css">
<!-- Load JQuery -->
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<link href="css/rp-styles.css" rel="stylesheet">
</head>

<body id="rp-bg">
	<div id="preview-bar">
        <div class="rp-container">
		<a href="https://phildesigns.com/" target="_blank"><div id="aa-logo"></div></a>
        <div class="title">Responsive Preview For <?php bloginfo( 'name' ); ?></div>
		<div class="sel-box">
            <div class="select"><span id="select">SELECT VIEWPORT</span><i class="fa fa-chevron-down" aria-hidden="true"></i></div>
            <ul class="toc-odd level-1" id="sel-option">
                <?php
                // build the list of viewports from defaults + custom
                $all_devices = function_exists('rp_get_all_devices') ? rp_get_all_devices() : array();
                $selected     = get_option('rp_devices', array());
                if ( ! is_array( $selected ) ) {
                    $selected = array();
                }
                if ( empty( $selected ) ) {
                    $selected = array( 'desktop' => 1 );
                }

                foreach ( $all_devices as $key => $device ) :
                    if ( ! isset( $selected[ $key ] ) || ! $selected[ $key ] ) {
                        continue;
                    }
                    $link_id = 'viewport-change-' . esc_attr( $key );
                    ?>
                    <li class="icon-row">
                        <a id="<?php echo $link_id; ?>" href="#" data-key="<?php echo esc_attr( $key ); ?>">
                            <i class="<?php echo esc_attr( $device['icon'] ); ?>" aria-hidden="true"></i>
                            <?php echo esc_html( $device['name'] ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <button id="orientation-toggle" style="display:none;">Portrait</button>
        </div>
    </div>

<iframe id="rp-window" src="<?php echo get_home_url(); ?>" align="middle" frameborder="0"></iframe>        

<script type="text/javascript">
    $(function(){
        var f=$('#rp-window');
        f.load(function(){
            f.contents().find('#wpadminbar').hide();
            f.contents().find("html").css("cssText","margin-top: 0px !important;");
        });
    });
</script>

<script type="text/javascript">
jQuery(function($){
    var devices = <?php echo json_encode(rp_get_all_devices()); ?>;
    var currentDevice = null;
    var currentOrientation = 'portrait';

    function setViewport(key) {
        if ( ! devices[key] ) {
            return;
        }
        // reset orientation when switching to a different device
        if ( currentDevice !== key ) {
            currentOrientation = 'portrait';
        }
        currentDevice = key;
        var dev = devices[key];
        var w = dev.width;
        var h = dev.height;
        if ( currentOrientation === 'landscape' && dev.rotatable ) {
            var tmp = w; w = h; h = tmp;
        }
        $("iframe").width(w).height(h);
        if ( dev.rotatable ) {
            $('#orientation-toggle').show().text(currentOrientation);
        } else {
            $('#orientation-toggle').hide();
        }
    }

    // remove any old handlers so we can reattach cleanly
    $('#select,.select').off('click');
    $('#sel-option a').off('click');
    $('#sel-option').off('click');

    $('#select,.select').click(function(){
        $('#sel-option').show();
    });

    $('#sel-option').on('click', 'a', function(e){
        e.preventDefault();
        var key = $(this).data('key');
        $('#select').html($(this).html());
        $('#sel-option').hide();
        $(this).addClass('current').siblings().removeClass('current');
        setViewport(key);
    });

    $('#orientation-toggle').click(function(){
        if ( ! currentDevice ) {
            return;
        }
        currentOrientation = (currentOrientation === 'portrait') ? 'landscape' : 'portrait';
        setViewport(currentDevice);
    });
});
</script>

</body>
</html>

</body>
</html>