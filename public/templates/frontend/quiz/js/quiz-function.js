var _timeOut = 10000;
function quizFighting(userId) {
    $('#system-modal').on('show.bs.modal', function () {
        setModalTitleName('Thách đấu');
        hiddenCloseButton();
        //hiddenSubmitButton();
        loadingSubmitButton('Fighting');
        hiddenModalBody();
    });
    $('#system-modal').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');
    var jqxhr = $.post(_baseUri + '/quiz/cm/' + userId + '/', function (data) {
    }).done(function (data) {
        if (data && data.code == 0) {
            setModalBody(data.msg);
            var time = 5;
            setInterval(function () {
                time--;
                if (time == 0) {
                    window.location.href = _baseUri + '/quiz/fight/' + data.data + '/';
                } else {
                    resetSubmitButton('Trận đấu sẽ được bắt đầu trong ' + dTime + ' giây!');
                    var dTime = parseInt(time) > 0 ? parseInt(time) : 0;
                    setModalBody('Trận đấu đã được khởi tạo, bạn sẽ được chuyển đến đấu trường trong <strong>' + dTime + ' giây!</strong>');
                    loadingSubmitButton('Trận đấu sẽ được bắt đầu trong ' + dTime + ' giây!');
                }
            }, 1000);

        } else {
            setModalBody(data.msg);
            hiddenSubmitButton();
            resetCloseButton();
        }
    }).fail(function () {
    }).always(function () {
    });
}
function setModalTitleName(str) {
    $('#system-modal .modal-title').html(str);
}
function setSubmitButtonName(str) {
    $('#system-modal .btn-submit').html(str);
}
function hiddenCloseButton() {
    $('#system-modal .btn-close').css('display', 'none');
}
function resetCloseButton(text) {
    if (text) {
        $('#system-modal .btn-close').html(text).removeClass('disable').css('display', 'inline-block');
    } else {
        $('#system-modal .btn-close').css('display', 'inline-block');
    }
}
function hiddenModalBody() {
    $('#system-modal .modal-body').css('display', 'none');
}
function setModalBody(text) {
    if (text) {
        $('#system-modal .modal-body').html(text).css('display', 'block');
    } else {
        $('#system-modal .modal-body').css('display', 'block');
    }
}
function hiddenSubmitButton() {
    $('#system-modal .btn-submit').css('display', 'none');
}
function disableSubmitButton() {
    $('#system-modal .btn-submit').addClass('disabled');
}
function loadingSubmitButton(text) {
    $('#system-modal .btn-submit').html('<i class="fa fa-refresh fa-spin"></i> ' + text).addClass('disabled');
}
function resetSubmitButton(text) {
    $('#system-modal .btn-submit').html(text).removeClass('disabled').css('display', 'inline-block');
}
function hiddenModalFooter() {
    $('#system-modal .modal-footer').css('display', 'none');
}
String.prototype.toHHMMSS = function () {
    var sec_num = parseInt(this, 10); // don't forget the second param
    var hours = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours < 10) {
        if (hours == 0) {
            hours = '';
        } else {
            hours = '0' + hours + ':';
        }

    }
    if (minutes < 10) {
        minutes = '0' + minutes + ':';
    }
    if (seconds < 10) {
        seconds = '0' + seconds;
    }
    return hours + minutes + seconds;
};
function countDown(time, timeLoading, fightCode) {
    time = time * 1000;
    var totalTime = time;
    var timePost = Date.now() + time;

    $('#time-remaining').attr('aria-valuemax', time).attr('aria-remaining', time);
    var refreshIntervalId = setInterval(function () {
        if (!_fightIsStop) {
            var timeRemaining = (time * 100 / totalTime).toFixed(2);
            var timeRemainingInt = Math.round(time / 1000);
            //var timeRemainingStr = timeRemainingInt + ' s';
            var timeRemainingStr = timeRemainingInt + ' s';
            $('#time-remaining').css('width', timeRemaining + '%').attr('aria-remaining', Math.round(time / 1000));
            if (timeRemainingInt <= 10) {
                $('#fight-timeout').html('<span style="color: red;">' + timeRemainingStr.toHHMMSS() + '</span>');
            } else if (timeRemainingInt <= 30) {
                $('#fight-timeout').html('<span style="color: orange;">' + timeRemainingStr.toHHMMSS() + '</span>');
            } else {
                $('#fight-timeout').html('<span style="color: #1ABD9B;">' + timeRemainingStr.toHHMMSS() + '</span>');
            }
            if (Date.now() > timePost) {
                time = 0;
            } else {
                time -= timeLoading;
            }
            if (time % 10000 == 0 && time + 10000 < totalTime && time > 10000) {
                //if (time % 10200 == 0) {
                sendFightData(fightCode, 0);
            }
            if (time <= 0) {
                sendFightData(fightCode, 1);
                clearInterval(refreshIntervalId);
                $('#time-remaining').css('width', '0%').attr('aria-remaining', '0');
                $('#fight-timeout').html('<span style="color: red;">0 s</span>');
                //questionList = [];
            }
        }
    }, timeLoading);
}
function sendFightData(fightCode, isSubmit) {
    var data = [];
    for (var i in questionList) {
        data.push({
            id: questionList[i].id,
            answer: questionList[i].answer,
            mark: questionList[i].mark
        });
    }
    $.post(_baseUri + '/quiz/fight/sm/' + fightCode + '/', {data: JSON.stringify(data), is_submit: isSubmit}, function (data) {

        if (typeof data == 'object') {
            if (data.code == 0) {
                data = data.data;
                if (!data.challenger_score && data.challenger_score !== 0) {
                    data.challenger_score = '?'
                }
                if (!data.re_challenger_score && data.re_challenger_score !== 0) {
                    data.re_challenger_score = '?'
                }
                $('.fight-vs .fight-score').html(data.challenger_score + ' : ' + data.re_challenger_score);
                $('#fight-submit').modal('hide');
                _fightIsStop = true;
                $('#time-remaining').css('width', '0%').attr('aria-remaining', '0');
                $('#fight-timeout').html('<span style="color: red;">0 s</span>');
                questionList = [];
                quiz.cleanForm();
                if (isSubmit == 1) {
                    _isStart = false;
                }
            } else {
                alert('Không thể kết nối đến Internet. Vui lòng tải lại trang!');
            }
        }
    });
}
function loadFriends() {
    if (_isLogin) {
        $.getJSON(_baseUri + '/quiz/getFriends/', function (data) {
            if (data.code == 0) {
                data = data.data;
                var html = '';
                for (var i in data) {
                    _friendList['user_' + parseInt(data[i].uid)] = data[i];
                    if (data[i].avatar.indexOf('http://') == -1 && data[i].avatar.indexOf('https://') == -1) {
                        data[i].avatar = _baseUri + data[i].avatar;
                    }
                    html += '<li class="direct-user-uid direct-user-uid-' + data[i].uid + '"><div class="pull-left user-left"><img width="32px" height="32px" src="' + data[i].avatar + '" alt="' + data[i].display_name + '" data-id="">' + data[i].display_name + '</div><div class="pull-right"><a class="btn btn-warning btn-sm" href="#" title="Thách đấu" data-toggle="tooltip" data-placement="bottom" onclick="quizFighting(' + data[i].uid + ')"><i class="fa fa-flash"></i></a></div></li>';
                }
                $('#user-friends .box-body ul').html(html);
                $('#user-friends .overlay').css('display', 'none');
                $('#user-friends .box-tools span').html(data.length);
                getAllUserInfoInHome();
            }
        });
    }
}

