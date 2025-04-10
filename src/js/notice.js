$(document).ready(function()
{
    $('.notices a, .notice-view').on('click', function(e){
       
        const notice = $(this).data('id');
        $.post("sc-includes/php/ajax/read_notice.php", {notice}, function(data)
        {
        });
    });

    $('.notices .notice-close').on('click', function(e){
        e.preventDefault();
    });

    const notices = $('.notices .notice');
    
    for(let i = 0; i < notices.length; i++)
    {
        setTimeout(function(){
            $(notices[i]).slideDown(500);
            setTimeout(function(){
                $(notices[i]).slideUp(500);
                const id = $(notices[i]).data('id');
                $.post("sc-includes/php/ajax/read_notice.php", {notice: id}, function(data)
                {});
            }, 10000);
        }, 2000 * (i+1));
  
    }


});

function readNotice(notice, element)
{
    $.post("sc-includes/php/ajax/read_notice.php", {notice}, function(data)
    {
        if(data.result == 'ok')
        {
            console.log('ok');
        }

        $(element).parent().parent().remove();
    });
}