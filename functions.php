<?php
//include necessary files
include dirname(__FILE__) . "/incl/getytcaptions.php";
//include dirname(__FILE__) . "/tempinfo.php";

//define constants
//this fixes the file_get_contents
define("FIXFILEGET", serialize(array("ssl" => array("verify_peer" => false, "verify_peer_name" => false))));

//todo automatically choose category based on title
function autoselectcategory($titleinfunc)
{

    $thiscat = 0;
    $titleinfunc = strtolower($titleinfunc);
    if (strpos($titleinfunc, 'bryan') !== false) {
        $thiscat = 157;
    } elseif (strpos($titleinfunc, 'talk') !== false) {
        $thiscat = 24;
    } else {
        $thiscat = 0;
    }

    if ($thiscat != 0) {
        $defaultcat = array(2, 6, $thiscat);
    } else { $defaultcat = array(2, 6);}

    return $defaultcat;

//echo "result".
}

// get all the yt ids from all wp posts to not repost again
function scs_ytap_getYtIdsFromPosts()
{
    $allwpytids = array();
    $postargs = array('post_format' => 'post-format-video', 'posts_per_page' => 999);

    $loop = new WP_Query($postargs);
    while ($loop->have_posts()): $loop->the_post();
        $postmeta = get_post_meta(get_the_ID());
        $tempytid = unserialize($postmeta['scs_ytap_video_id'][0]);
        array_push($allwpytids, $tempytid[0]);
    endwhile;

    return $allwpytids;
}

// adding yt thumbnail to media library
function scs_ytap_addPostFeatImgFromYt($currvidid, $curthumb, $the_post_id)
{

    $somerand = "-" . rand(1000, 9999);
    $filename = $currvidid . $somerand . ".jpg";
    $uploaddir = wp_upload_dir();
    $uploadfile = $uploaddir['path'] . '/' . $filename;

    $contents = file_get_contents($curthumb, false, stream_context_create(unserialize(FIXFILEGET)));
    $savefile = fopen($uploadfile, 'w');
    fwrite($savefile, $contents);
    fclose($savefile);

    $wp_filetype = wp_check_filetype(basename($filename), null);

    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => $filename,
        'post_content' => '',
        'post_status' => 'inherit',
    );

    $attach_id = wp_insert_attachment($attachment, $uploadfile);

    set_post_thumbnail($the_post_id, $attach_id);
}

//this gets the latest 20 videos from youtube
function scs_ytap_getYtVideoListData($scs_apikey, $scs_channelId, $scs_noofvids,$scs_publishedAfter,$scs_publishedBefore)
{
    //var_dump($scs_publishedAfter);
    //var_dump($scs_publishedBefore);
    $orderbydate = "&order=date";
    if($scs_publishedAfter !=""){$scs_publishedAfter = "&publishedAfter=".$scs_publishedAfter."T00%3A00%3A00.0Z";$orderbydate="";}
    if($scs_publishedBefore != ""){$scs_publishedBefore = "&publishedBefore=".$scs_publishedBefore."T00%3A00%3A00.0Z";$orderbydate="";}

    $yturl = "https://www.googleapis.com/youtube/v3/search?key=" . $scs_apikey . "&channelId=" . $scs_channelId . "&part=snippet,id".$orderbydate."&maxResults=" . $scs_noofvids.$scs_publishedAfter.$scs_publishedBefore;
    echo $yturl;
    $json = file_get_contents($yturl, false, stream_context_create(unserialize(FIXFILEGET)));
    $data = json_decode($json, true);
    return $data;
}

//now we get more info on each individual video
function scs_ytap_getYtVideoIndividualData($scs_apikey, $currvidid)
{
    $ytvidurl = "https://www.googleapis.com/youtube/v3/videos?part=id%2C+snippet&id=" . $currvidid . "&key=" . $scs_apikey;
    $jsonvid = file_get_contents($ytvidurl, false, stream_context_create(unserialize(FIXFILEGET)));
    $viddata = json_decode($jsonvid, true);
    return $viddata;

}

