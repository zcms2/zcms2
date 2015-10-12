var quiz = quiz || {};
var questionList = questionList || [];

quiz.sMChoice = function (q){

    displayQuestion();
    initChoice();

    function displayQuestion(){
        if(q.question_content.length > 0){
            var html = "<div class='form-group'>";
            $.each(q.question_content, function(index){
                var check = '';
                if(q.answer.length > 0 && $.inArray(this.id,q.answer) != -1 ){
                    check = ' checked="checked" data-content="' + index + '"';
                }
                if(q.multi_choice == true){
                    html += '<div class="checkbox"><label><input type="checkbox" name="answer" class="controls" '+check+' value="'+this.id+'"> '+this.content+'</label></div>';
                }else{
                    html += '<div class="radio"><label><input type="radio" name="answer" class="controls" '+check+' value="'+this.id+'"> '+this.content+'</label></div>';
                }
            });
            html += "</div>";

            $("#quiz-answer-content")[0].innerHTML = "<div class=\"quiz_single_choice\"><p>"+html+"</p></div>";
        }
    }

    function initChoice(){
        $(".quiz_single_choice input").click(function(){
            var select = $(".quiz_single_choice input:checked");
            q.answer = [];
            if(q.multi_choice == true){
                $.each(select, function(){
                    q.answer.push(parseInt($(this).val()));
                });
            }else{
                q.answer = [parseInt(select.val())];
            }
        });
    }
};