function buildNotification(items) {
    var html = '';
    if (items.length) {
        for (var i in items) {
            html += '<li class="notification-item"><a href="' + items[i].link + '" class="notification-link"><div class="notification-item"><div class="notification-item-image">';
            html += '<img src="' + items[i].image + '" alt="" width="50" height="50"></div><div>';
            html += '<div>' + items[i].content + '</div>';
            html += '<span class="timeago">' + jQuery.timeago(items[i].created_at);  + '</span>';
            html += '</div></div></a></li>';
        }
        html += '<li class="text-center"><a href="#' + _baseUri + '/user/notifications/">Xem tất cả</a></li>';
    } else {
        html = '<li style="padding: 15px" class="text-center notifications-empty"><a href="#">Bạn chưa có thông báo!</a></li>'
    }
    return html;
}

function buildUserInfoToolTip(user) {
    if (user) {
        var html =
            '<div class="user-tooltip"><div class="ut-info"><div class="ut-user-avatar pull-left">' +
            '<a href="#"><img width="64px" height="64px" src="' + user.avatar + '"></a>' +
            '</div><div class="ut-info-text">' +
            '<div class="ut-display-name">' + user.display_name + '</div>' +
            '<div class="ut-user-info">' +
            '<span>Level: ' + user.level + ' | Coin: ' + user.coin + '</span>' +
            '</div></div>' +
            '<div class="ut-fight" data-id="' + user.uid + '"><a onclick="quizFighting(' + user.uid + ')" title="Thách đấu" data-toggle="tooltip" data-placement="bottom" href="#"><i class="fa fa-flash"></i></a></div>' +
            '<div class="clearfix"></div>' +
            '</div>' +
            '<div class="clearfix"></div>' +
            '<div class="ut-add-friend text-center">';
        if (_friendList.hasOwnProperty('user_' + user.uid)) {
            html += '<a href="javascript:void(0)"><i class="fa fa-user"> Bạn bè</i></a>';
        } else {
            if (user.is_request) {
                html += '<a href="javascript:void(0)" class="btn-waitting-friend" data-uid="' + user.uid + '"><i class="fa fa-hourglass-start"> Đang chờ</i></a>';
            } else {
                html += '<a href="javascript:void(0)" class="btn-add-friend" data-uid="' + user.uid + '"><i class="fa fa-user-plus"> Kết bạn</i></a>';
            }
        }
        html += '</div>' +
            '</div>';
        return html;
    }
    return '';
}

