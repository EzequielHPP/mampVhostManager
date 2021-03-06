<?php
$hosts = array();

$vhostsLocation = '/Applications/MAMP/conf/apache/extra/';
$docroot = '/Applications/MAMP/htdocs/';
$vhostsFileName = 'httpd-vhosts.conf';

$categories = ['nature', 'tech', 'arch', 'animals', 'people'];
$categoriesNames = ['Nature', 'Technology', 'Architecture', 'Animals', 'People'];

copy($vhostsLocation . $vhostsFileName, __DIR__ . '/files/' . $vhostsFileName . '.bkp');

$hosts = json_decode(file_get_contents('hosts.json'));

$changed = false;
foreach ($hosts as $index => $host) {
    if ($host->id == "") {
        $changed = true;
        $hosts[$index]->id = md5($host->title . $host->url);
    }
}
if ($changed) {
    file_put_contents('hosts.json', json_encode($hosts));
}

$sections = array(
    'location ' => 'Root directory of website',
    'url ' => 'urlhere.com'
);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MAMP Virtual Host manager</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

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
            foreach ($hosts as $tmpData) {
                if ($tmpData->title != '') {

                    $img = 'sepia';

                    $tmp = rand(0, sizeof($categories) - 1);
                    if (in_array($tmpData->category, $categories)) {
                        $what = $tmpData->category;
                    } else {
                        $what = $categories[$tmp];
                    }

                    $rand = rand(20, 60);
                    $diff = 180 + ($rand - 20);


                    ?>
                    <div class="card" style="width: 20rem; float:left; margin:10px;">
                        <div style="width:318px; height:180px; overflow: hidden;">
                            <img class="card-img-top"
                                 src="https://placeimg.com/3<?php echo $rand; ?>/<?php echo $diff; ?>/<?php echo $what; ?>/<?php echo $img; ?>"
                                 alt="Card image cap">
                        </div>
                        <div class="card-block">
                            <h4 class="card-title"><?php echo $tmpData->title; ?></h4>
                            <a href="http://<?php echo $tmpData->url; ?>" class="btn btn-primary"
                               target="_blank">Open</a>
                            <a href="#<?php echo $tmpData->id; ?>"
                               class="btn btn-info js-showForm">Update</a>
                            <a href="#<?php echo $tmpData->id; ?>" class="btn btn-danger js-remove">x</a>
                        </div>
                    </div>
                    <div id="<?php echo $tmpData->id; ?>" class="modal fade real-content" tabindex="-1"
                         role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">

                                <form role="form">
                                    <input type="hidden" name="id" value="<?php echo $tmpData->id; ?>">
                                    <div class="modal-header">
                                        <h4><?php echo $tmpData->title; ?></h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">

                                            <label for="hostname">
                                                Host:
                                            </label>
                                            <input type="text" class="form-control" id="hostname" name="hostname"
                                                   value="<?php echo trim($tmpData->title); ?>"/>
                                        </div>
                                        <div class="form-group">

                                            <label for="category">
                                                Category:
                                            </label>
                                            <select id="category" name="category">
                                                <?php
                                                foreach ($categories as $index => $value) {
                                                    ?>
                                                    <option value="<?php echo $value; ?>" <?php if ($tmpData->category == $value) {
                                                        echo 'selected="selected"';
                                                    }; ?>><?php echo $categoriesNames[$index]; ?></option>
                                                    <?php
                                                };
                                                ?>
                                            </select>
                                        </div>
                                        <!-- Document Root -->
                                        <div class="form-group">

                                            <label for="<?php echo $tmpData->id . '-location'; ?>">
                                                Document Root
                                            </label>
                                            <input type="text" class="form-control" name="location"
                                                   placeholder="/Applications/MAMP/htdocs/"
                                                   id="<?php echo $tmpData->id . '-location'; ?>"
                                                   value="<?php echo ($tmpData->location == '') ? '/Applications/MAMP/htdocs' : $tmpData->location; ?>"/>
                                        </div>
                                        <!-- Server Name -->
                                        <div class="form-group">

                                            <label for="<?php echo $tmpData->id . '-url'; ?>">
                                                Server URL
                                            </label>
                                            <input type="text" class="form-control" name="url"
                                                   placeholder="something.com"
                                                   id="<?php echo $tmpData->id . '-url'; ?>"
                                                   value="<?php echo $tmpData->url; ?>"/>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close
                                        </button>
                                        <button type="submit" class="btn btn-primary save-changes">Save changes</button>
                                    </div>
                                </form>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <?php
                };// If
            };// foreach

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
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">

                                    <label for="exampleInputEmail1">
                                        Title:
                                    </label>
                                    <input type="text" class="form-control" id="hostname" name="hostname" value=""
                                           placeholder="Name Of The Host"/>
                                </div>
                                <div class="form-group">

                                    <label for="category">
                                        Category:
                                    </label>
                                    <select name="category">
                                        <option value="arch">Architecture</option>
                                        <option value="tech">Technology</option>
                                        <option value="nature">Nature</option>
                                    </select>
                                </div>
                                <?php
                                foreach ($sections as $section => $placeholder) {
                                    if ($placeholder != '') {
                                        ?>
                                        <div class="form-group">

                                            <label for="<?php echo 'addHost-' . trim(strtolower($section)); ?>">
                                                <?php echo $section; ?>
                                            </label>
                                            <input type="text" class="form-control"
                                                   name="<?php echo trim($section); ?>"
                                                   id="<?php 'addHost-' . trim(strtolower($section)); ?>"
                                                   placeholder="<?php echo $placeholder; ?>" value=""/>
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
<script src="https://code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"
        integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
        integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn"
        crossorigin="anonymous"></script>
<script src="/js/main.js"></script>
</body>
</html>