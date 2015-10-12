var quiz = quiz || {};
var questionList = questionList || [];

quiz.fillToInput = function (q){

    loadQuestion();
    initFillToInput();

    function loadQuestion(){
        $('#quiz-answer-content')[0].innerHTML = '<div class=\"quiz_fill_to_input_content\">' + q.question_content + '</div>';
        if(q.answer.length > 0){
            $('.quiz_fill_to_input_content input').each(function(index){
                $(this).val(q.answer[index]);
            });
        }
    }

    function initFillToInput(){
        $('.quiz_fill_to_input_content input').change(function(){
            $('.quiz_fill_to_input_content input').each(function(index){
                q.answer[index] = $(this).val();
            });
        });

    }
};