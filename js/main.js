/**
 * Created by ezequielpereira on 08/03/2017.
 */
$(document).ready(function () {
    $('li.showVHost').click(function () {
        let virtual = $(this).attr('data-host');
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
            // updateAllvHosts();
            let data = {
                "content": $(this).parents('form').serialize()
            };

            $.ajax({
                type: "POST",
                url: '/actions.php?updatehosts=true',
                data: data,
                success: function (response) {
                    let responseDetails = JSON.parse(response);
                    if (responseDetails.status === 'success') {
                        // restart();
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
        });
    });

    $('.js-showForm').each(function () {
        $(this).click(function (e) {
            e.preventDefault();
            let id = $(this).attr('href');
            $(id).modal('show');
        });

    });

    $('.js-remove').each(function () {
        $(this).click(function (e) {
            e.preventDefault();

            if(confirm('Do you want to remove the vhost?')) {
                let id = $(this).attr('href');
                $(id).remove();
                $(this).parents('.card').remove();

                updateAllvHosts();
            }
        });

    });
});

function updateAllvHosts(){
    let json = buildJson();
    let data = {
        "content": JSON.stringify(json)
    };

    $.ajax({
        type: "POST",
        url: '/actions.php?updatehosts=true&force=true',
        data: data,
        success: function (response) {
            let responseDetails = JSON.parse(response);
            if (responseDetails.status === 'success') {
                // restart();
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

function restart(){
    $.ajax({
        type: "GET",
        url: '/actions.php?restart=true',
        success: function (response) {

        },
        fail: function (response) {

        }
    });
}


function refresh() {
    location.reload();
}

function createNewEntry(){
    let json = buildJson(true);
    let data = {
        "content": JSON.stringify(json)
    };

    $.ajax({
        type: "POST",
        url: '/actions.php?addenewhost=true',
        data: data,
        success: function (response) {
            let responseDetails = JSON.parse(response);
            console.log('success');
            if (responseDetails.status === 'success') {
            console.log('refresh');
                refresh();
            } else {
            console.log('alert');
                alert(responseDetails.message);
            }
        },
        fail: function (response) {
            console.log('fail');
            console.log(response);
            alert('Couldn\'t save the host at the moment. ' + response.message);
        }
    });
}

function buildJson(justNewHost) {
    if (justNewHost === undefined) {
        justNewHost = false;
    }

    let parent = '.real-content';
    let json = [];
    if(justNewHost){
        parent = '#addHost';
        json = {};
    }
    $(parent).each(function(){
        let tmpvhost = {};
        $(this).find('input').each(function(){
            if($(this).val() !== ''){
                tmpvhost[$(this).attr('name')] = $(this).val();
            }
        });
        $(this).find('select').each(function(){
            if($(this).find(":selected").val() !== ''){
                tmpvhost[$(this).attr('name')] = $(this).find(":selected").val();
            }
        });

        if(!justNewHost && tmpvhost['hostname'] !== undefined){
            json.push(tmpvhost);
        } else if(justNewHost && tmpvhost['hostname'] !== undefined){
            json = tmpvhost;
        }
    });

    return json;
}