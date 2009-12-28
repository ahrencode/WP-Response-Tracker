
//jQuery.noConflict();

jQuery(document).ready(colouriseTodos);

function setCommentStatus(el, commentid, status)
{
    var data = { action: 'tracker_comment_status', id: commentid, status: status };
    jQuery.post(ajaxurl, data,
        function(response)
        {
            if( response != "OK" )
            {
                alert(response);
                return(false);
            }

            jQuery(el).parent().find('.trackercurrentselected').removeClass('trackercurrentselected');
            jQuery(el).parent().find('.trackeraction'+status).addClass('trackercurrentselected');

            jQuery(el).parents('.comment').removeClass('trackercommentresponded');
            jQuery(el).parents('.comment').removeClass('trackercommenttodo');
            jQuery(el).parents('.comment').removeClass('trackercommentignored');
            jQuery(el).parents('.comment').removeClass('trackercommentreplied');

            if( status == 'todo' )
                jQuery(el).parents('.comment').addClass('trackercommenttodo');
            else
            if( status == 'ignore' )
                jQuery(el).parents('.comment').addClass('trackercommentignored');
            else
            if( status == 'replied' )
                jQuery(el).parents('.comment').addClass('trackercommentreplied');
        }
    );

    return(false);
}

function colouriseTodos()
{
    jQuery('.trackerstatusignored').parents('.comment').addClass('trackercommentignored');
    jQuery('.trackerstatustodo').parents('.comment').addClass('trackercommenttodo');
    jQuery('.trackerstatusresponded').parents('.comment').addClass('trackercommentresponded');
    jQuery('.trackerstatusreplied').parents('.comment').addClass('trackercommentreplied');
}

