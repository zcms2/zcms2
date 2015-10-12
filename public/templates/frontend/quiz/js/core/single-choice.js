var quiz = quiz || {};
var questionList = questionList || [];

quiz.singleChoice = function (q) {

    displayQuestion();
    initChoice();

    function displayQuestion() {
        if (q.question_content.length > 0) {
            var html = "<div class='form-group'>";
            $.each(q.question_content, function (index) {
                var check = '';
                if (q.answer.length > 0 && $.inArray(this.id, q.answer) != -1) {
                    check = ' checked="checked" data-content="' + index + '"';
                }
                html += '<div class="radio"><label><input type="radio" name="answer" class="radio-button" ' + check + ' value="' + this.id + '"> <span>' + this.content + '</span></label></div>';
            });
            html += "</div>";

            $("#quiz-answer-content")[0].innerHTML = "<div class=\"quiz_single_choice\"><p>" + html + "</p></div>";
        }
    }

    function initChoice() {
        $(".quiz_single_choice input").click(function () {
            var select = $(".quiz_single_choice input:checked");
            q.answer = [];
            q.answer = [parseInt(select.val())];
        });
    }
};