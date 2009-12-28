== Response Tracker ==
Contributors: ravisarma
Donate link: http://ahren.org/code/contribute
Tags: comments, reply, manager
Requires at least: 2.9
Tested up to: 2.9
Stable tag: trunk

Response Tracker lets bloggers track comments on posts by marking them as todo, replied, or ignored, in the Comments Administration page.

== Description ==

Response Tracker is a plugin for WordPress that lets bloggers keep track of comments on their posts by marking them as todo, replied, or ignored, in Comments Administration page in the admin dashboard. On the comments administration page, the plugin paints each comment block with a colour that either indicates the status set by the author (if any) or provides a hint on what the status might be (read below for an explication).

### Features / Details ###

* Adds options to the Comments Admin page actions, for each comment, to mark it as _Todo_, _Replied_, or _Ignore_.
* Comment blocks are given a background colour based on their actual or guessed state (default = _todo_).
* Comments with a response from the post author are background coloured to indicate that.
* Comments by post authors are automatically coloured _ignored_ (though not marked so).
* When new comments are added their parent (if any) is marked as responded if the response is by the post author.
* A bulk mark option/link in the plugin admin page lets you automatically flag existing comments as responded to by author — this has to be used with care of course, in case your blog has thousands of comments. Mine has hundreds and that went through pretty quick.

### Actions vs Status vs Highlights ###

A word of explanation is called for with regard to Response Tracker’s logic of highlighting comments, since it can be (and probably is!) confusing.

Response Tracker colours each comment block in the Comments Administration page with a colour that says something about the status of that comment. The plugin also colours and highlights the action option (_todo_, _replied_ or _ignore_) that indicates the current choice.

Where the user (author or blog admin) has explicitly clicked on one of these actions and made a choice, the highlighted action and the background colour of the comment block coincide in what they indicate. If the author (or blog admin) had previously clicked on the option _replied_ for a comment, then this action link is highlighted and the comment block is coloured accordingly (see the plugin admin/settings page for a legend for the colours).

For comments for which the user has not yet made a selection among the options (_todo_, _ignore_ or _replied_), Response Tracker marks always marks the comment with status _todo_, but also tries to make a guess on what the comment’s real status might be. If the comment is by the post author, then the plugin guesses that the comment can be _ignore_d. If the comment was responded to by the post author, then Response Tracker guesses that the status may be responded (the reason to differentiate between a guess of _responded_ and a status of _replied_ is that you, the blogger, might respond to a comment but want to retain it’s _todo_ status because you want to add more details in a later response). The guess is then used to colour the comment block.

This is the reason for the otherwise puzzling nature of the colour highlighting and the action/option highlighting. Any good user interface should not require this much explication! In a later release, an attempt will be made to clear this up a bit while still retaining some of the logic.

### Why All This Effort? ###

You may wonder why go to all this trouble? The threaded view of comments in the post page gives simple visual cues on what comments have been replied to and which ones are pending a response. Why not just use that to keep track of comment status? I have at least two reasons to prefer using the Comments Adminstration page for managing responses:

* The Comment Administration page posts replies via AJAX, thus making it easier to mark or respond quickly to a large set of comments. There are AJAX commenting plugins for posts, but in my experience none of them quite do the trick, or if they do, they do so at the cost of messing up the look and feel of the theme in use.

* The Comment Administration page can list all comments, not just ones related to a particular post, and sorted descending by date. A good way to scan latest comments, respond to them and mark them.

Additionally, the plugin also provides a mechanism for marking comments that can be ignored, and since it explicitly marks a comment with a status, it makes it possible to mark multiple comments in a single response and mark all the relevant comments as _replied_.

Another possible line of future development is to also add links in the post comments section to let authors mark comments from post pages.

== Installation ==

1. Download and unzip the plugin.
2. Upload the entire directory (response-tracker) to the directory `wp-content/plugins/` in your WordPress installation.
3. Optionally visit the plugin's settings page and bulk mark comments that have author responses.

== Changelog ==

= 0.8 =

* First release of the plugin.