function userInfoToolTip(userId) {
    if (_friendList.hasOwnProperty('user_' + userId)) {
        return buildUserInfoToolTip(_friendList['user_' + userId]);
    } else if (_userInfoList.hasOwnProperty('user_' + userId)) {
        return buildUserInfoToolTip(_userInfoList['user_' + userId]);
    }
}

function getAllUserInfoInHome() {
    var userIds = [];
    $('.qt-user').each(function () {
        var userIdTmp = $(this).attr('data-uid');
        if (!_userInfoList.hasOwnProperty('user_' + userIdTmp)) {
            userIds.push(userIdTmp);
        }
    });
    $.post(_baseUri + '/quiz/getUsersInfo/', {userIds: userIds}, function (data, status, response) {
        if (response.status == 200) {
            data = data.data;
            for (var i in data) {
                _userInfoList['user_' + parseInt(data[i].uid)] = data[i];
            }
            $('.qt-user').each(function () {
                $(this).qtip({
                    content: {
                        text: userInfoToolTip($(this).attr('data-uid'))
                    },
                    hide: {
                        fixed: true,
                        delay: 700
                    },
                    //hide: false,
                    position: {
                        viewport: $(window),
                        my: 'top left',
                        at: 'bottom center'
                    }
                });
            });
        }
    });
}

function loadNotification() {
    if (_isLogin) {
        $.getJSON(_baseUri + '/user/getNotifications/', function (data) {
            if (data && data.code == '0') {
                if (data.data.unRead > 0) {
                    if (data.data.unRead > 100) {
                        data.data.unRead = '99+'
                    }
                    $('.btn-header-extra-notification').html(data.data.unRead).css('display', 'block');
                } else {
                    $('.btn-header-extra-notification').css('display', 'none');
                }
                $('#notification-items').html(buildNotification(data.data.notifications));
                $("#notification-items .timeago").timeago();
            }
        });
    }
}

function addFriend(userId) {
    if (_isLogin) {
        $.post(_baseUri + '/user/addFriend/', {userId: userId}, function (data, status, response) {
            if (data == '1') {
                $.notify('Đã gửi lời mời kết bạn thành công!', 'success');
            } else if (data == '0') {
                //Show error
            } else {
                $.notify(data, 'warning');
            }
        });
    }
}

