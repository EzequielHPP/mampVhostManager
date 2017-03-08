<?php
$hosts = array();

$vhostsLocation = '/Applications/MAMP/conf/apache/extra/';
$docroot = '/Applications/MAMP/htdocs/Sites/';
$vhostsFileName = 'httpd-vhosts.conf';

$vhostsFile = file_get_contents($vhostsLocation . $vhostsFileName);

if ($vhostsFile != '') {
    if (strpos($vhostsFile, '#START WEBSITES HERE') !== false) {
        copy($vhostsLocation . $vhostsFileName, __DIR__ . '/files/' . $vhostsFileName);
        $hostsData = explode('#START WEBSITES HERE', $vhostsFile);
        $hostsSplit = $hostsData[1];

        $array = explode('#', $hostsSplit);

        foreach ($array as $host) {
            $title = substr($host, 0, strpos($host, '<'));
            $hostDataSplit = explode('<VirtualHost *:80>', $host);
            if (isset($hostDataSplit[1])) {
                $hostData = '<VirtualHost *:80>' . $hostDataSplit[1];
            } else {
                $hostData = $hostDataSplit;
            }

            $hosts[$title] = $hostData;
        }
    } else {
        copy(__DIR__ . '/files/' . $vhostsFileName, $vhostsLocation . $vhostsFileName);
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
                        $line = str_replace('<', '', $line);
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

                    if (rand(0, 10) < 5) {
                        $img = 'grayscale';
                    } else {
                        $img = 'sepia';
                    }

                    $tmp = rand(0, 2);
                    $array = ['nature', 'tech', 'arch'];
                    $what = $array[$tmp];

                    ?>
                    <div class="card" style="width: 20rem; float:left; margin:10px;">
                        <img class="card-img-top" src="https://placeimg.com/318/180/<?php echo $what; ?>/<?php echo $img; ?>" alt="Card image cap">
                        <div class="card-block">
                            <h4 class="card-title"><?php echo $host; ?></h4>
                            <a href="http://<?php echo $currentSections['servername']; ?>" class="btn btn-primary" target="_blank">Open</a>
                            <a href="#<?php echo md5($host); ?>" class="btn btn-info js-showForm">Update</a>
                            <a href="#<?php echo md5($host); ?>" class="btn btn-danger js-remove">x</a>
                        </div>
                    </div>
                    <div id="<?php echo md5($host); ?>" class="modal fade real-content" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">

                                <form role="form">
                                    <div class="modal-header">
                                        <h4><?php echo $host; ?></h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">

                                            <label for="exampleInputEmail1">
                                                Host:
                                            </label>
                                            <input type="text" class="form-control" id="hostname" name="hostname" value="<?php echo trim($host); ?>"/>
                                        </div>
                                        <?php
                                        foreach ($sections as $section => $placeholder) {
                                            $placeholderHtml = '';
                                            if ($placeholder != '') {
                                                $placeholderHtml = 'placeholder="' . $placeholder . '"';
                                            }
                                            ?>
                                            <div class="form-group">

                                                <label for="<?php echo md5($host) . '-' . trim(strtolower($section)); ?>">
                                                    <?php echo $section; ?>
                                                </label>
                                                <input type="text" class="form-control" name="<?php echo trim($section); ?>" <?php echo $placeholderHtml; ?> id="<?php echo md5($host) . '-' . trim(strtolower($section)); ?>" value="<?php echo $currentSections[trim(strtolower($section))]; ?>"/>
                                            </div>
                                            <?php
                                        };
                                        ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary save-changes">Save changes</button>
                                    </div>
                                </form>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <?php
                }
            };

            $tmp = rand(0, 2);
            $array = ['nature', 'tech', 'arch'];
            $what = $array[$tmp];
            ?>
            <div class="card" style="width: 20rem; float:left; margin:10px;">
                <img class="card-img-top" src="http://placehold.it/318x180?text=New+VHost" alt="Card image cap">
                <div class="card-block">
                    <h4 class="card-title">New VHost</h4>
                    <a href="#newwebsite" class="btn btn-info js-showForm">Create</a>
                </div>
            </div>
            <div id="newwebsite" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">

                        <form role="form" id="addHost">
                            <div class="modal-header">
                                <h4>New VHost</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">

                                    <label for="exampleInputEmail1">
                                        Host:
                                    </label>
                                    <input type="text" class="form-control" id="hostname" name="hostname" value="" placeholder="Name Of The Host"/>
                                </div>
                                <?php
                                foreach ($sections as $section => $placeholder) {
                                    if ($placeholder != '') {
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
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success"> + Create</button>
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="/js/main.js"></script>
</body>
</html>