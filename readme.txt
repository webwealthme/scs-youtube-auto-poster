=== SCS YouTube Auto Poster ===
Contributors: mikemindme
Tags: auto youtube, auto posting, youtube posts, embed videos, embed youtube videos, videos, youtube, youtube videos
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ERBYPAZV9Z9RC&source=url
Requires at least: 4.0
Tested up to: 4.9.8
Requires PHP: 5.6
Stable tag: trunk
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Auto Posts newest YouTube videos from the YouTube channel(s) of your choice.

== Description ==
Using this plugin you can manually or automatically (using WPcron job) make WordPress YouTube embedded posts which automatically adds:
* YouTube video title as post title
* YouTube video tags as post tags
* YouTube video thumbnail as post featured image if theme supports it

= Features: =
* Easy settings: You just need a free Google developer api key and the YouTube channel id
* Choose the number of created posts at once. Get up to 50 videos at once, if you need more use the before and after date of the video
* Choose post settings: post status, post author, post category, post publish date (when post is made or when it was published on YouTube)

**Use shortcodes to customize post content**
[scs_ytap_video-title] eg: Frontend Developer vs Backend Developer - What Should You Learn? (Funducational)
[scs_ytap_video-id] eg: yBA7lOu4W8Q
[scs_ytap_video-embed] eg: [embed]https://www.youtube.com/watch?v=yBA7lOu4W8Q[/embed]
[scs_ytap_video-description] eg: Frontend Developer vs Backend Developer - The most funducational video out there!...
[scs_ytap_video-captions] eg: hi I\\m Mike Mind and welcome to Mike Mind Acodemy...
[scs_ytap_video-tags] eg: Frontend Developer vs Backend Developer,frontend web development,frontend,backend web development,backend,fullstack,javascript,html,css,node,node.js,...
[scs_ytap_video-thumbnail] eg: https://i.ytimg.com/vi/yBA7lOu4W8Q/hqdefault.jpg
TIP: You can use HTML tags here, for eg use <br> for line break, <hr> for horizontal line etc
Example: [scs_ytap_video-embed] [scs_ytap_video-description] <br> <h3>Auto Generated Captions</h3> [scs_ytap_video-captions]

**Posting automation using cron:**
Hourly, Twicedaily, Daily


Feel free to contact me for any request/feedback or other custom work :)
Note: Requires at least PHP 5.6

== Installation ==
Like any WordPress plugin :) 

== Frequently Asked Questions ==
Q: How to get all YouTube Videos?  
A: Use Published After and Before.  

== Screenshots ==
1. SCS YouTube Auto Poster Settings

== Changelog ==
= 2018.11.29 =
Initial release :)