function scs_ytap_createPost($currvidtitle, $scs_post_status, $currvidid, $currviddes, $autogencaptions, $currvidtags, $curthumb, $scs_ytap_shortcodes)
{
    //$currvidtitle [scs_ytap_video-title]
    //$currvidid [scs_ytap_video-id]
    //$currvididembed [scs_ytap_video-embed]
    //$currviddes [scs_ytap_video-description]
    //$autogencaptions [scs_ytap_video-captions]
    //$currvidtags [scs_ytap_video-tags]
    //$curthumb [scs_ytap_video-thumbnail]

    $currvididembed = "[embed]https://www.youtube.com/watch?v=" . $currvidid . "[/embed]";
    
    
$scs_variables_array = [$currvidtitle,$currvidid,$currvididembed,$currviddes,$autogencaptions,$currvidtags,$curthumb];
$scs_shortcodes_array = ["[scs_ytap_video-title]", "[scs_ytap_video-id]", "[scs_ytap_video-embed]", "[scs_ytap_video-description]", "[scs_ytap_video-captions]", "[scs_ytap_video-tags]", "[scs_ytap_video-thumbnail]"];
for ($i=0;$i<count($scs_variables_array);$i++){
    $scs_ytap_shortcodes = str_replace($scs_shortcodes_array[$i], $scs_variables_array[$i], $scs_ytap_shortcodes);
}

    //echo "<pre>";
    //var_dump($scs_ytap_shortcodes);
    //echo "</pre>";

    //then we create the post with minimum information, we will update the post later with more info
    $my_post = array(
        'post_title' => $currvidtitle,
        'post_date' => the_date(),
        'post_status' => $scs_post_status,
        'post_type' => 'post',
        //'post_content' => "[embed]https://www.youtube.com/watch?v=" . $currvidid . "[/embed]" . $currviddes . "<br> <h3>Auto Generated Captions</h3>" . $autogencaptions,
        'post_content' => $scs_ytap_shortcodes,
        //'post_category' => autoselectcategory($data['items'][$j]['snippet']['title']),
        'tags_input' => $currvidtags,
        'meta_input' => array($currvidid),

    );
    $the_post_id = wp_insert_post($my_post);

    scs_ytap_addPostFeatImgFromYt($currvidid, $curthumb, $the_post_id);

    //todo ability to add post type of standard or video
    $tag = 'post-format-video';
    $taxonomy = 'post_format';
    wp_set_post_terms($the_post_id, $tag, $taxonomy);
    //add the video url to database
    $meta_key = 'scs_ytap_video_id';

    $meta_value = array(
        //'td_video' => 'https://www.youtube.com/watch?v='.$currvidid,
        $currvidid,
    );

    add_post_meta($the_post_id, $meta_key, $meta_value);

    return $the_post_id;
}

function post_status_array_loop($current)
{
    $post_status_array = array("draft", "publish", "pending", "private", "trash", "inherit");
    $result = "";
    $selected = "";
    foreach ($post_status_array as $post_status) {
        if ($current == $post_status) {$selected = "selected='selected'";} else { $selected = "";}
        $result .= "<option value='$post_status' $selected>$post_status</option>";
    }
    return $result;
}

function scs_ytap_outputcss()
{
    $css = "
<style>
.scs_ytap_ytbutton{
    background-color: #FF0000;
    border-radius: 2px;
    color: white;
    padding: 10px 16px;
    margin: 4px;
    white-space: nowrap;
    font-size: 1.4rem;
    font-weight: 500;
    letter-spacing: .007px;
    text-transform: uppercase;}

</style>";
    echo $css;

}
function scs_ytap_outputjs()
{
    $js = '
<script>
jQuery(document).ready(function($) {
  
    $(".scsytapdate").each(function() {
       // $(this).datepicker();
    });
})
</script>';
    echo $js;

}