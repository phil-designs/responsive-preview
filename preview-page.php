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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700" rel="stylesheet" type="text/css">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<link href="css/rp-styles.css" rel="stylesheet">
</head>

<body id="rp-bg">
    <div id="preview-bar">
        <div class="rp-container">
            <a href="https://phildesigns.com/" target="_blank"><div id="aa-logo"></div></a>
            <div class="title">Responsive Preview For <?php bloginfo( 'name' ); ?></div>
            <div class="rp-controls-right">

                <!-- Device Selector -->
                <div class="sel-box">
                    <div class="select" id="device-select-trigger">
                        <span id="select">SELECT VIEWPORT</span>
                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                    </div>
                    <ul class="toc-odd level-1" id="sel-option">
                        <?php
                        $all_devices = function_exists( 'rp_get_all_devices' ) ? rp_get_all_devices() : array();
                        $selected    = get_option( 'rp_devices', array() );
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
                            ?>
                            <li class="icon-row">
                                <a id="viewport-change-<?php echo esc_attr( $key ); ?>" href="#" data-key="<?php echo esc_attr( $key ); ?>">
                                    <i class="<?php echo esc_attr( $device['icon'] ); ?>" aria-hidden="true"></i>
                                    <?php echo esc_html( $device['name'] ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- User Agent Selector -->
                <div class="sel-box" id="ua-sel-box">
                    <div class="select" id="ua-select-trigger">
                        <span id="ua-label">SELECT USER AGENT</span>
                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                    </div>
                    <ul class="toc-odd toc-ua" id="ua-options">
                        <li><a href="#" data-ua="default">Default (Browser UA)</a></li>
                        <li class="ua-group-header">— Desktop —</li>
                        <li><a href="#" data-ua="chrome-win">Chrome 135 &middot; Windows</a></li>
                        <li><a href="#" data-ua="chrome-mac">Chrome 135 &middot; macOS</a></li>
                        <li><a href="#" data-ua="firefox-win">Firefox 137 &middot; Windows</a></li>
                        <li><a href="#" data-ua="firefox-mac">Firefox 137 &middot; macOS</a></li>
                        <li><a href="#" data-ua="safari-mac">Safari 18 &middot; macOS</a></li>
                        <li><a href="#" data-ua="edge-win">Edge 135 &middot; Windows</a></li>
                        <li class="ua-group-header">— Mobile —</li>
                        <li><a href="#" data-ua="safari-iphone">Safari &middot; iPhone iOS 18</a></li>
                        <li><a href="#" data-ua="chrome-iphone">Chrome &middot; iPhone iOS 18</a></li>
                        <li><a href="#" data-ua="safari-ipad">Safari &middot; iPad iPadOS 18</a></li>
                        <li><a href="#" data-ua="chrome-android">Chrome 135 &middot; Android</a></li>
                        <li><a href="#" data-ua="samsung-android">Samsung Internet &middot; Android</a></li>
                        <li><a href="#" data-ua="firefox-android">Firefox 137 &middot; Android</a></li>
                    </ul>
                </div>

                <!-- Orientation Toggle -->
                <button id="orientation-toggle" style="display:none;">Portrait</button>

            </div>
        </div>
    </div>

    <iframe id="rp-window" src="<?php echo get_home_url(); ?>" frameborder="0"></iframe>

    <!-- Responsive Walk Bar -->
    <div id="rp-walk-bar">
        <span class="rp-walk-label">WALK</span>
        <button id="rp-walk-play" title="Animate from 1440px to 320px">
            <i class="fa fa-play"></i>
        </button>
        <input type="range" id="rp-walk-scrubber" min="0" max="100" value="0" step="0.1">
        <span id="rp-walk-width">1440px</span>
    </div>

<script type="text/javascript">
jQuery(function($){

    // =========================================================
    // Admin bar / margin fix inside iframe
    // =========================================================
    var $iframe = $('#rp-window');

    $iframe.on('load', function(){
        try {
            $iframe.contents().find('#wpadminbar').hide();
            $iframe.contents().find('html').css('cssText', 'margin-top: 0px !important;');
        } catch(e) {}
    });

    // =========================================================
    // Device / Viewport
    // =========================================================
    var devices = <?php echo json_encode( rp_get_all_devices() ); ?>;
    var currentDevice = null;
    var currentOrientation = 'portrait';

    function setViewport(key) {
        if ( ! devices[key] ) return;
        if ( currentDevice !== key ) currentOrientation = 'portrait';
        currentDevice = key;
        var dev = devices[key];
        var w = dev.width, h = dev.height;
        if ( currentOrientation === 'landscape' && dev.rotatable ) {
            var tmp = w; w = h; h = tmp;
        }
        $iframe.width(w);
        if ( h === '100%' ) {
            $iframe.css('height', '');
        } else {
            $iframe.height(h);
        }
        if ( dev.rotatable ) {
            $('#orientation-toggle').show().text(currentOrientation);
        } else {
            $('#orientation-toggle').hide();
        }
    }

    $('#device-select-trigger').click(function(){
        $('#sel-option').toggle();
        $('#ua-options').hide();
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
        if ( ! currentDevice ) return;
        currentOrientation = ( currentOrientation === 'portrait' ) ? 'landscape' : 'portrait';
        setViewport(currentDevice);
    });

    // =========================================================
    // User Agent Switcher
    // Source: useragents.me — baked-in list, current as of 2025
    // Note: overrides navigator.userAgent via JS after iframe load;
    // affects JS-based UA detection but not HTTP-level headers.
    // =========================================================
    var userAgents = {
        'default':         { label: 'Default (Browser UA)',       string: null },
        'chrome-win':      { label: 'Chrome 135 · Windows',      string: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36' },
        'chrome-mac':      { label: 'Chrome 135 · macOS',        string: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36' },
        'firefox-win':     { label: 'Firefox 137 · Windows',     string: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0' },
        'firefox-mac':     { label: 'Firefox 137 · macOS',       string: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14.7; rv:137.0) Gecko/20100101 Firefox/137.0' },
        'safari-mac':      { label: 'Safari 18 · macOS',         string: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_7_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.4 Safari/605.1.15' },
        'edge-win':        { label: 'Edge 135 · Windows',        string: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0' },
        'safari-iphone':   { label: 'Safari · iPhone iOS 18',    string: 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_3_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.3.2 Mobile/15E148 Safari/604.1' },
        'chrome-iphone':   { label: 'Chrome · iPhone iOS 18',    string: 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_3_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/135.0.6478.153 Mobile/15E148 Safari/604.1' },
        'safari-ipad':     { label: 'Safari · iPad iPadOS 18',   string: 'Mozilla/5.0 (iPad; CPU OS 18_3_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.3.2 Mobile/15E148 Safari/604.1' },
        'chrome-android':  { label: 'Chrome 135 · Android',      string: 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Mobile Safari/537.36' },
        'samsung-android': { label: 'Samsung Internet · Android',string: 'Mozilla/5.0 (Linux; Android 14; SAMSUNG SM-S911B) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/27.0 Chrome/125.0.0.0 Mobile Safari/537.36' },
        'firefox-android': { label: 'Firefox 137 · Android',     string: 'Mozilla/5.0 (Android 15; Mobile; rv:137.0) Gecko/137.0 Firefox/137.0' }
    };
    var currentUA = 'default';

    function injectUA() {
        if ( currentUA === 'default' || ! userAgents[currentUA] || ! userAgents[currentUA].string ) return;
        var uaString = userAgents[currentUA].string;
        try {
            Object.defineProperty( document.getElementById('rp-window').contentWindow.navigator, 'userAgent', {
                get: function() { return uaString; },
                configurable: true
            });
        } catch(e) {}
    }

    // Re-inject on every iframe navigation so it survives page loads
    $iframe.on('load', function(){
        injectUA();
    });

    $('#ua-select-trigger').click(function(){
        $('#ua-options').toggle();
        $('#sel-option').hide();
    });

    $('#ua-options').on('click', 'a', function(e){
        e.preventDefault();
        var uaKey = $(this).data('ua');
        if ( ! userAgents[uaKey] ) return;
        currentUA = uaKey;
        $('#ua-label').text( userAgents[uaKey].label );
        $('#ua-options').hide();
        document.getElementById('rp-window').contentWindow.location.reload();
    });

    // Close all dropdowns on outside click
    $(document).on('click', function(){
        $('#sel-option, #ua-options').hide();
    });
    $('.sel-box').on('click', function(e){
        e.stopPropagation();
    });

    // =========================================================
    // Responsive Walk
    // Animates iframe width from 1440px → 320px over 10 seconds
    // =========================================================
    var walk = {
        active:     false,
        startWidth: 1440,
        endWidth:   320,
        duration:   10000,
        startTime:  null,
        animFrame:  null,
        progress:   0      // 0–100
    };

    function walkApplyProgress(progress) {
        progress = Math.max(0, Math.min(100, progress));
        walk.progress = progress;
        var width = Math.round( walk.startWidth - (walk.startWidth - walk.endWidth) * (progress / 100) );
        $iframe.width(width);
        document.getElementById('rp-walk-scrubber').value = progress;
        updateScrubberFill(progress);
        $('#rp-walk-width').text(width + 'px');
    }

    function updateScrubberFill(progress) {
        $('#rp-walk-scrubber').css(
            'background',
            'linear-gradient(to right, #F7941D ' + progress + '%, #444444 ' + progress + '%)'
        );
    }

    function walkTick(timestamp) {
        if ( ! walk.active ) return;
        if ( ! walk.startTime ) {
            // Resume from current progress position
            walk.startTime = timestamp - (walk.progress / 100 * walk.duration);
        }
        var elapsed  = timestamp - walk.startTime;
        var progress = Math.min( (elapsed / walk.duration) * 100, 100 );
        walkApplyProgress(progress);
        if ( progress < 100 ) {
            walk.animFrame = requestAnimationFrame(walkTick);
        } else {
            walk.active    = false;
            walk.animFrame = null;
            $iframe.css('transition', '');
            $('#rp-walk-play i').removeClass('fa-pause').addClass('fa-repeat');
        }
    }

    function walkPlay() {
        if ( walk.progress >= 100 ) {
            // Restart from the beginning
            walkApplyProgress(0);
        }
        walk.active    = true;
        walk.startTime = null;
        $iframe.css('transition', 'none'); // disable CSS transition during rAF animation
        $('#rp-walk-play i').removeClass('fa-play fa-repeat').addClass('fa-pause');
        walk.animFrame = requestAnimationFrame(walkTick);
    }

    function walkPause() {
        walk.active = false;
        if ( walk.animFrame ) {
            cancelAnimationFrame(walk.animFrame);
            walk.animFrame = null;
        }
        walk.startTime = null;
        $iframe.css('transition', '');
        $('#rp-walk-play i').removeClass('fa-pause').addClass('fa-play');
    }

    $('#rp-walk-play').on('click', function(){
        if ( walk.active ) {
            walkPause();
        } else {
            walkPlay();
        }
    });

    $('#rp-walk-scrubber').on('input', function(){
        if ( walk.active ) walkPause();
        walkApplyProgress( parseFloat(this.value) );
    });

    // Initialise scrubber fill
    updateScrubberFill(0);

});
</script>

</body>
</html>
