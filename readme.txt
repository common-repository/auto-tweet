=== Auto Tweet ===
Contributors: joefish
Tags: twitter, tweet, post
Requires at least: 2.0
Tested up to: 2.3.3
Stable tag: 2.1

Auto Tweet is a plugin to cross-post selected entries to Twitter.

== Description ==

Auto Tweet adds a check box to the Write Post editor (both the TinyMCE/Visual Editor and the advanced editor).
When this box is checked, publishing your post will also send the post to Twitter.
If a post is longer than 140 characters, it will be truncated.
Auto Tweet creates a new column in the "posts" table of your database to mark which posts have already been submitted and will use that information to help prevent double-posting.

== Installation ==

= New installation =

1. Download the plugin package and expand it. (Because you're reading this, you've probably already done this step.)
1. Backup your WordPress database. Auto Tweet adds a column to the "posts" table. This operation has not been problematic in my testing, but you should have a backup just in case. Besides, it's good to backup often. :)
1. Upload `auto_tweet.php` to your plugins directory, likely `wp-content/plugins/`. The plugin will NOT install or function properly if it is located in a subdirectory.
1. Activate the plugin through the Plugins menu in WordPress.
1. Visit the Auto Tweet options page to input your Twitter username and password, and optionally adjust how italic and bold HTML tags are handled or to set checkbox persistence. ('Options -> Auto Tweet' in your WordPress Dashboard)

= Upgrade installation =

1. Download the plugin package and expand it. (Because you're reading this, you've probably already done this step.)
1. Backup your WordPress database. Auto Tweet will not make new database changes in an upgrade installation, but isn't it always a good idea to backup?
1. Deactivate your existing version of Auto Tweet.
1. Upload `auto_tweet.php` to your plugins directory, likely `wp-content/plugins/`. The plugin will NOT install or function properly if it is located in a subdirectory. This will overwrite your previous version of Auto Tweet.
1. Activate the plugin through the Plugins menu in WordPress.
1. Visit the Auto Tweet options page to input your Twitter username and password as needed, and optionally adjust how italic and bold HTML tags are handled or to set checkbox persistence. ('Options -> Auto Tweet' in your WordPress Dashboard)

== Screenshots ==

1. The Auto Tweet options page.
2. The Auto Tweet checkbox on the Write Post page.

== Configuration ==

Visit the Auto Tweet options page ('Options -> Auto Tweet') to enter your Twitter username and password before using Auto Tweet. You can also specify how Auto Tweet handles italic and bold HTML tags or to set checkbox persistence.

== Frequently Asked Questions ==

= My posts publish on my blog, but don't publish to Twitter. What's wrong? =

There are two likely possibilities.
First, Twitter may be down. At the time of this writing, Twitter is growing very rapidly and does occasionally experience issues with service availability.
Second, you may have incorrectly entered your username and/or password on the Auto Tweet options page. Double check that you're using the correct login info. Auto Tweet does not verify your login information. It tries to use whatever you input.

= Sometimes it takes a very long time to publish a post with Auto Tweet. What's up? =

Twitter is currently undergoing rapid growth and is not always stable. The operation may time out, either on the server end or in your browser.
This is an issue with Twitter, not with Auto Tweet.
If your post/tweet contains links, Auto Tweet will convert them into TinyURLs. Performance issues at TinyURL may also slow the posting/tweeting process.

= What happened to my HTML? =

Twitter does not render HTML, so Auto Tweet uses regular expressions to remove markup.
Italics and bold tags may optionally be processed specially to preserve some form of text emphasis in the output submitted to Twitter. Visit the Options page to configure this feature.

= What about links? =

Auto Tweet uses regular expressions to convert links to a Twitter-friendly format.
What appears in your WordPress post as `<a href="http://feastofcrumbs.com/">A Feast of Crumbs</a>` will be submitted to Twitter as `A Feast of Crumbs ( http://tinyurl.com/24w7wc )`.
Publish your post as you want it to appear in WordPress and Auto Tweet will do all the heavy lifting automagically.
"Raw" links will not be converted. Auto Tweet will not convert URLs not contained in an anchor tag, though Twitter may.
Do not use anchor attributes like `class`, `rel`, `title`, `target`, etc. These will likely confound the regular expression that converts links, and may send garbage output to Twitter. For best results, use only the `href` attribute with your links. 

= I published my post without the Auto Tweet box checked. How do I get that post to Twitter? =

Edit your existing WP post. In the editor, change the post status to draft. Click the Save and Continue Editing button. When the page reloads, click the Auto Tweet checkbox and hit the Publish button.
This may seem like an awkward way to do it, but Auto Tweet only hooks the Publish action. Hooking the Save action can result in inadvertant double-tweets (and triple-tweets, and quadruple-tweets...)

= Can I future post? =

No. Auto Tweet does not evalutate the post date. If you publish a post/tweet with a future date, WordPress will handle the post as you would expect, but Auto Tweet will still submit to Twitter immediately.
I have no plans to implement support for future posts, but I may consider it if it's requested.

= The JavaScript counter on the post page is inaccurate when I post links. Why? =

The JavaScript counter does its best to evalute the text entered in the `post_content` textarea and determine exactly how many characters will be in the final output.
To this end it uses an `onkeyup` event handler to check that textarea after every key press.
First it will convert italics and bold tags according to your preferences.
Next it counts and stores the number of links.
Next it strips HTML tags.
Then it counts the total number of characters in what's left.
Finally, it adds 30 to that character count for each link it found earlier.
As far as I've noticed all TinyURLs are the same length, so the output length will always be the same. In my implementation, that's 30 characters.
All of this conversion is done simply to obtain a "live" character count. The JavaScript does not change the content.
The JavaScript assumes your TinyURL output will be 30 characters. If the output is a different length, the count will be inaccurate.
Also, additional link attributes like `rel` and `target` may generate inaccurate counts. For best results, use only the href attribute with your links. 
 

--Love and sloppy kisses, Joefish http://feastofcrumbs.com/
