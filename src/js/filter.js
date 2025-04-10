var wordsFilter = [];

function loadFilter(cat)
{
    $.ajax({
        type: "GET",
        url: site_url + "sc-includes/php/ajax/getfilter.php",
        dataType: "json",
        data: {cat: cat},
        success: function (response) 
        {
            if(response.length > 0)
            {
                wordsFilter = response.map(item => transformToPattern(item));
            }
        },
        timeout: 3000        
    });
}

function transformToPattern(str)
{
    str = str.replace(/á/ig, '(?:a|á)');
    str = str.replace(/é/ig, '(?:e|é)');
    str = str.replace(/í/ig, '(?:i|í)');
    str = str.replace(/ó/ig, '(?:o|ó)');
    str = str.replace(/ú/ig, '(?:u|ú)');
    str = str.replace(/ñ/ig, '(?:n|ñ)');
    str = str.replace(/ü/ig, '(?:u|ü)');
    str = "\\b" + str + "\\b";

    return RegExp(str, 'i');
}

function testWord(text)
{
    if(wordsFilter.length == 0)
        return false;
    
    var result = wordsFilter.some(function(pattern) {
        return pattern.test(text);
    });

    return result;
}

function applyFilter(text)
{
    const response = {}
    response.passed = true;
    response.text = text.replace(/\b\w+\b/g, (word, pos) => {
        if(testWord(word))
        {
            response.passed = false;
            return `<b>${word}</b>`;
        }
      return word;
    });

    return response;
}


function getCaretPosition(editableDiv) {
    let caretOffset = 0;
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
        const range = selection.getRangeAt(0);
        const preCaretRange = range.cloneRange();
        preCaretRange.selectNodeContents(editableDiv);
        preCaretRange.setEnd(range.endContainer, range.endOffset);
        caretOffset = preCaretRange.toString().length;
    }
    return caretOffset;
}

function setCaretPosition(editableDiv, caretOffset) {
    const range = document.createRange();
    const selection = window.getSelection();
    let charIndex = 0, found = false;

    function traverseNodes(node) {
        if (node.nodeType === 3) { // Text node
            const nextCharIndex = charIndex + node.length;
            if (!found && caretOffset >= charIndex && caretOffset <= nextCharIndex) {
                range.setStart(node, caretOffset - charIndex);
                range.setEnd(node, caretOffset - charIndex);
                found = true;
            }
            charIndex = nextCharIndex;
        } else {
            for (let i = 0; i < node.childNodes.length; i++) {
                traverseNodes(node.childNodes[i]);
                if (found) break;
            }
        }
    }

    traverseNodes(editableDiv);
    selection.removeAllRanges();
    selection.addRange(range);
}

$(document).ready(function() {
    $('#text_editable').focusout(function() {
        filterWordText()
    });

    $('#tit').on('input', function() {
        filterWordTitle()
    });

    $(document).on('keyup', '#text_editable', function(e) {
        const caretPosition = getCaretPosition(this);
        var text = $(this).text();
        const response = applyFilter(text);
      
        if(!response.passed)
        {
            this.innerHTML = response.text;
            $(this).addClass('error');
            $('#error_text').hide();
            $('#error_text1').show();
        }
        else
        {
            this.innerHTML = this.innerText;
            $(this).removeClass('error');
            $('#error_text1').hide();
            $('#error_text').hide();
            
        }

        setCaretPosition(this, caretPosition);
    });
});

function filterWordTitle()
{
    var text = $('#tit').val();
    if(testWord(text))
    {
        $(this).addClass('error');
        $('#error_tit1').show();
        return false;
    }
    else
    {
        $(this).removeClass('error');
        $('#error_tit1').hide();
        return true;
    }
}

function filterWordText()
{
    var text = $('#text_editable').text();
    const response = applyFilter(text);
    if(!response.passed)
    {
        this.innerHTML = response.text;
        $(this).addClass('error');
        $('#error_text1').show();
        return false;
    }
    else
    {
        $('#error_text1').hide();
        $('#error_text').hide();
        $("#text").val(text);
        $('#text_editable').html(text);
        //this.innerHTML = this.innerText;
        return true;
    }
}