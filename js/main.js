/**
 * Created by ezequielpereira on 08/03/2017.
 */
$(document).ready(function () {
    $('li.showVHost').click(function () {
        var virtual = $(this).attr('data-host');
        $('form[role="form"]').hide();
        $('#' + virtual).show();
    });

    $('button[type="submit"]').click(function (e) {
        e.preventDefault();
        var json = buildJson(true);
        if ($(this).parents('form').attr('id') == 'addHost') {
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

    function buildJson(justNewHost) {
        if (justNewHost === undefined) {
            justNewHost = false;
        }
        var json = {};
        $('form[role="form"]').each(function () {
            var tmpId = $(this).attr('id');
            if (!justNewHost || (justNewHost == true && tmpId == 'addHost')) {
                json[tmpId] = {};
                $(this).find('input').each(function () {
                    json[tmpId][$(this).attr('name')] = $(this).val();
                });
            }
        });

        return json;
    }

    $('.js-showForm').each(function () {
        var id = $(this).attr('href');
        $(this).click(function (e) {
            e.preventDefault();
            var id = $(this).attr('href');
            $(id).modal('show');
        });

    });

    function refresh() {
        window.location = window.location;
    }
});