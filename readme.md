# SCS Youtube Auto Poster - Wordpress Plugin
Auto Posts newest YouTube Videos from the YouTube channel(s) of your choice.  

![SCS YouTube AutoPoster](https://github.com/webwealthme/scs-yt-autoposter/blob/master/img/screenshot.jpg)


Using this plugin you can manually or automatically (using WPcron job) make WordPress YouTube embedded posts which automatically adds:  
- YouTube video title as post title
- YouTube video tags as post tags
- YouTube video thumbnail as post featured image if theme supports it

# Features
- Easy settings: You just need a free Google developer api key and the YouTube channel id  
- Choose the number of created posts at once. Get up to 50 videos at once, if you need more use the before and after date of the video  
- Choose post settings: post status, post author, post category, post publish date (when post is made or when it was published on YouTube)  

## Use shortcodes to customize post content
[scs_ytap_video-title] eg: Frontend Developer vs Backend Developer - What Should You Learn? (Funducational)  
[scs_ytap_video-id] eg: yBA7lOu4W8Q  
[scs_ytap_video-embed] eg: [embed]https://www.youtube.com/watch?v=yBA7lOu4W8Q[/embed]  
[scs_ytap_video-description] eg: Frontend Developer vs Backend Developer - The most funducational video out there!...  
[scs_ytap_video-captions] eg: hi I\m Mike Mind and welcome to Mike Mind Acodemy...  
[scs_ytap_video-tags] eg: Frontend Developer vs Backend Developer,frontend web development,frontend,backend web development,backend,fullstack,javascript,html,css,node,node.js,...  
[scs_ytap_video-thumbnail] eg: https://i.ytimg.com/vi/yBA7lOu4W8Q/hqdefault.jpg  
TIP: You can use HTML tags here, for eg use &lt;br&gt; for line break, &lt;br&gt; for horizontal line etc  
Example: [scs_ytap_video-embed] [scs_ytap_video-description] &lt;br&gt; &lt;h3&gt;Auto Generated Captions&lt;/h3&gt; [scs_ytap_video-captions]  

## Posting automatization using cron:
Hourly, Twicedaily, Daily  

# How to install?
Like any WordPress plugin :)  

# FAQ
Q: How to get all YouTube Videos?  
A: Use Published After and Before.  


# TODO
...to be updated  

# WISHLIST
- get video comments  
...  

## Feel free to contact me for any request/feedback or other custom work :)
## Note: Requires at least PHP 5.6