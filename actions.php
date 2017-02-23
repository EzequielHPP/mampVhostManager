<?php
/**
 * Created by PhpStorm.
 * User: ezequielpereira
 * Date: 30/01/2017
 * Time: 11:39
 */

$vhostsLocation = '/Applications/MAMP/conf/apache/extra/';
$vhostsFileName = 'httpd-vhosts.conf';

if(isset($_GET['addenewhost'])){

    $postArray = json_decode($_POST['content']);
    $postArray = $postArray->addHost;
    $return = array();

    if(!isset($postArray->ServerAdmin) || !isset($postArray->DocumentRoot) || !isset($postArray->ServerName)){
        var_dump($postArray);
        $return = array('status'=>'failed','message'=>'missing parameters');
    } else {
        $vhostsFile = file_get_contents($vhostsLocation . $vhostsFileName);

        $templateData = "\n" . '# '.$postArray->hostname.'' . "\n";
        $templateData .= '<VirtualHost *:80>' . "\n";
        $templateData .= "    " . 'ServerAdmin ' . $postArray->ServerAdmin . "\n";
        $templateData .= "    " . 'DocumentRoot "/Applications/MAMP/htdocs/Sites/' . str_replace('//','/',str_replace('/Applications/MAMP/htdocs/Sites/','',$postArray->DocumentRoot)) . '"' . "\n";
        $templateData .= "    " . 'ServerName ' . $postArray->ServerName . "\n";
        $templateData .= "    " . 'ErrorLog "/Applications/MAMP/logs/' . $postArray->ServerName . '-error_log"' . "\n";
        $templateData .= "    " . 'CustomLog "/Applications/MAMP/logs/' . $postArray->ServerName . '-access_log" common' . "\n";
        $templateData .= '</VirtualHost>' . "\n";

        $vhostsFile .= $templateData;
        file_put_contents($vhostsLocation . $vhostsFileName, $vhostsFile);

        $return = array('status' => 'success', 'message' => '');
    }

    echo json_encode($return);
}