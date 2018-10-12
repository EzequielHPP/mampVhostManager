<?php
/**
 * Created by PhpStorm.
 * User: ezequielpereira
 * Date: 30/01/2017
 * Time: 11:39
 */

$vhostsTemplateFolder = 'templates/';

function saveJson($vhostData)
{

    $vhostsLocation = '/Applications/MAMP/conf/apache/extra/';
    $vhostsFileName = 'httpd-vhosts.conf';

    $templateData = file_get_contents('templates/vconf.template.conf');

    foreach ($vhostData as $data) {

        $templateData .= "\n" . '# ' . $data->title . "\n";
        $templateData .= '<VirtualHost *:80>' . "\n";
        $templateData .= "    " . 'ServerAdmin email@email.com' . "\n";
        $templateData .= "    " . 'DocumentRoot "' . $data->location . '"' . "\n";
        $templateData .= "    " . 'ServerName ' . $data->url . "\n";
        $templateData .= "    " . 'ErrorLog "/Applications/MAMP/logs/' . $data->url . '-error_log"' . "\n";
        $templateData .= "    " . 'CustomLog "/Applications/MAMP/logs/' . $data->url . '-access_log" common' . "\n";
        $templateData .= "    " . '<Directory "' . $data->location . '">' . "\n";
        $templateData .= "    " . '    Options Indexes FollowSymLinks' . "\n";
        $templateData .= "    " . '    AllowOverride All' . "\n";
        $templateData .= "    " . '</Directory>' . "\n";
        $templateData .= '</VirtualHost>' . "\n";
    }

    file_put_contents($vhostsLocation . $vhostsFileName, $templateData);
    file_put_contents('hosts.json', json_encode($vhostData));
}

if (isset($_GET['addenewhost'])) {

    $postArray = json_decode($_POST['content']);
    $return = array();

    if (!isset($postArray->DocumentRoot) || !isset($postArray->ServerName)) {
        $return = array('status' => 'failed', 'message' => 'missing parameters');
    } else {

        $vhostData = json_decode(file_get_contents('hosts.json'));

        $vhostData[] = array(
            "title" => $postArray->hostname,
            "category" => $postArray->category,
            "location" => $postArray->DocumentRoot,
            "url" => $postArray->ServerName
        );

        saveJson($vhostData);

        if (substr(sprintf('%o', fileperms('/etc/hosts')), -4) === '0777') {
            @file_put_contents('/etc/hosts', '# ' . $postArray->hostname . "\n", FILE_APPEND);
            @file_put_contents('/etc/hosts', '127.0.0.1     ' . $postArray->ServerName . "\n", FILE_APPEND);
        }

        $return = array('status' => 'success', 'message' => '');
    }

    echo json_encode($return);
}

if (isset($_GET['updatehosts'])) {

    parse_str($_POST['content'], $postArray);
    $return = array();

    if (is_array($postArray)) {

        $vhostData = json_decode(file_get_contents('hosts.json'));

        foreach ($vhostData as $index => $data) {
            if ($data->id == $postArray['id']) {
                $vhostData[$index]->title = $postArray['hostname'];
                $vhostData[$index]->category = $postArray['category'];
                $vhostData[$index]->location = $postArray['location'];
                $vhostData[$index]->url = $postArray['url'];
            }
        }

        saveJson($vhostData);

        $return = array('status' => 'success', 'message' => '');

    }

    echo json_encode($return);
}

if (isset($_GET['restart'])) {
    shell_exec('/Applications/MAMP/bin/restart.sh');
}