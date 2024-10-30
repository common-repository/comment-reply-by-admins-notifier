<?php
/*
Plugin Name: Comment Reply by Admins Notifier
Plugin URI: http://www.yakuphoca.com/comment-reply-by-admins-notifier-plugin/
Description: When you reply a comment as Admin or Editor, an E-mail will send a notify to commenter.
Version: 2.0
Author: Yakup Hoca
Author URI: http://www.yakuphoca.com
License: GPL2
*/

// don't load directly

if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

add_action('comment_post', 'yh_comment_reply_by_admins_notifier');
function yh_comment_reply_by_admins_notifier($comment_id)
{
    $comment = get_comment($comment_id);
    if( $comment->comment_parent != 0 ) 
    {
		if( current_user_can( 'moderate_comments' ) )
		{
			$current_user = wp_get_current_user();
			if ( ($current_user->user_email == $comment->comment_author_email) ) 
			{
				$parent_comment = get_comment($comment->comment_parent);
				if($parent_comment->user_id != $current_user->ID)
				{
					$email = $parent_comment->comment_author_email;
					$adminname = $current_user->display_name;
					$post = get_post($comment->comment_post_ID);
					$posttitle = esc_attr($post->post_title);
					$postlink = get_permalink($comment->comment_post_ID);
					$commentlink = get_comment_link($comment_id);
					$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
					$subject = sprintf('Your Comment was Replied - %1$s', $blogname);
					$notify_message  = sprintf('%1$s (Admin of %2$s) replied to a comment you left on:', $adminname, $blogname ) . '<br />';
					$notify_message .= sprintf( '<a href="%1$s">%2$s</a>', $postlink, $posttitle ) . "<br /><br />";
					$notify_message .= 'You can check admin\'s comment here: ' . "<br />";
					$notify_message .= '<a href="' . $commentlink . '">' . $commentlink . '</a><br /><br /><br />';
					$notify_message .= 'This e-mail was sent by <a href="' . get_home_url() . '">' . $blogname . '</a><br />';
					$notify_message .= 'If the comment isn\'t belong to you, never mind about this e-mail.' . "<br />";
					$message_headers = "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
					wp_mail( $email, $subject, $notify_message, $message_headers );
				}
			}
		}
	}
}	

?>