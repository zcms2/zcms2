/**
 * Created by huydq on 6/23/14.
 */

var quiz = quiz || {};
var questionList = questionList || [];

quiz.dragDropParagraphs = function (q) {

    var bgColorRecommendWrapper = 'whitesmoke';
    var bgColorPlaceholder = '#F4D03F';
    var bgRadiusPlaceholder = '4px';

    loadQuestion();
    initSortable(bgColorRecommendWrapper, bgColorPlaceholder, bgRadiusPlaceholder);

    function loadQuestion() {
        //Load answer description
        var recommend_wrapper = '<div class="drag-drop-recommend-wrapper panel-warning">';
        var recommend = q.question_content.recommend;
        var answer = q.answer;
        $.each(recommend, function (key, value) {
            //Check recommend not in answer
            if (jQuery.inArray(value, answer) == -1) {
                recommend_wrapper += '<span class="drag-drop-item" answer="' + value + '">' + value + '</span>';
            }
        });
        recommend_wrapper += '</div>';

        var paragraph_wrapper = '<div class="drag-drop-paragraph-wrapper">';
        paragraph_wrapper += q.question_content.paragraph;
        paragraph_wrapper += '</div>';

        $('#quiz-answer-content')[0].innerHTML = recommend_wrapper + paragraph_wrapper;

        //Load answer
        var i = 0;
        $('.drag-drop-drop-able').each(function () {
            //Check answer in recommend
            if (jQuery.inArray(answer[i], recommend) > -1) {
                var html = '<span class="drag-drop-item" answer="' + answer[i] + '">' + answer[i] + '</span>';
                $(this).html(html);
                $(this).css('border-bottom', 'none');
                $(this).css('margin', '0');
            }
            i++;
        });

        //Balance CSS
        var recommend_wrapper_height = $('.drag-drop-recommend-wrapper');
        recommend_wrapper_height.height(recommend_wrapper_height.height);
    }

    function initSortable(bgColorRecommendWrapper, bgColorPlaceholder, bgRadiusPlaceholder) {

        var dropAbleParent;

        $('.drag-drop-recommend-wrapper').sortable({
            connectWith: '.drag-drop-drop-able',
            placeholder: 'drag-drop-placeholder',
            cursor: 'move',
            cursorAt: {
                top: 10,
                left: 15
            },
            start: function () {
                dropAbleParent = $(this);
            },
            stop: function () {
                $(this).css('background-color', '');
                $(this).css('border-radius', '');
            },
            receive: function () {
                $(this).css('background-color', '');
                $(this).css('border-radius', '');
            }
        }).droppable({
            over: function () {
                $(this).css('background-color', bgColorRecommendWrapper);
                $(this).css('border-radius', bgRadiusPlaceholder);
            },
            out: function () {
                $(this).css('background-color', '');
                $(this).css('border-radius', '');
            }
        });

        $('.drag-drop-drop-able').sortable({
            connectWith: '.drag-drop-recommend-wrapper, .drag-drop-drop-able',
            placeholder: 'drag-drop-placeholder',
            cursor: 'move',
            cursorAt: {
                top: 10,
                left: 15
            },
            start: function () {
                dropAbleParent = $(this);
            },
            receive: function (event, ui) {
                //Save answer when drag-drop
                saveAnswer(q.answer);

                //Reset CSS
                $(this).css('border', 'none');
                $(this).css('margin', '0');

                //Init swappable
                var children = $(this).children();
                children.each(function () {
                    if ($(this)[0] != ui.item[0]) {
                        $(this).appendTo(dropAbleParent).hide().show(300);

                        //Reset CSS in paragraph
                        $(this).css('background-color', '');
                        $(this).css('border-radius', '');

                        //Save answer when swap
                        saveAnswer(q.answer);
                    }
                });
            },
            remove: function () {
                $(this).css('border-bottom', '1px solid #333333');
                $(this).css('margin', '0 5px 0 5px');

                //Save answer when remove
                saveAnswer(q.answer);
            }
        }).droppable({
            over: function () {
                $(this).css('background-color', bgColorPlaceholder);
                $(this).css('border-radius', bgRadiusPlaceholder);
            },
            out: function () {
                $(this).css('background-color', '');
                $(this).css('border-radius', '');
            }
        });

        $('.drag-drop-item').droppable({
            over: function (event, ui) {
                if ($(this).parent()[0].className == 'drag-drop-drop-able ui-sortable ui-drop-able') {
                    $(this).css('background-color', bgColorPlaceholder);
                    $(this).css('border-radius', bgRadiusPlaceholder);
                }
            },
            out: function () {
                $(this).css('background-color', '');
                $(this).css('border-radius', '');
            }
        });

        $('.drag-drop-paragraph-wrapper, .drag-drop-recommend-wrapper').disableSelection();
    }

    function saveAnswer(data) {
        var i = 0;
        $('.drag-drop-drop-able').each(function () {
            var answer = $(this).children().attr('answer');
            if (answer != '' && answer != null) {
                data[i] = answer;
            } else {
                data[i] = '';
            }
            i++;
        });
    }
};