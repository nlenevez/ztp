<?php

ini_set('display_errors', FALSE);

$XEFILENAME = "/var/www/html/ztp-advanced.py";

$headers = apache_request_headers();
if (isset($headers['X-cisco-serial*']))
{
  $serial = str_replace("UTF-8''", "", $headers['X-cisco-serial*']);
}
if (isset($headers['X-cisco-rp-serial*']))
{
  $rpserial = str_replace("UTF-8''", "", $headers['X-cisco-rp-serial*']);
}
if (isset($headers['X-cisco-platform*']))
{
  $platform = str_replace("UTF-8''", "", $headers['X-cisco-platform*']);
}
if (isset($_SERVER['REMOTE_ADDR']))
{
  $remoteAddr = $_SERVER['REMOTE_ADDR'];
}
  else
{
  $remoteAddr = "UNKNOWN";
}
if (isset($headers['User-Agent']))
{
  $agent = $headers['User-Agent'];
}
  else
{
  $agent = "UNKNOWN";
}

openlog("ZTP", LOG_PID, LOG_LOCAL0);

if ($agent == "cisco-IOS")
{
  $devicetype = "IOSXE";
  syslog(LOG_WARNING, "ZTP IOS-XE client: Agent[ ". $agent . "]");
}
  elseif (isset($platform))
{
  switch($platform)
  {
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
     default:
       $devicetype = "IOSXR";
       $XRFILENAME = "/var/www/html/ztp_install_iso_NCS1004.py";
       syslog(LOG_WARNING, "ZTP IOS-XR client: Agent[" . $agent . "] Platform[" . $platform . "] Serial[" . $serial . "]");
       break;
   }
}
   else
{
   $devicetype = "UNKNOWN";
}

if ($devicetype == "IOSXE")
{
  header("Last-Modified: Tue, 12 Apr 2039 01:58:24 GMT");
  header("Content-Length: " . filesize($XEFILENAME));
  header("Content-Type: text/x-python");
  readfile($XEFILENAME);
  die();
}
  elseif ($devicetype == "IOSXR")
{
  header("Accept-Ranges: bytes");
  header("Last-Modified: Tue, 12 Apr 2039 01:58:24 GMT");
  header("ETag: \"2269-5dc6b65661586\"");
  header("Content-Length: " . filesize($XRFILENAME));
  header("Keep-Alive: timeout=5, max=100");
  header("Connection: Keep-Alive");
  header("Content-Type: text/plain");
  readfile($XRFILENAME);
  die();
}
  else
{
  echo "Not a Cisco device";
  die();
}
?>
