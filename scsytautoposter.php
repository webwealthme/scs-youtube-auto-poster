<?php
/**
 * Plugin Name:       SCS YouTube Auto Poster
 * Description:       Auto Posts newest YouTube videos from the Youtube channel(s) of your choice.
 * Version:           2018.11.20
 * Author:            Mike Mind
 * Author URI:        https://mikemind.me
 * Text Domain:       mikemind.me
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/
 */




//here we make the settings menu
class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'SCS Auto Poster Options', 
            'SCS Auto Poster', 
            'manage_options', 
            'scs_ytap', 
           // array( $this, 'create_admin_page' )
           array( $this, 'tytttap' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
     //   $this->options = get_option( 'my_option_name' );        

        
        ?>
        <div class="wrap">
            <h1>My Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
            //    settings_fields( 'my_option_group' );
            //    do_settings_sections( 'my-setting-admin' );

                //submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
		//echo "myyy settings";
    }



    //+++++++ADD ALL CODE HERE+++++++++++

//DONE activate on specific page
//wishlist status update
//DONE add thumbnail
//DONE add captions
//todo make published
//todo chron job

//we create a function to automatically choose category based on title
function autoselectcategory($titleinfunc){

    $thiscat = 0;
    $titleinfunc = strtolower($titleinfunc);
    if (strpos($titleinfunc, 'bryan') !== false) {
        $thiscat = 157;
    }elseif(strpos($titleinfunc, 'talk') !== false){
        $thiscat = 24;
    }elseif(strpos($titleinfunc, 'update') !== false){
        $thiscat = 25;
    }elseif((strpos($titleinfunc, 'ad') !== false)||(strpos($titleinfunc, 'ads') !== false)){
        $thiscat = 19;
    }elseif(strpos($titleinfunc, 'tutorial') !== false){
        $thiscat = 11;
    }else{
        $thiscat = 0;
    }

    if($thiscat != 0){
        $defaultcat = array(2, 6, $thiscat);
    }else{$defaultcat = array(2, 6);}

return $defaultcat;


//echo "result".
}

