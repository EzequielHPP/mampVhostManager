<?php
/**
 * Created by PhpStorm.
 * User: ezequielpereira
 * Date: 30/01/2017
 * Time: 11:39
 */

include('vendor/autoload.php');

$vhostsLocation = '/Applications/MAMP/conf/apache/extra/';
$vhostsTemplateFolder = 'templates/';
$vhostsFileName = 'httpd-vhosts.conf';

if(isset($_GET['addenewhost'])){

    $postArray = json_decode($_POST['content']);
    $return = array();

    if(!isset($postArray->DocumentRoot) || !isset($postArray->ServerName)){
        $return = array('status'=>'failed','message'=>'missing parameters');
    } else {
        $vhostsFile = file_get_contents($vhostsLocation . $vhostsFileName);

        $templateData = "\n" . '# '.$postArray->hostname . ' | '. $postArray->category . "\n";
        $templateData .= '<VirtualHost *:80>' . "\n";
        $templateData .= "    " . 'ServerAdmin email@email.com' . "\n";
        $templateData .= "    " . 'DocumentRoot "'. $postArray->DocumentRoot . '"' . "\n";
        $templateData .= "    " . 'ServerName ' . $postArray->ServerName . "\n";
        $templateData .= "    " . 'ErrorLog "/Applications/MAMP/logs/' . $postArray->ServerName . '-error_log"' . "\n";
        $templateData .= "    " . 'CustomLog "/Applications/MAMP/logs/' . $postArray->ServerName . '-access_log" common' . "\n";
	    $templateData .= "    " . '<Directory "'. $postArray->DocumentRoot . '">' . "\n";
	    $templateData .= "    " . '    Options Indexes FollowSymLinks' . "\n";
        $templateData .= "    " . '    AllowOverride All' . "\n";
	    $templateData .= "    " . '</Directory>' . "\n";
        $templateData .= '</VirtualHost>' . "\n";

        $vhostsFile .= $templateData;
        file_put_contents($vhostsLocation . $vhostsFileName, $vhostsFile);

        $return = array('status' => 'success', 'message' => '');
    }

    echo json_encode($return);
}

if(isset($_GET['updatehosts'])){

    $postArray = json_decode($_POST['content']);
    $return = array();

    if(is_array($postArray)){
        $template = file_get_contents($vhostsTemplateFolder . 'vconf.template.conf');
        foreach($postArray as $vHost){
            $template .= "\n" . '# ' . $vHost->hostname . ' | '. $vHost->category . "\n";
            $template .= '<VirtualHost *:80>' . "\n";
            $template .= "    " . 'ServerAdmin email@email.com' . "\n";
            $template .= "    " . 'DocumentRoot "' . $vHost->DocumentRoot . '"' . "\n";
            $template .= "    " . 'ServerName ' . $vHost->ServerName . "\n";
            $template .= "    " . 'ErrorLog "/Applications/MAMP/logs/' . $vHost->ServerName . '-error_log"' . "\n";
            $template .= "    " . 'CustomLog "/Applications/MAMP/logs/' . $vHost->ServerName . '-access_log" common' . "\n";
	        $template .= "    " . '<Directory "'. $vHost->DocumentRoot . '">' . "\n";
	        $template .= "    " . '    Options Indexes FollowSymLinks' . "\n";
	        $template .= "    " . '    AllowOverride All' . "\n";
	        $template .= "    " . '</Directory>' . "\n";
            $template .= '</VirtualHost>' . "\n";
        }

        file_put_contents($vhostsLocation . $vhostsFileName, $template);

        $return = array('status' => 'success', 'message' => '');

    } else {
        if (!isset($postArray->DocumentRoot) || !isset($postArray->ServerName)) {
            var_dump($postArray);
            $return = array('status' => 'failed', 'message' => 'missing parameters');
        } else {
            $vhostsFile = file_get_contents($vhostsLocation . $vhostsFileName);

            $templateData = "\n" . '# ' . $postArray->hostname . ' | '. ((property_exists($vHost,'category'))?$vHost->category:'') .  "\n";
            $templateData .= '<VirtualHost *:80>' . "\n";
            $templateData .= "    " . 'ServerAdmin email@email.com' . "\n";
            $templateData .= "    " . 'DocumentRoot "' . $postArray->DocumentRoot . '"' . "\n";
            $templateData .= "    " . 'ServerName ' . $postArray->ServerName . "\n";
            $templateData .= "    " . 'ErrorLog "/Applications/MAMP/logs/' . $postArray->ServerName . '-error_log"' . "\n";
            $templateData .= "    " . 'CustomLog "/Applications/MAMP/logs/' . $postArray->ServerName . '-access_log" common' . "\n";
	        $templateData .= "    " . '<Directory "'. $postArray->DocumentRoot . '">' . "\n";
	        $templateData .= "    " . '    Options Indexes FollowSymLinks' . "\n";
	        $templateData .= "    " . '    AllowOverride All' . "\n";
	        $templateData .= "    " . '</Directory>' . "\n";
            $templateData .= '</VirtualHost>' . "\n";

            $vhostsFile .= $templateData;
            file_put_contents($vhostsLocation . $vhostsFileName, $vhostsFile);

            $return = array('status' => 'success', 'message' => '');
        }
    }

    echo json_encode($return);
}

if(isset($_GET['restart'])){
	//shell_exec('/Applications/MAMP/bin/restart.sh;');
}