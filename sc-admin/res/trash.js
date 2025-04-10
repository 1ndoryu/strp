$(document).ready(function ()
{
    $('.trash_button').click(function ()
    {
        $(this).parent().toggleClass('open');
    });
});


function openCommentModal(id, comment)
{
    $('#trash-comment-id').val(id);
    $('#trash-comment').val(comment);
    $('#trash-comment-modal').modal('show');
}
