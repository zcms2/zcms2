var quiz = quiz || {};
var questionList = questionList || [];

quiz.multiChoice = function (q) {

    displayQuestion();
    initChoice();

    function displayQuestion() {
        if (q.question_content.length > 0) {
            var html = '<div class="form-group">';
            $.each(q.question_content, function (index) {
                var check = '';
                if (q.answer.length > 0 && $.inArray(this.id, q.answer) != -1) {
                    check = ' checked="checked" data-content="' + index + '"';
                }
                html += '<div class="question-checkbox"><label><input type="checkbox" name="answer" class="checkbox tick" ' + check + ' value="' + this.id + '"> <span>' + this.content + '</span></label></div>';
            });
            html += '</div>';

            $('#quiz-answer-content')[0].innerHTML = '<div class=\"quiz_multi_choice\"><p>' + html + '</p></div>';
        }
    }

    function initChoice() {
        $('.quiz_multi_choice input').click(function () {
            var select = $('.quiz_multi_choice input:checked');
            q.answer = [];
            $.each(select, function () {
                q.answer.push(parseInt($(this).val()));
            });
        });
    }
};