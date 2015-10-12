var quiz = quiz || {};
var questionList = questionList || [];
var currentQuestionIndex = 0;
var previousQuestionIndex = 0;
var currentQuestion = {};

$(function () {
    quiz.opt = {
        question_title: $('#quiz-question-title'),
        question_content: $('#quiz-question-content'),
        extra_content: $('#quiz-extra-content'),
        answer_content: $('#quiz-answer-content'),
        question_list: $('#question-list')
    }
});

function updateOldAnswer(questionList, answer) {
    if (answer) {
        for (var index in questionList) {
            if (answer.hasOwnProperty(index)) {
                if (answer[index].hasOwnProperty('answer')) {
                    questionList[index].answer = answer[index].answer;
                }
                if (answer[index].hasOwnProperty('mark')) {
                    questionList[index].mark = answer[index].mark;
                }
            }
        }
    }
    return questionList;
}

quiz.load = function (fightCode) {
    //Load question list from ajax
    $.getJSON(_baseUri + '/quiz/getQuiz/' + fightCode + '/', {format: 'json'}, function (data) {
        if (data) {
            var answer = [];
            _isStart = true;
            questionList = JSON.parse(data.data.questions_content);

            if (data.data.hasOwnProperty('answer')) {
                answer = JSON.parse(data.data.answer);
                questionList = updateOldAnswer(questionList, answer);
            }

            var index = 1;
            questionList.forEach(function (q) {
                var cls = 'btn-info';
                if (q.answer != '') {
                    cls = 'btn-success';
                }
                if (q.mark === true) {
                    cls = 'btn-danger';
                }
                quiz.opt.question_list.append('<li><button onclick="quiz.setQuestion(' + (index - 1) + ')" class="btn ' + cls + '" id="button' + (index) + '">' + index + '</button> </li>');
                index++;
            });

            currentQuestionIndex = 0;
            quiz.setQuestion(currentQuestionIndex);
            countDown(parseInt(data.data.total_exam_time), 100, fightCode);
        } else {
            /**
             * Todo
             * Alert load quiz error
             */
        }
    });
};

quiz.next = function () {
    //quiz.save();
    if (currentQuestionIndex < (questionList.length - 1)) {
        currentQuestionIndex++;
    }
    else {
        return;
    }
    quiz.setQuestion(currentQuestionIndex);
};

quiz.previous = function () {
    //quiz.save(currentQuestion);
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
    }
    else {
        return;
    }
    quiz.setQuestion(currentQuestionIndex);
};

quiz.setQuestion = function (index) {
    currentQuestionIndex = index;
    quiz.updateButtonStatus(previousQuestionIndex);
    currentQuestion = questionList[index];
    previousQuestionIndex = index;

    var q = currentQuestion;
    quiz.toggleButton(q);
    quiz.opt.question_title[0].innerHTML = 'Question ' + (currentQuestionIndex + 1) + ': <span>' + q.question_title + '</span>';
    //quiz.opt.question_content[0].innerHTML = q.question_title;

    if (q.extra != undefined && q.extra != '') {
        quiz.opt.extra_content.show();
        quiz.opt.extra_content[0].innerHTML = q.extra;
    }
    else {
        quiz.opt.extra_content.hide();
    }

    if (q.type == 'writing') {
        quiz.writing(q);
    } else if (q.type == 'drag_drop_paragraphs') {
        quiz.dragDropParagraphs(q);
    } else if (q.type == 'drag_drop_dialogues') {
        quiz.dragDropDialogues(q);
    } else if (q.type == 'single_choice') {
        quiz.singleChoice(q);
    } else if (q.type == 'multi_choice') {
        quiz.multiChoice(q);
    } else if (q.type == 'select_box') {
        quiz.selectBox(q);
    } else if (q.type == 'fill_to_input') {
        quiz.fillToInput(q);
    }
};

