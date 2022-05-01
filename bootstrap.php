<?php
// Configure PHP to not output any warnings or errors
ini_set('display_errors', FALSE);

// Set the IOS-XE bootstrap script
$XEFILENAME = "/var/www/html/xe_ztp.py";

// Grab the HTTP headers
$headers = apache_request_headers();
if (isset($headers['X-cisco-serial*'])) $serial = str_replace("UTF-8''", "", $headers['X-cisco-serial*']);
if (isset($headers['X-cisco-rp-serial*'])) $rpserial = str_replace("UTF-8''", "", $headers['X-cisco-rp-serial*']);
if (isset($headers['X-cisco-platform*'])) $platform = str_replace("UTF-8''", "", $headers['X-cisco-platform*']);
if (isset($_SERVER['REMOTE_ADDR'])) $remoteAddr = $_SERVER['REMOTE_ADDR']; else $remoteAddr = "UNKNOWN";
if (isset($headers['User-Agent'])) $agent = $headers['User-Agent']; else $agent = "UNKNOWN";

// Open a syslog connection
openlog("ZTP", LOG_PID, LOG_LOCAL0);

//date for headers
$today = date("D, j M Y G:i:s T");

// Determine what OS and model and return the correct file to the device
if ($agent == "cisco-IOS") {
    $devicetype = "IOSXE";
    syslog(LOG_WARNING, "ZTP IOS-XE client: Agent[". $agent . "]");
    header("Last-Modified: " . $today);
    header("Content-Length: " . filesize($XEFILENAME));
    header("Content-Type: text/x-python");
    readfile($XEFILENAME);
    die();
} elseif (isset($platform)) {
    switch($platform) {
        case "NCS1004":
            $devicetype = "IOSXR";
            $XRFILENAME = "/var/www/html/ztp_install_iso_NCS1004.py";
            syslog(LOG_WARNING, "ZTP IOS-XR client: Agent[" . $agent . "] Platform[" . $platform . "] Serial[" . $serial . "]");
            break;
        case "R-IOSXRV9000-CC":
            $devicetype = "IOSXR";
            $XRFILENAME = "/var/www/html/ztp_install_iso_ASR9K.py";
            syslog(LOG_WARNING, "ZTP IOS-XR client: Agent[" . $agent . "] Platform[" . $platform . "] Serial[" . $serial . "]");
            break;

        case "N540-24Z8Q2C-M": //packet capture to find what headers return
            $devicetype = "IOSXR";
            $XRFILENAME = "/var/www/html/ztp_install_iso_NCS540.py";
            syslog(LOG_WARNING, "ZTP IOS-XR client: Agent[" . $agent . "] Platform[" . $platform . "] Serial[" . $serial . "]");
            break;
        case "NCS5500": //packet capture to find what headers return
            $devicetype = "IOSXR";
            $XRFILENAME = "/var/www/html/ztp_install_iso_NCS5500.py";
            syslog(LOG_WARNING, "ZTP IOS-XR client: Agent[" . $agent . "] Platform[" . $platform . "] Serial[" . $serial . "]");
            break;

        default:
//             $devicetype = "IOSXR";
//             $XRFILENAME = "/var/www/html/ztp_install_iso_NCS1004.py";
//             syslog(LOG_WARNING, "ZTP IOS-XR client: Agent[" . $agent . "] Platform[" . $platform . "] Serial[" . $serial . "]");
            syslog(LOG_WARNING, "***ERROR***");
            break;
    }
} else {
//     Device type was not detected, so we just return a catch-all script instead
//     $devicetype = "IOSXR";
//     $XRFILENAME = "/var/www/html/ztp_install_iso_ASR9K.py";
    syslog(LOG_WARNING, "ZTP IOS-XR client: Agent[" . $agent . "] Unknown platform, presuming XR, update or add appropriate script.");
}

// Output the requested script
header("Accept-Ranges: bytes");
header("Last-Modified: " . $today);
// header("ETag: \"2269-5dc6b65661586\"");
header("Content-Length: " . filesize($XRFILENAME));
header("Keep-Alive: timeout=5, max=100");
header("Connection: Keep-Alive");
header("Content-Type: text/plain");
readfile($XRFILENAME);
die();
?>
