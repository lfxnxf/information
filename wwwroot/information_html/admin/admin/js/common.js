var baseUrl = 'http://information_interface.com/api/admin/';

$(function () {
    var local = location.href.split('/');
    var html = local[local.length - 1];
    if (html != 'login.html') {
        getMenu(html)
    }
});

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
        contentType: false,
        processData: false,
        success: function (msg) {
            if (msg.error == 100001) {
                alert('请登录!');
                window.location.href = 'login.html';
            } else if (msg.error == 100002) {
                alert(msg.err_msg);
                //todo 跳转到一个新页面，展示没有权限logo
                //history.go(-1);location.reload();
                return false;
            } else if (msg.error != 0) {
                alert(msg.err_msg);
                return false;
            } else {
                func(msg.data);
            }
        }
    })
}

function getMenu(htmlName) {
    var url = baseUrl + 'getMenu';
    ajaxPost(url, {}, function (data) {
        $('#menu').html('');
        var html = '';
        for (var i = 0; i < data.length; i++) {
            html += '<li class="ydc-menu-item">' +
            '           <span class="ydc-menu-sub-title">'+ data[i]['title'] +'</span><ul>';
            if (data[i]['child']) {
                for (var j = 0; j<data[i]['child'].length; j++) {
                    html +=  '<li><a href="' + data[i]['child'][j]['url'] + '" ';
                    if (data[i]['child'][j]['url'] == htmlName) {
                        html += 'class = "active"';
                    }
                    html += '>' + data[i]['child'][j]['title'] + '</a>' +
                        '</li>';
                }
            }
            html += '</ul></li>';
        }
        $('#menu').html(html);
    });
}