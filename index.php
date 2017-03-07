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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

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
        <div class="col-sm-12">
            <?php
            foreach ($hosts as $host => $data) {
                if ($host != '' && trim($host) != 'MAMP Admin') {
                    $currentSections = array();
                    $data = str_replace(' common', '', $data);
                    $data = str_replace('</VirtualHost>', '', $data);
                    $dataExploded = explode(' ', $data);
                    $nextSection = '';

                    foreach ($dataExploded as $line) {
                        $line = str_replace('<','',$line);
                        $line = trim($line);
                        if (strlen($line) > 6) {
                            $matched = false;
                            foreach ($sections as $section => $placeholder) {
                                if (trim(strtolower($section)) == trim(strtolower($line))) {
                                    $matched = true;
                                }
                            }
                            if (!$matched) {
                                $currentSections[trim(strtolower($nextSection))] = trim(str_replace('"', '', $line));
                            } else {
                                $nextSection = trim(strtolower($line));
                                $currentSections[trim(strtolower($nextSection))] = '';
                            }
                        }
                    }

                    if(rand(0,10) < 5){
                        $img = 'grayscale';
                    } else {
                        $img = 'sepia';
                    }

                    $tmp = rand(0,2);
                    $array = ['nature','tech','arch'];
                    $what = $array[$tmp];

                    ?>
                    <div class="card" style="width: 20rem; float:left; margin:10px;">
                        <img class="card-img-top" src="https://placeimg.com/318/180/<?php echo $what; ?>/<?php echo $img; ?>" alt="Card image cap">
                        <div class="card-block">
                            <h4 class="card-title"><?php echo $host; ?></h4>
                            <a href="http://<?php echo $currentSections['servername']; ?>" class="btn btn-primary" target="_blank">Go to website</a>
                        </div>
                    </div>
                    <form role="form" id="<?php echo md5($host); ?>" style="display:none">
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

            $tmp = rand(0,2);
            $array = ['nature','tech','arch'];
            $what = $array[$tmp];
            ?>

            <div class="card" style="width: 20rem; float:left; margin:10px;">
                <img class="card-img-top" src="https://placeimg.com/318/180/<?php echo $what; ?>/<?php echo $img; ?>" alt="Card image cap">
                <div class="card-block">
                    <h4 class="card-title">New Website</h4>

                </div>
            </div>
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
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
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