quiz.toggleButton = function (q) {
    //Toggle button previous,next
    if (currentQuestionIndex <= 0) {
        $('.btn-previous').attr('onclick', '').removeClass('btn-primary').addClass('btn-block');
        $('.btn-next').attr('onclick', 'quiz.next()').removeClass('btn-block').addClass('btn-primary');
    } else if (currentQuestionIndex >= (questionList.length - 1)) {
        $('.btn-next').attr('onclick', '').removeClass('btn-primary').addClass('btn-block');
        $('.btn-previous').attr('onclick', 'quiz.previous()').removeClass('btn-block').addClass('btn-primary');
    } else {
        $('.btn-previous').attr('onclick', 'quiz.previous()').removeClass('btn-block').addClass('btn-primary');
        $('.btn-next').attr('onclick', 'quiz.next()').removeClass('btn-block').addClass('btn-primary');
    }

    //Toggle button mark
    if (q.mark) {
        $('.btn-mark').removeClass('btn-warning').addClass('btn-danger');
    } else {
        $('.btn-mark').removeClass('btn-danger').addClass('btn-warning');
    }
};

quiz.checkMark = function () {
    currentQuestion.mark = !currentQuestion.mark;
    quiz.updateButtonStatus(currentQuestionIndex);

    var btn_mark = $('.btn-mark');
    if (btn_mark.className = 'btn btn-info pull-left') {
        btn_mark.toggleClass('btn-warning btn-danger');
    } else {
        btn_mark.toggleClass('btn-danger btn-warning');
    }
};

quiz.updateButtonStatus = function (i) {
    var cls = 'btn-info';
    var q = questionList[i];

    if (q == undefined) return;

    if (q.answer != undefined && q.answer != '') {
        cls = 'btn-success';
    }
    if (q.mark === true) {
        cls = 'btn-danger';
    }
    $('#button' + (i + 1)).removeClass().addClass('btn').addClass(cls);
};

quiz.save = function (fightCode) {
    sendFightData(fightCode, 1);
};

quiz.fightScore = function (data) {
    console.log(data);
    var challengerHTML = '<div>';
    if (data.challenger_avatar.indexOf('http') == -1) {
        data.challenger_avatar = _baseUri + data.challenger_avatar
    }
    data.challenger_score_text = data.challenger_score == null ? '?' : data.challenger_score;
    data.re_challenger_score_text = data.re_challenger_score == null ? '?' : data.re_challenger_score;
    challengerHTML += '<img width="64px" height="64px" src="' + data.challenger_avatar + '">';
    challengerHTML += '<div class="fight-score-u1" data-percent="' + parseInt(data.challenger_score * 100 / data.score) + '">' + data.challenger_score_text + '</div>';
    challengerHTML += '<div id="fight-score-name1"><span>' + data.challenger_name + '</span></div>';
    challengerHTML += '</div>';
    $('#fight-score-user1').append(challengerHTML);

    challengerHTML = '<div>';
    if (data.re_challenger_avatar.indexOf('http') == -1) {
        data.re_challenger_avatar = _baseUri + data.re_challenger_avatar
    }
    challengerHTML += '<img width="64px" height="64px" src="' + data.re_challenger_avatar + '">';
    challengerHTML += '<div class="fight-score-u2" data-percent="' + parseInt(data.re_challenger_score * 100 / data.score) + '">' + data.re_challenger_score_text + '</div>';
    challengerHTML += '<div id="fight-score-name2"><span>' + data.re_challenger_name + '</span></div>';
    challengerHTML += '</div>';
    $('#fight-score-user2').append(challengerHTML);
    if (data.re_challenger_score < data.challenger_score) {
        $('#fight-score-user1').addClass('circle-success');
        $('#fight-score-user2').addClass('circle-fail');
    } else {
        $('#fight-score-user1').addClass('circle-fail');
        $('#fight-score-user2').addClass('circle-success');
    }
};

quiz.loadScore = function (fightCode) {

};

quiz.cleanForm = function () {
    $('#fight-nav-bottom, #fight-nav-top, #fight-view').html('');
    $('.fight-ended').show();
    $('#fight-quiz-body').css('min-height', '160px');
    $("html, body").animate({scrollTop: 0}, "slow");
};

quiz.submitQuiz = function (q) {
    if (_fightIsStop == false) {
        $('#fight-submit').modal('show');
    }
};