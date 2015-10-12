String.prototype.trim = function () {
    return this.replace(/^[\s,]|[\s,]$/g, '');
};
String.prototype.ltrim = function () {
    return this.replace(/^[\s,]+/, '');
};
String.prototype.rtrim = function () {
    return this.replace(/[\s,]+$/, '');
};

quiz.selectBox = function (question) {
    function resetContent() {
        //Disable change answer
        $('select.dropdown-select').off('change');
        //Enable change answer
        $('select.dropdown-select').on('change', function () {
            selectBoxAnswer(currentQuestion);
        });
    }

    function selectBoxRender(question) {
        var quizAnswerContent = $('#quiz-answer-content');
        quizAnswerContent.html('');
        quizAnswerContent.append('<div id="content"></div>');
        $('#quiz-answer-content > #content').html(question.question_content);

        resetContent();
        if (question.answer && question.answer != null && question.answer != '') {
            //var select_methods = JSON.parse(question.answer);
            var select_methods = question.answer;

            $('select.dropdown-select').each(function (index) {
                $(this).val(select_methods[index]);
            });

        }
        $('select.dropdown-select').on('change', function () {
            selectBoxAnswer(currentQuestion);
        });
    }

    function selectBoxAnswer(question) {
        var answers = [];

        resetContent();

        $('select.dropdown-select').each(function () {
            answers.push($(this).val());
        });

        //question.answer = JSON.stringify(answers);
        question.answer = answers

        return question.answer;

    }

    this.currentQuestion = question;

    selectBoxRender(question);

};