function tytttap() {

    include dirname(__FILE__)."/incl/getytcaptions.php";
    include dirname(__FILE__)."/tempinfo.php";

    echo "<h1>POSTS CREATED! ...</h1>";
    //$chosen = "only me now";
    //echo "<p id='tytttapcss'>$chosen</p>";
    
    // get all the yt ids from all wp posts to not repost again
    $allwpytids = array();
    $postargs = array( 'post_format' => 'post-format-video', 'posts_per_page' => 999);
    
    $loop = new WP_Query( $postargs );
    while ( $loop->have_posts() ) : $loop->the_post();
    $postmeta = get_post_meta(get_the_ID());
    //echo $postmeta['td_post_video'][0];
    //echo "<br>";
    //we get only the yt links
    $regex = '/https?\:\/\/[^\",]+/i';
    preg_match_all($regex, $postmeta['scs_ytap_video_id'][0], $matches);
    //echo $matches[0][0]; 
    // we get only the ids
    $url = $matches[0][0];
    parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
    //we put all the wp yt ids in array
    array_push($allwpytids, $my_array_of_vars['v']);
    //echo $my_array_of_vars['v']; 
    endwhile;
    //echo "<pre>";
    //var_dump($allwpytids);
    //echo "</pre>";
    
    //this fixes the file_get_contents
    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  

    //this gets the latest 20 videos from youtube
    $yturl="https://www.googleapis.com/youtube/v3/search?key=".$scs_apikey."&channelId=".$scs_channelId."&part=snippet,id&order=date&maxResults=".$scs_noofvids;
    $json = file_get_contents($yturl, false, stream_context_create($arrContextOptions));
    $data = json_decode($json,true);
    //echo $data;


    for($j=0;$j<$scs_noofvids;$j++){
    echo "<br><h2>Video $j:</h2>";
    $currvidid = $data['items'][$j]['id']['videoId'];
    //echo "ID: ".$currvidid."<br>";
    $currvidtitle = $data['items'][$j]['snippet']['title'];
    //echo "TITLE: ".$currvidtitle."<br>";
    //echo "<hr>";
    
    //first we check if post was already created in wordpress by video id
    if(!in_array($currvidid, $allwpytids)){
    //now we get more info on each individual video
    $ytvidurl="https://www.googleapis.com/youtube/v3/videos?part=id%2C+snippet&id=".$currvidid."&key=".$scs_apikey;
    $jsonvid = file_get_contents($ytvidurl, false, stream_context_create($arrContextOptions));
    $viddata = json_decode($jsonvid,true);
    
        echo "ID: ".$currvidid."<br>";	
        $currviddes = $viddata['items'][0]['snippet']['description'];
        echo "DESCRIPTION: ".$currviddes."<br>";
        $curthumb = $viddata['items'][0]['snippet']['thumbnails']['high']['url'];
        echo "THUMBNAIL: ".$curthumb."<br>";
        //todo category id and matches category from site
        $currvidcatid = $viddata['items'][0]['snippet']['categoryId'];
        echo "CATEGORY ID: ".$currvidcatid."<br>";
        $currvidtags = $viddata['items'][0]['snippet']['tags'];
        echo "TAGS: ".$currvidtags."<br>";
        for($i=0;$i<count($currvidtags);$i++){
            echo $currvidtags[$i].", ";
        }
        //then we get the autogenerated captions
        $autogencaptions = getClosedCaptionsForVideo($currvidid);
        

        //then we create the post with minimum information, we will update the post later with more info
    $my_post = array(
        'post_title' => $data['items'][$j]['snippet']['title'],
        'post_date' => the_date(),        
        'post_status' => $scs_post_status,
        'post_type' => 'post',
        'post_content' => "[embed]https://www.youtube.com/watch?v=".$currvidid."[/embed]".$currviddes."<br> <h3>Auto Generated Captions</h3>".$autogencaptions,
        'post_category' => $this->autoselectcategory($data['items'][$j]['snippet']['title']),
        'tags_input' => $currvidtags,        
        'meta_input' => array($currvidid),
    
    );
    $the_post_id = wp_insert_post( $my_post );

// adding yt thumbnail to media library
$somerand = rand(1000,9999);
$filename = $currvidid.$somerand.".jpg";
$uploaddir = wp_upload_dir();
$uploadfile = $uploaddir['path'] . '/' . $filename;

$contents= file_get_contents($curthumb, false, stream_context_create($arrContextOptions));
$savefile = fopen($uploadfile, 'w');
fwrite($savefile, $contents);
fclose($savefile);

$wp_filetype = wp_check_filetype(basename($filename), null );

$attachment = array(
    'post_mime_type' => $wp_filetype['type'],
    'post_title' => $filename,
    'post_content' => '',
    'post_status' => 'inherit'
);

$attach_id = wp_insert_attachment( $attachment, $uploadfile );

set_post_thumbnail( $the_post_id, $attach_id );


    //here we add the video id to post metadata to not repost it again
    $tag = 'post-format-video';
    $taxonomy = 'post_format';
    wp_set_post_terms( $the_post_id, $tag, $taxonomy );
    //add the video url to database
    $meta_key = 'scs_ytap_video_id';   
    

    
    $meta_value = array(
        'td_video' => 'https://www.youtube.com/watch?v='.$currvidid,
        'td_last_video' => 'https://www.youtube.com/watch?v='.$currvidid,

    );
    
    add_post_meta( $the_post_id, $meta_key, $meta_value); 



/*
$imagenew = get_post( $attach_id );
$fullsizepath = get_attached_file( $imagenew->ID );
$attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
wp_update_attachment_metadata( $attach_id, $attach_data );
*/



    //todo ability to add post type of standard or video
    $tag = 'post-format-video';
    $taxonomy = 'post_format';
    wp_set_post_terms( $the_post_id, $tag, $taxonomy );
    //add the video url to database
    $meta_key = 'scs_ytap_video_id';   
    

    
    $meta_value = array(
        //'td_video' => 'https://www.youtube.com/watch?v='.$currvidid,       
        $currvidid
    );
    
    add_post_meta( $the_post_id, $meta_key, $meta_value); 

/*
// old specific theme code insert
    $currviddesno = count($currviddes);
    $currvidtitleno = count($currvidtitle);
    $meta_key2 = 'td_post_theme_settings';
        $meta_value2 = array(
        'td_post_template' => 'single_template_11',
        'td_subtitle' => $currviddes,
        'td_source' => $currvidtitle,
        'td_source_url' => 'https://www.youtube.com/watch?v='.$currvidid,

    );
    
    add_post_meta( $the_post_id, $meta_key2, $meta_value2); 
*/

    }
    
    }
    
    }
       
}

if( is_admin() )
    $my_settings_page = new MySettingsPage();


    //-------------START EXTRA CODE-----------------------
/*
//add this to functions.php for theme file
//this calls a chron for the tyt auto poster plugin to work daily
register_activation_hook(__FILE__, 'my_schedule');
add_action('execute_my_url', 'do_this_daily');

function my_schedule() {
    $timestamp = time();//some time you want it to run
    wp_schedule_event($timestamp, 'twicedaily', 'execute_my_url');
}

function do_this_daily() {
    wp_remote_get( 'https://www.timetag.tv/wp-admin/options-general.php?page=my-setting-admin');
}
*/

/*
//add this as cron job
// 0	*	*	*	*	wget -O - https://www.timetag.tv/
*/
//----------------- END EXTRA CODE-------------------------