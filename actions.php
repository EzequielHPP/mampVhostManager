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

        if(property_exists($data, 'title') && !is_null($data->title) && $data->title !== '') {

            $templateData .= "\n" . '# ' . $data->title . "\n";
            $templateData .= '<VirtualHost *:80>' . "\n";
            $templateData .= "    " . 'ServerAdmin email@email.com' . "\n";
            $templateData .= "    " . 'DocumentRoot "' . $data->location . '"' . "\n";
            $templateData .= "    " . 'ServerName ' . $data->url . "\n";
            $templateData .= "    " . 'ErrorLog "/Applications/MAMP/logs/' . $data->url . '-error_log"' . "\n";
            $templateData .= "    " . 'CustomLog "/Applications/MAMP/logs/' . $data->url . '-access_log" common' . "\n";
            $templateData .= "    " . '<Directory "' . $data->location . '">' . "\n";
            $templateData .= "    " . '    Options Indexes FollowSymLinks MultiViews' . "\n";
            $templateData .= "    " . '    AllowOverride All' . "\n";
            $templateData .= "    " . '    Order allow,deny' . "\n";
            $templateData .= "    " . '    allow from all' . "\n";
            $templateData .= "    " . '</Directory>' . "\n";
            $templateData .= '</VirtualHost>' . "\n";
        }
    }

    file_put_contents($vhostsLocation . $vhostsFileName, $templateData);
    file_put_contents('hosts.json', json_encode($vhostData));
}

if (isset($_GET['addenewhost'])) {

    $postArray = json_decode($_POST['content'], true);
    $return = array();

    if (!isset($postArray['location']) || !isset($postArray['url'])) {
        $return = array('status' => 'failed', 'message' => 'missing parameters');
        if (!isset($postArray->location)) {
            $return['message'] .= '. Location';
        }
        if (!isset($postArray->url)) {
            $return['message'] .= '. Url';
        }
    } else {

        $vhostData = json_decode(file_get_contents('hosts.json'));

        if ($postArray['hostname'] !== "" && $postArray['location'] !== "" && $postArray['url'] !== "") {
            $vhostData[] = array(
                "title" => $postArray['hostname'],
                "category" => $postArray['category'],
                "location" => $postArray['location'],
                "url" => $postArray['url']
            );

            saveJson($vhostData);

            if (substr(sprintf('%o', fileperms('/etc/hosts')), -4) === '0777') {
                @file_put_contents('/etc/hosts', '# ' . $postArray['hostname'] . "\n", FILE_APPEND);
                @file_put_contents('/etc/hosts', '127.0.0.1     ' . $postArray['url'] . "\n", FILE_APPEND);
            }
            $return = array('status' => 'success', 'message' => '');
        } else {
            $message = array();
            if ($postArray['hostname'] !== "") {
                $message[] = 'hostname';
            }
            if ($postArray['location'] !== "") {
                $message[] = 'location';
            }
            if ($postArray['url'] !== "") {
                $message[] = 'url';
            }
            $return = array('status' => 'false', 'message' => 'Missing parameters: ' . (implode(', ', $message)));
        }

    }

    echo json_encode($return);
} else if (isset($_GET['updatehosts'])) {

    parse_str($_POST['content'], $postArray);
    $return = array();
    $force = (isset($_GET['force']))? $_GET['force'] : false;

    if (is_array($postArray)) {

        if ($force) {
            $force = false;
            $vhostData = [];
        } else {
            $vhostData = json_decode(file_get_contents('hosts.json'));
        }

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
} else if (isset($_GET['restart'])) {
    shell_exec('/Applications/MAMP/bin/restart.sh');
}