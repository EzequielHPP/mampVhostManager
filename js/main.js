/**
 * Created by ezequielpereira on 08/03/2017.
 */
$(document).ready(function () {
    $('li.showVHost').click(function () {
        var virtual = $(this).attr('data-host');
        $('form[role="form"]').hide();
        $('#' + virtual).show();
    });

    $('#newwebsite button[type="submit"]').click(function (e) {
        e.preventDefault();
        createNewEntry();
    });

    $('.save-changes').each(function(){
        $(this).click(function (e) {
            e.preventDefault();
            updateAllvHosts();
        });
    });

    $('.js-showForm').each(function () {
        $(this).click(function (e) {
            e.preventDefault();
            var id = $(this).attr('href');
            $(id).modal('show');
        });

    });

    $('.js-remove').each(function () {
        $(this).click(function (e) {
            e.preventDefault();

            if(confirm('Do you want to remove the vhost?')) {
                var id = $(this).attr('href');
                $(id).remove();
                $(this).parents('.card').remove();

                updateAllvHosts();
            }
        });

    });
});

function updateAllvHosts(){
    var json = buildJson();
    var data = {
        "content": JSON.stringify(json)
    };

    console.log(data);

    $.ajax({
        type: "POST",
        url: '/actions.php?updatehosts=true',
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
}


function refresh() {
    window.location = window.location;
}

function createNewEntry(){
    var json = buildJson(true);
    var data = {
        "content": JSON.stringify(json)
    };

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
}

function buildJson(justNewHost) {
    if (justNewHost === undefined) {
        justNewHost = false;
    }

    var parent = '.real-content';
    var json = [];
    if(justNewHost){
        parent = '#newwebsite';
        json = {};
    }
    $(parent).each(function(){
        var tmpvhost = {};
        $(this).find('input').each(function(){
            if($(this).val() != ''){
                tmpvhost[$(this).attr('name')] = $(this).val();
            }
        });

        if(!justNewHost && tmpvhost['hostname'] != undefined){
            json.push(tmpvhost);
        } else if(justNewHost && tmpvhost['hostname'] != undefined){
            json = tmpvhost;
        }
    });

    return json;
}