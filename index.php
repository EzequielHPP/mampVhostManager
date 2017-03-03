<?php
$hosts = array();

$vhostsLocation = '/Applications/MAMP/conf/apache/extra/';
$docroot = '/Applications/MAMP/htdocs/Sites/';
$vhostsFileName = 'httpd-vhosts.conf';

$vhostsFile = file_get_contents($vhostsLocation . $vhostsFileName);

if ($vhostsFile != '') {
    if (strpos($vhostsFile, '#START WEBSITES HERE') !== false) {
        copy($vhostsLocation . $vhostsFileName, __DIR__.'/files/'.$vhostsFileName);
        $hostsData = explode('#START WEBSITES HERE', $vhostsFile);
        $hostsSplit = $hostsData[1];

        $array = explode('#', $hostsSplit);

        foreach ($array as $host) {
            $title = substr($host, 0, strpos($host, '<'));
            $hostDataSplit = explode('<VirtualHost *:80>', $host);
            if(isset($hostDataSplit[1])) {
                $hostData = '<VirtualHost *:80>' . $hostDataSplit[1];
            } else {
                $hostData = $hostDataSplit;
            }

            $hosts[$title] = $hostData;
        }
    } else {
        copy(__DIR__ . '/files/' . $vhostsFileName, $vhostsLocation.$vhostsFileName);
        echo '<script>window.location = window.location</script>';
    }
}

$sections = array(
    'ServerAdmin ' => 'email@here.com',
    'DocumentRoot ' => 'Root directory of website',
    'ServerName ' => 'urlhere.com',
    'ErrorLog ' => '',
    'CustomLog ' => ''
)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MAMP Virtual Host manager</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <h1>
                    MAMP!
                    <small>VirtualHost Manager</small>
                </h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Virtual Hosts
                    </h3>
                </div>
                <div class="panel-body">
                    <ul>
                        <?php
                        foreach ($hosts as $host => $data) {
                            if ($host != '' && trim($host) != 'MAMP Admin') {
                                echo '<li class="showVHost" data-host="' . md5($host) . '">' . trim($host) . '</li>';
                            }
                        }
                        ?>
                        <li class="showVHost" data-host="addHost">+ Add a new host</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <?php
            foreach ($hosts as $host => $data) {
                if ($host != '' && trim($host) != 'MAMP Admin') {
                    $currentSections = array();
                    $data = str_replace(' common', '', $data);
                    $data = str_replace('</VirtualHost>', '', $data);
                    $dataExploded = explode(' ', $data);
                    $nextSection = '';
                    ?>
                    <form role="form" id="<?php echo md5($host); ?>" style="display:none">
                        <div>
                        <?php

                            foreach ($dataExploded as $line) {
                                $line = str_replace('<','',$line);
                                // $line = str_replace('*:80>','',$line);
                                $line = trim($line);
                                if (strlen($line) > 6) {
                                    $matched = false;
                                    foreach ($sections as $section => $placeholder) {
                                        // echo trim(strtolower($section)).' == '.trim(strtolower($line)).'<br> ';
                                        if (trim(strtolower($section)) == trim(strtolower($line))) {
                                            $matched = true;
                                        }
                                    }
                                    // echo '---------------------<br>';
                                    if (!$matched) {
                                        $currentSections[trim(strtolower($nextSection))] = trim(str_replace('"', '', $line));
                                    } else {
                                        $nextSection = trim(strtolower($line));
                                        $currentSections[trim(strtolower($nextSection))] = '';
                                    }
                                }
                            }

                        ?>
                        </div>
                        <div class="form-group">

                            <label for="exampleInputEmail1">
                                Host:
                            </label>
                            <input type="text" class="form-control" id="hostname" name="hostname" value="<?php echo trim($host); ?>"/>
                        </div>
                        <?php
                        foreach ($sections as $section => $placeholder) {
                            $placeholderHtml= '';
                            if($placeholder != ''){
                                $placeholderHtml = 'placeholder="'.$placeholder.'"';
                            }
                            ?>
                            <div class="form-group">

                                <label for="<?php echo md5($host) . '-' . trim(strtolower($section)); ?>">
                                    <?php echo $section; ?>
                                </label>
                                <input type="text" class="form-control" name="<?php echo trim($section); ?>"  <?php echo $placeholderHtml; ?> id="<?php echo md5($host) . '-' . trim(strtolower($section)); ?>" value="<?php echo $currentSections[trim(strtolower($section))]; ?>"/>
                            </div>
                            <?php
                        };
                        ?>
                        <button type="submit" class="btn btn-default">
                            Update
                        </button>
                    </form>
                    <?php
                }
            };
            ?>
            <form role="form" id="addHost" style="display:none">
                <div class="form-group">

                    <label for="exampleInputEmail1">
                        Host:
                    </label>
                    <input type="text" class="form-control" id="hostname" name="hostname" value="" placeholder="Name Of The Host"/>
                </div>
                <?php
                foreach ($sections as $section => $placeholder) {
                    if($placeholder != '') {
                        ?>
                        <div class="form-group">

                            <label for="<?php echo 'addHost-' . trim(strtolower($section)); ?>">
                                <?php echo $section; ?>
                            </label>
                            <input type="text" class="form-control" name="<?php echo trim($section); ?>" id="<?php 'addHost-' . trim(strtolower($section)); ?>" placeholder="<?php echo $placeholder; ?>" value=""/>
                        </div>
                        <?php
                    }
                };
                ?>
                <button type="submit" class="btn btn-success">
                   + Create
                </button>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        $('li.showVHost').click(function () {
            var virtual = $(this).attr('data-host');
            $('form[role="form"]').hide();
            $('#' + virtual).show();
        });

        $('button[type="submit"]').click(function(e){
            e.preventDefault();
            var json = buildJson(true);
            if($(this).parents('form').attr('id') == 'addHost') {
                var data = {
                    "content": JSON.stringify(json)
                };

                console.log(data);

                $.ajax({
                    type: "POST",
                    url: '/actions.php?addenewhost=true',
                    data: data,
                    success: function (response) {
                        var responseDetails = JSON.parse(response);
                        if (responseDetails.status == 'success') {
                            refresh();
                        } else {
                            alert(responseDetails.message);
                        }
                    },
                    fail: function (response) {
                        console.log(response);
                        alert('Couldn\'t save the brand at the moment');
                    }
                });
            } else {
                console.log(json);
            }
        });

        function buildJson(justNewHost){
            if(justNewHost === undefined){
                justNewHost = false;
            }
            var json = {};
            $('form[role="form"]').each(function () {
                var tmpId = $(this).attr('id');
                if(!justNewHost || (justNewHost == true && tmpId == 'addHost')) {
                    json[tmpId] = {};
                    $(this).find('input').each(function () {
                        json[tmpId][$(this).attr('name')] = $(this).val();
                    });
                }
            });

            return json;
        }

        function refresh(){
            window.location = window.location;
        }
    });
</script>
</body>
</html>