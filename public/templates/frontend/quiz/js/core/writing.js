var quiz = quiz || {};
var questionList = questionList || [];

quiz.writing = function (q) {
    var min_word = q.question_content.min_word;
    var max_word = q.question_content.max_word;
    var progress_bar = '<div class="col-md-9"><div class="progress"><div class="progress-bar progress-bar-warning" style="width: 0%;"><span>0%</span></div></div><div class="col-md-6">Min: '+ min_word +'</div><div class="col-md-6" style="text-align: right">Max: '+ max_word +'</div></div>';
    $("#quiz-answer-content")[0].innerHTML = '<div class="col-md-3"><strong>Your words: <span class="word-count">0/' + max_word + '</span></strong></div>' + progress_bar + '<textarea class="form-control" rows="10" style="width: 100%; margin-top: 10px; resize: none" id="answer" name="answer" onkeyup="quiz.textChange(' + min_word + ',' + max_word + ')">' + currentQuestion.answer + '</textarea>';
    $(".word-count").css("color", "#f0ad4e");
};

quiz.textChange = function (min_word, max_word) {
    var textarea = $("#answer");
    var word_count = $(".word-count");
    var progress_bar = $(".progress-bar");
    currentQuestion.answer = textarea.val();

    var counter = countWords(textarea);
    var percent = Math.round((counter - min_word) / (max_word - min_word) * 100);

    if(counter == 0){
        word_count.css("color", "#f0ad4e");
    }

    if (counter < min_word) {
        word_count.css("color", "#f0ad4e");
        $(".progress-bar span").text("0%");
        progress_bar.attr("style", "width: 0%");
        progress_bar.removeClass().addClass("progress-bar progress-bar-warning");
    } else if (counter >= min_word && counter <= max_word) {
        word_count.css("color", "#5bc0de");
        $(".progress-bar span").text(percent + "%");
        progress_bar.attr("style", "width: " + percent + "%");
        progress_bar.removeClass().addClass("progress-bar progress-bar-info");
    } else {
        word_count.css("color", "#d9534f");
        $(".progress-bar span").text("Exceed limit");
        progress_bar.attr("style", "width: 100%");
        progress_bar.removeClass().addClass("progress-bar progress-bar-danger");
    }

    word_count.text(counter + "/" + max_word);

    function countWords(textarea) {
        var s = textarea.val();
        if(s == "")
            return 0;
        s = s.replace(/(^\s*)|(\s*$)/gi, "");
        s = s.replace(/[ ]{2,}/gi, " ");
        s = s.replace(/\n /, "\n");
        return s.split(' ').length
    }
};