<?
include('../../config/common.php');

    if(isset($_REQUEST['token'])) {
        $token=$_REQUEST['token'];
    }
    $url = REP_CDN_IVR.'/cdn/audio/'.$token;

    header('Content-type: audio/wav');
    file_get_contents($url);

    $remote = fopen($url, 'rb');

    while(!feof($remote)){
        echo( fread($remote, 4096) );
    }
    
?>