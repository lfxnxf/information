var baseUrl = 'http://information_interface.com/api/admin/';

function ajaxPost(url, data, func) {
    var token = localStorage.getItem('token');
    var headers = {};
    if (token) {
        headers.token = token;
    }
    $.ajax({
        url: url,
        headers: headers,
        data: data,
        dataType: 'JSON',
        type: 'POST',
        success: function(msg) {
            if (msg.error == 100001) {
                alert('请登录!');
                window.location.href = 'index.html';
            }else if (msg.error != 0) {
                alert(msg.err_msg);
                return false;
            }else{
                func(msg);
            }
        }
    })
}