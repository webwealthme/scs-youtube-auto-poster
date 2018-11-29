# SCS Youtube Auto Poster - Wordpress Plugin
Auto Posts newest YouTube Videos from the YouTube channel(s) of your choice.  

Using this plugin you can manually or automatically (using WPcron job) make WordPress YouTube embedded posts which automatically adds:  
- YouTube video title as post title
- YouTube video tags as post tags
- YouTube video thumbnail as post featured image if theme supports it

# Features
Easy settings: You just need an api key and the yt channel id  
Choose the number of created posts at once. Get up to 50 videos at once, if you need more use the before and after date of the video  
Choose post settings: post status, post author, post category, post publish date (when post is made or when it was published on yt)  

## Use shortcodes to customize post content
            <span title="YouTube Video Title (used as post name as well)">[scs_ytap_video-title] eg: <span class="scsytapeg">Frontend Developer vs Backend Developer - What Should You Learn? (Funducational)</span></span><br>
            <span title="YouTube Video ID">[scs_ytap_video-id] eg: <span class="scsytapeg">yBA7lOu4W8Q</span></span><br>
            <span title="YouTube Video with Wordpress Video Embed Code">[scs_ytap_video-embed] eg: <span class="scsytapeg">[embed]https://www.youtube.com/watch?v=yBA7lOu4W8Q[/embed]</span></span><br>
            <span title="YouTube Video Description">[scs_ytap_video-description] eg: <span class="scsytapeg">Frontend Developer vs Backend Developer - The most funducational video out there!...</span></span><br>
            <span title="YouTube Video Captions (works with automated generated captions too!)">[scs_ytap_video-captions] eg: <span class="scsytapeg">hi I\m Mike Mind and welcome to Mike Mind Acodemy...</span></span><br>
            <span title="YouTube Video Tags/Keywords (used as post Tags as well)">[scs_ytap_video-tags] eg: <span class="scsytapeg">Frontend Developer vs Backend Developer,frontend web development,frontend,backend web development,backend,fullstack,javascript,html,css,node,node.js,...</span></span><br>
            <span title="YouTube Video Thumbnail (used as fetured image as well if theme supports)">[scs_ytap_video-thumbnail] eg: <span class="scsytapeg">https://i.ytimg.com/vi/yBA7lOu4W8Q/hqdefault.jpg</span></span><br>
            <i><span><b>TIP:</b> You can use HTML tags here, for eg use &lt;br&gt; for line break, &lt;hr&gt; for horizontal line etc</span></i><br>
            <i><span><b>Example:</b> [scs_ytap_video-embed] [scs_ytap_video-description] &lt;br&gt; &lt;h3&gt;Auto Generated Captions&lt;/h3&gt; [scs_ytap_video-captions]</span></i><br>

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