function removeFriend(userId) {
    if (_isLogin) {
        $.post(_baseUri + '/user/unFriend/', {userId: userId}, function (data, status, response) {
            if (data == '1') {
                $('.ffuid-' + userId).remove();
                $.notify('Bạn đã hủy kết bạn thành công!', 'success');
            } else {
                $.notify('Bạn đã hủy kết bạn thất bại!', 'warning');
            }
        });
    }
}

function renderSearchDirectUserFriends(keyword) {
    if (keyword.length == 0) {
        $('.direct-user-uid').css('display', 'block');
        return;
    }
    for (var key in _friendList) {
        if (_friendList[key].display_name.toLowerCase().indexOf(keyword) == -1) {
            $('.direct-user-uid-' + _friendList[key].uid).css('display', 'none');
        } else {
            $('.direct-user-uid-' + _friendList[key].uid).css('display', 'block');
        }
    }
}

$(function () {
    jQuery.timeago.settings.strings = {
        prefixAgo: null,
        prefixFromNow: null,
        suffixAgo: "",
        suffixFromNow: "",
        seconds: "1m",
        minute: "1m",
        minutes: "%dm",
        hour: "1h",
        hours: "%dh",
        day: "1 day",
        days: "%d days",
        month: "1 month",
        months: "%d month",
        year: "1yr",
        years: "%dyr",
        wordSeparator: " ",
        numbers: []
    };
    $(".user-timeago").timeago();
    jQuery.timeago.settings.strings = {
        prefixAgo: null,
        prefixFromNow: null,
        suffixAgo: "ago",
        suffixFromNow: "from now",
        seconds: "less than a minute",
        minute: "a minute",
        minutes: "%d minutes",
        hour: "an hour",
        hours: "%d hours",
        day: "a day",
        days: "%d days",
        month: "a month",
        months: "%d months",
        year: "a year",
        years: "%d years",
        wordSeparator: " ",
        numbers: []
    };
    $(".timeago").timeago();
    setTimeout(function () {
        loadFriends();
        loadNotification();
    }, 1500);
    $('body').on('click', '.btn-add-friend', function () {
        addFriend($(this).attr('data-uid'));
        $(this).removeClass('btn-add-friend');
        $(this).html('<i class="fa fa-user-plus"></i> Đã yêu cầu').attr('disabled', 'disabled');
    });
    $('.btn-un-friend').click(function () {
        var userName = $(this).parent().find('span').html();
        var userId = $(this).attr('data-uid');
        $('#fight-friend .btn.btn-warning').attr('data-uid', userId);
        $('#fight-friend').on('show.bs.modal', function () {
            $('#fight-friend .modal-body').html('Bạn có chắc chặn muốn hủy kết bạn với <strong>' + userName + '</strong>?');
        });
        $('#fight-friend').modal('show');
    });

    $('#fight-un-friend').click(function () {
        removeFriend($('#fight-friend .btn.btn-warning').attr('data-uid'));
        $('#fight-friend').modal('hide');
    });

    $('.btn-accept-friend').click(function () {
        var parent = $(this).parent();
        $.post(_baseUri + '/user/acceptFriend/', {userId: $(this).attr('data-uid')}, function (data, status, response) {
            if (data == '1') {
                $.notify('Kết bạn thành công', 'success');
                parent.remove();
            } else if (data == '0') {
                //Show error
                return false;
            } else {
                $.notify(data, 'warning');
                return false;
            }
        });
    });
    $('input#direct-user-search').keyup(function () {
        renderSearchDirectUserFriends($(this).val());
    });
    $(".btn-notification-item").click(function () {
//            $(this).toggleClass("open");
        $("#notification-items").fadeToggle(300);
        $('.btn-header-extra-notification').css('display', 'none');
        $.getJSON(_baseUri + '/user/irn/', function (data) {
        });
//            $("#notification-items").toggleClass("notification-items-open");
    });
    $('body').click(function (event) {
        var target = $(event.target);
        if (!target.hasClass('fa-notifications')) {
            $("#notification-items").slideUp('fast');
//                $("#notification-items").removeClass("notification-items-open");
        }
    });

    setInterval(function () {
        loadNotification()
    }, _timeOut);
});