<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
    Plugin Name: Comment Tracker
    Plugin URI:http://ahren.org/code/wp-response-tracker
    Description: A plugin to keep track of responses to comments in the dashboard
    Version: 0.8
    Author: Ravi Sarma
    Author URI: http://ahren.org/code/
*/

add_action('admin_head', 'response_tracker_head');
add_action('wp_ajax_tracker_comment_status', 'tracker_set_comment_status');
add_filter('comment_row_actions', 'response_tracker', 10, 2);
//add_filter('admin_comment_types_dropdown', 'tracker_comments_filter', 10, 1);
add_action('comment_post', 'tracker_update_meta_on_comment');
# setup admin menu
add_action('admin_menu', 'response_tracker_admin_menu');

#-------------------------------------------------------------------------------
function response_tracker($actions, $comment)
{
    global $current_user;

    get_currentuserinfo();

    $current    = get_comment_meta($comment->comment_ID, 'tracker_comment_status', true);
    $responded  = get_comment_meta($comment->comment_ID, 'tracker_responded_flag', true);

    if( $current == "" )
        $current = "todo";

    // TODO: this is a hack... need a better way to mark the below.
    // I am using an empty span and it's class to designate the current status.
    // This class is then used via jQuery in response-tracker.js to set the
    // colour of the comment block when the page load completes.
    if( $current == 'replied' )
        $curstatusclass = 'trackerstatusreplied';
    elseif( $current == 'ignore' || $comment->comment_author == $current_user->user_login )
        $curstatusclass = 'trackerstatusignored';
    elseif( $responded )
        $curstatusclass = 'trackerstatusresponded';
    else
        $curstatusclass = 'trackerstatustodo';
    $actions['status'] = "<span class='$curstatusclass'> </span>";

    $actions['status'] .= "Mark: (";
    foreach( array('todo', 'replied', 'ignore') as $status )
    {
        $class = "trackerstatusaction trackeraction$status";
        $class .= ( $status == $current ) ? " trackercurrentselected" : "";
        $onclick = "onClick='setCommentStatus(this, $comment->comment_ID, \"$status\");'";
        $actions['status'] .= " <span class='$class' $onclick>" . ucfirst($status) . "</span>";
    }
    $actions['status'] .= " )";
    return($actions);
}

#-------------------------------------------------------------------------------
function response_tracker_head()
{
    $plugin_url = WP_PLUGIN_URL . "/response-tracker/";
    print
    "
        <link href='" . esc_url("$plugin_url/response-tracker.css") .
        "' rel= 'stylesheet' type='text/css' />
        <script language='JavaScript' src='" .
         esc_url("$plugin_url/response-tracker.js") .
        "'></script>
    ";
}

#-------------------------------------------------------------------------------
function tracker_set_comment_status()
{
    global $_POST;

    $commentid  = $_POST['id'];
    $status     = $_POST['status'];

    if( ! current_user_can('edit_post', $commentid) )
        die("Sorry you can't do that.");

    add_comment_meta($commentid, 'tracker_comment_status', $status, true)
        or update_comment_meta($commentid, 'tracker_comment_status', $status)
        or die("Failed to add or update status for comment.");

    die("OK");
}

#-------------------------------------------------------------------------------
function tracker_comments_filter($options)
{
    $options['todo']    = __('Todo');
    $options['replied'] = __('Replied');
    $options['ignore']  = __('Ignore');
    return($options);
}

#-------------------------------------------------------------------------------
function tracker_update_meta_on_comment($commentid)
{
    // TODO: for now we just return silently
    if( ! ($comment = get_comment($commentid)) )
        return;

    // TODO: for now we just return silently
    if( ! tracker_check_update_response_flag($comment) )
        return;
}

#-------------------------------------------------------------------------------
function response_tracker_admin_menu()
{
    add_options_page(
        'Response Tracker Options',
         'Response Tracker',
         'administrator',
         'response-tracker-settings',
         'response_tracker_settings');
}

#-------------------------------------------------------------------------------
function response_tracker_settings()
{
    global $_GET;

    if( $_GET['action'] == 'primedb' )
        response_tracker_prime_db();

    print
    "
        <div style='padding: 30px;'>

        <h2 style='margin-bottom: 40px;'>Response Tracker Settings and Actions</h2>

        <dl>
            <dt>
                &raquo;&raquo;
                <a href=''
                    onClick='document.location=document.location+\"&action=primedb\"; return false;'>
                    Prime the comment database</a>
            </dt>
            <dd style='margin-left: 30px; margin-top: 12px; font-size: smaller; margin-right: 100px;'>
                Mark comments that have an author response, in the comments admin page.
                Beware, this loops through all the comments, so if you have a lot of
                them, be very, very, very patient.
            </dd>
        </dl>

        </div>
    ";
}

#-------------------------------------------------------------------------------
function response_tracker_prime_db()
{
    $logmsg = "";

    foreach( get_comments() as $comment )
    {
        if( ! tracker_check_update_response_flag($comment) )
            response_tracker_die("
                Bad things have happened! Cannot get post info
                for comment id = $comment->comment_ID");

        $logmsg .= "Marked comment $comment->comment_ID as responded to by post author.<br/>\n";
    }

    print
    "
        <h3>Log of what I did:</h3>
        <div style='
                border: 1px solid #999999;
                height: 200px;
                overflow: auto;
                margin: 20px 40px;
                '>
                $logmsg
        </div>
    ";
}

#-------------------------------------------------------------------------------
function tracker_check_update_response_flag($comment)
{
    // we can ignore top level comments and comments by unregistered users
    if( $comment->comment_parent == 0 || $comment->user_id == 0 )
        return(true);

    if( ! ($post = get_post($comment->comment_post_ID)) )
        return(false);

    /*
    print "Got Comment $comment->comment_ID (author $comment->user_id),
            related to post $comment->comment_post_ID (author $post->post_author),
            with parent $comment->comment_parent<br/>\n";
    */

    if( $comment->user_id != $post->post_author )
        return(true);

    // if we have already added this to the comment_meta table, this call
    // below will fail, but we can silently ignore that (as we do)
    add_comment_meta($comment->comment_parent, 'tracker_responded_flag', 1, true);

    return(true);
}


#-------------------------------------------------------------------------------
function response_tracker_die($msg)
{
    print
    "
        <div style='
                background-color: #bb2200;
                color: #ffffff;
                margin-top: 30px;
                margin-bottom: 30px;
                margin-left: 20%;
                width: 60%;
                padding: 0;'>
            <p style='padding: 30px; font-size: large;'>$msg</p>
        </div>
    ";
    die();
}

?>
