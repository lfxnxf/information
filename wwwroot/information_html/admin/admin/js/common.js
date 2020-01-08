var baseUrl = 'http://information_interface.com/api/admin/';

function ajaxPost(url, data, func) {
    $.ajax({
        url: url,
        data: data,
        dataType: 'JSON',
        type: 'POST',
        success: function(msg) {
            func(msg);
        }
    })
}