<?php
/**
 * Plugin Name:       SCS YouTube Auto Poster
 * Description:       Auto Posts newest YouTube videos from the YouTube channel(s) of your choice.
 * Version:           2018.11.30
 * Author:            Mike Mind
 * Author URI:        https://mikemind.me
 * Text Domain:       mikemind.me
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/
 */

//here we make the settings menu
class scs_ytap_SettingsPage
{
    public $scs_apikey,
    $scs_channelId,
    $scs_noofvids,
    $scs_post_status,
    $scs_post_category,
    $scs_post_author,
    $scs_post_date,
    $scs_ytap_shortcodes,
    $autogencaptionsswitch,
    $scs_publishedAfter,
    $scs_publishedBefore,
        $scs_cronDay;
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
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
            array($this, 'create_admin_page')
            //array($this, 'scs_ytap_main')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        require_once dirname(__FILE__) . "/functions.php";

        $this->options = get_option('scs_ytap_options');
        scs_ytap_outputcss();
        ?>

         <div class="wrap">
            <h1>SCS YouTube Auto Poster Settings</h1>
            <div class="scs_mikemind">
            I'm Mike, a <a href="http://mikemind.me/" target="_blank">Full-Stack Web Developer Freelancer</a>, and you can contact me for feedback, bugs, feature requests or other work at
            <a href="mailto:admin@webwealth.me?Subject=Hello%20Mike" target="_blank">admin@webwealth.me</a> or at my YouTube Channel:
            <a href="https://www.youtube.com/channel/UC3f86MEyfT0DLaa6uxbFF9w/videos" target="_blank">MikeMindAcodeMY</a>. </div>
            <div class="scs_mikemind donate">
            I don't drink beer nor coffee but you can <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ERBYPAZV9Z9RC&source=url" target="_blank">donate here</a> to help me improve this plugin, create other cool free software and to spend more time with my family. Thank you! :)
            </div>
            <br>
            <div id="scs_ytap_accordion" class="accordion">
    <label for="tm" class="accordionitem"><h1><b>Click for Settings</b></h1></label>
    <input type="checkbox" id="tm"/>
    <div class="hiddentext">
            <form method="post" action="options.php">
            <?php
// This prints out all hidden setting fields
        settings_fields('scs_ytap_option_group');
        do_settings_sections('scs_ytap');
        submit_button();
        ?>
        <p>*Required</p>
            </form>
        </div>
        <form method="post" action="">
        <input type="text" name="action" value="start" hidden><br>
        <input class="scs_ytap_ytbutton" type="submit" value="Make YT Posts!"><br><br>
        </form>
        </div>
  </div>

        <?php

        if (($this->scs_apikey == "") || (!isset($this->scs_apikey))) {
            //if no api key is set, the settings menu will be displayed initially
            echo "<style>.hiddentext{display:block!important;opacity:1!important}</style>";}

        scs_ytap_outputjs();

        if (isset($_POST['action'])) {

            $this->scs_ytap_main();}

    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'scs_ytap_option_group', // Option group
            'scs_ytap_options', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'setting_section_scs_ytap', // ID
            'YouTube Settings', // Title
            array($this, 'print_section_info'), // Callback
            'scs_ytap' // Page
        );

        add_settings_field(
            'apikey',
            'Api Key*',
            array($this, 'apikey_callback'),
            'scs_ytap',
            'setting_section_scs_ytap'
        );

        add_settings_field(
            'channelId',
            'Channel Id*',
            array($this, 'channelId_callback'),
            'scs_ytap',
            'setting_section_scs_ytap'
        );

        add_settings_field(
            'noofvids',
            'Number of Videos*',
            array($this, 'noofvids_callback'),
            'scs_ytap',
            'setting_section_scs_ytap'
        );
        add_settings_field(
            'scs_ytap_publishedAfter',
            'Published After Date',
            array($this, 'publishedAfter_callback'),
            'scs_ytap',
            'setting_section_scs_ytap'
        );
        add_settings_field(
            'scs_ytap_publishedBefore',
            'Published Before Date',
            array($this, 'publishedBefore_callback'),
            'scs_ytap',
            'setting_section_scs_ytap'
        );
        add_settings_section(
            'setting_section_scs_ytap_wppost', // ID
            'Post Settings', // Title
            array($this, 'print_section_info_wppost'), // Callback
            'scs_ytap' // Page
        );
        add_settings_field(
            'post_status',
            'Post Status',
            array($this, 'post_status_callback'),
            'scs_ytap',
            'setting_section_scs_ytap_wppost'
        );
        add_settings_field(
            'post_category',
            'Post Category',
            array($this, 'post_category_callback'),
            'scs_ytap',
            'setting_section_scs_ytap_wppost'
        );
        add_settings_field(
            'post_author',
            'Post Author',
            array($this, 'post_author_callback'),
            'scs_ytap',
            'setting_section_scs_ytap_wppost'
        );
        add_settings_field(
            'post_date',
            'Post Date',
            array($this, 'post_date_callback'),
            'scs_ytap',
            'setting_section_scs_ytap_wppost'
        );
        add_settings_field(
            'scs_ytap_shortcodes',
            'Customize Post Content',
            array($this, 'scs_ytap_shortcodes_callback'),
            'scs_ytap',
            'setting_section_scs_ytap_wppost'
        );
        add_settings_section(
            'setting_section_scs_ytap_cron', // ID
            'Automatization/Cron Settings', // Title
            array($this, 'print_section_info_cron'), // Callback
            'scs_ytap' // Page
        );
        add_settings_field(
            'scs_ytap_cronDay',
            'Automatically check/post',
            array($this, 'cronDay_callback'),
            'scs_ytap',
            'setting_section_scs_ytap_cron'
        );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();

        if (isset($input['apikey'])) {
            $new_input['apikey'] = sanitize_text_field($input['apikey']);
        }

        if (isset($input['channelId'])) {
            $new_input['channelId'] = sanitize_text_field($input['channelId']);
        }

        if (isset($input['noofvids'])) {
            $new_input['noofvids'] = sanitize_text_field($input['noofvids']);
        }

        if (isset($input['post_status'])) {
            $new_input['post_status'] = sanitize_text_field($input['post_status']);
        }
        if (isset($input['post_category'])) {
            $new_input['post_category'] = sanitize_text_field($input['post_category']);
        }
        if (isset($input['post_author'])) {
            $new_input['post_author'] = sanitize_text_field($input['post_author']);
        }
        if (isset($input['post_date'])) {
            $new_input['post_date'] = sanitize_text_field($input['post_date']);
        }
        if (isset($input['scs_ytap_shortcodes'])) {
            //$new_input['scs_ytap_shortcodes'] = sanitize_text_field($input['scs_ytap_shortcodes']);
            $new_input['scs_ytap_shortcodes'] = sanitize_text_field(htmlspecialchars($input['scs_ytap_shortcodes']));

        }
        if (isset($input['publishedAfter'])) {
            $new_input['publishedAfter'] = sanitize_text_field($input['publishedAfter']);
        }
        if (isset($input['publishedBefore'])) {
            $new_input['publishedBefore'] = sanitize_text_field($input['publishedBefore']);
        }
        if (isset($input['cronDay'])) {
            $new_input['cronDay'] = sanitize_text_field($input['cronDay']);
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        //print '*Required';
    }
    public function print_section_info_wppost()
    {
        //print 'Enter your post settings below:';
    }
    public function print_section_info_cron()
    {
        //print 'Wordpress Cron activates when someone visits the site, if you want to work as a real cron (activate on time) without someone visiting the site, then use this ';
    }
    /**
     * Get the settings option array and print one of its values
     */

    public function apikey_callback()
    {

        if (isset($this->options['apikey'])) {
            $this->scs_apikey = $this->options['apikey'];} else { $this->scs_apikey = "";}

        printf(
            '<input type="text" id="apikey" name="scs_ytap_options[apikey]" value="%s" required /> <a href="https://developers.google.com/youtube/v3/getting-started" target="_blank">How to get one?</a>',
            isset($this->options['apikey']) ? esc_attr($this->options['apikey']) : ''
        );
    }

    public function channelId_callback()
    {
        if (isset($this->options['channelId'])) {
            $this->scs_channelId = $this->options['channelId'];} else { $this->scs_channelId = "";}
        printf(
            '<input type="text" id="channelId" name="scs_ytap_options[channelId]" value="%s" required /> eg: <b title="https://www.youtube.com/channel/UC3f86MEyfT0DLaa6uxbFF9w">UC3f86MEyfT0DLaa6uxbFF9w</b>',
            isset($this->options['channelId']) ? esc_attr($this->options['channelId']) : ''
        );
    }

    public function noofvids_callback()
    {
        if (isset($this->options['noofvids'])) {
            $this->scs_noofvids = $this->options['noofvids'];} else { $this->scs_noofvids = "";}

        printf(
            '<input type="number" id="noofvids" name="scs_ytap_options[noofvids]" min="1" max="50" value="%s" required /> <span title="Maximum 50 videos at the moment, use published before and after to get older videos">(1-50)</span>',
            isset($this->options['noofvids']) ? esc_attr($this->options['noofvids']) : ''
        );
    }

    public function post_status_callback()
    {

        if (isset($this->options['post_status'])) {
            $this->scs_post_status = $this->options['post_status'];} else { $this->scs_post_status = "";}

        $post_status_code = scs_ytap_post_status_array_loop($this->scs_post_status);

        printf(
            '<select id="post_status" name="scs_ytap_options[post_status]" value="%s">
            ' . $post_status_code . '
      </select>',
            isset($this->options['post_status']) ? esc_attr($this->options['post_status']) : ''
        );

    }

    public function post_category_callback()
    {

        if (isset($this->options['post_category'])) {
            $this->scs_post_category = $this->options['post_category'];} else { $this->scs_post_category = "";}

        $categories = get_categories(array('hide_empty' => 0));

        $post_category_code = "";
        foreach ($categories as $category) {
            if ($this->scs_post_category == $category->term_id) {$selected = "selected='selected'";} else { $selected = "";}
            $post_category_code .= '<option class="" value="' . $category->term_id . '" ' . $selected . '>' . $category->name . '</option>';
        }

        printf(
            '<select id="post_category" name="scs_ytap_options[post_category]" value="%s">
            ' . $post_category_code . '
      </select>',
            isset($this->options['post_category']) ? esc_attr($this->options['post_category']) : ''
        );

    }

    public function post_author_callback()
    {

        if (isset($this->options['post_author'])) {
            $this->scs_post_author = $this->options['post_author'];} else { $this->scs_post_author = "";}

        $authors = get_users();

        $post_author_code = "";
        foreach ($authors as $author) {
            if ($this->scs_post_author == $author->ID) {$selected = "selected='selected'";} else { $selected = "";}
            $post_author_code .= '<option class="" value="' . $author->ID . '" ' . $selected . '>' . $author->user_nicename . '</option>';
        }

        printf(
            '<select id="post_author" name="scs_ytap_options[post_author]" value="%s">
            ' . $post_author_code . '
      </select>',
            isset($this->options['post_author']) ? esc_attr($this->options['post_author']) : ''
        );

    }

    public function post_date_callback()
    {

        if (isset($this->options['post_date'])) {
            $this->scs_post_date = $this->options['post_date'];} else { $this->scs_post_date = "";}

        $post_date_code = scs_ytap_post_date_array_loop($this->scs_post_date);

        printf(
            '<select id="post_date" name="scs_ytap_options[post_date]" value="%s">
            ' . $post_date_code . '
      </select>',
            isset($this->options['post_date']) ? esc_attr($this->options['post_date']) : ''
        );

    }

    public function scs_ytap_shortcodes_callback()
    {

        //here we replace the shortcode values with the actual variables
        if (isset($this->options['scs_ytap_shortcodes'])) {
            $this->scs_ytap_shortcodes = $this->options['scs_ytap_shortcodes'];} else { $this->scs_ytap_shortcodes = "";}
        if (strpos($this->scs_ytap_shortcodes, '[scs_ytap_video-captions]') == false) {
            $this->autogencaptionsswitch = false;
        } else { $this->autogencaptionsswitch = true;}

        printf(
            //<span title="Leave blank for default: [scs_ytap_video-embed] [scs_ytap_video-description] &lt;br&gt; &lt;h3&gt;Auto Generated Captions&lt;/h3&gt; [scs_ytap_video-captions]">
            //Shortcodes: [scs_ytap_video-title] [scs_ytap_video-id] [scs_ytap_video-embed] [scs_ytap_video-description] [scs_ytap_video-captions] [scs_ytap_video-tags] [scs_ytap_video-thumbnail]</span><br>
            '       </span><br>
            <span><b>Shortcodes:</b></span><br>
            <span title="YouTube Video Title (used as post name as well)">[scs_ytap_video-title] eg: <span class="scsytapeg">Frontend Developer vs Backend Developer - What Should You Learn? (Funducational)</span></span><br>
            <span title="YouTube Video ID">[scs_ytap_video-id] eg: <span class="scsytapeg">yBA7lOu4W8Q</span></span><br>
            <span title="YouTube Video with Wordpress Video Embed Code">[scs_ytap_video-embed] eg: <span class="scsytapeg">[embed]https://www.youtube.com/watch?v=yBA7lOu4W8Q[/embed]</span></span><br>
            <span title="YouTube Video Description">[scs_ytap_video-description] eg: <span class="scsytapeg">Frontend Developer vs Backend Developer - The most funducational video out there!...</span></span><br>
            <span title="YouTube Video Captions (works with automated generated captions too!)">[scs_ytap_video-captions] eg: <span class="scsytapeg">hi I\m Mike Mind and welcome to Mike Mind Acodemy...</span></span><br>
            <span title="YouTube Video Tags/Keywords (used as post Tags as well)">[scs_ytap_video-tags] eg: <span class="scsytapeg">Frontend Developer vs Backend Developer,frontend web development,frontend,backend web development,backend,fullstack,javascript,html,css,node,node.js,...</span></span><br>
            <span title="YouTube Video Thumbnail (used as fetured image as well if theme supports)">[scs_ytap_video-thumbnail] eg: <span class="scsytapeg">https://i.ytimg.com/vi/yBA7lOu4W8Q/hqdefault.jpg</span></span><br>
            <i><span><b>TIP:</b> You can use HTML tags here, for eg use &lt;br&gt; for line break, &lt;hr&gt; for horizontal line etc</span></i><br>
            <i><span><b>Example:</b> [scs_ytap_video-embed] [scs_ytap_video-description] &lt;br&gt; &lt;h3&gt;Auto Generated Captions&lt;/h3&gt; [scs_ytap_video-captions]</span></i><br>



            <textarea rows="4" cols="50" id="scs_ytap_shortcodes" name="scs_ytap_options[scs_ytap_shortcodes]" value="" >%s</textarea>
            <br> Note: [scs_ytap_video-captions] might not always work due to multiple reasons and are in no way proof read.',
            isset($this->options['scs_ytap_shortcodes']) ? esc_attr($this->options['scs_ytap_shortcodes']) : "[scs_ytap_video-embed] [scs_ytap_video-description] &lt;br&gt; &lt;h3&gt;Auto Generated Captions&lt;/h3&gt; [scs_ytap_video-captions]"
        );

    }

    public function publishedAfter_callback()
    {
        if (isset($this->options['publishedAfter'])) {
            $this->scs_publishedAfter = $this->options['publishedAfter'];} else { $this->scs_publishedAfter = "";}

        printf(
            '<input type="date" id="publishedAfter" class="scsytapdate" name="scs_ytap_options[publishedAfter]" value="%s" /> <span title="If facing issues, use Published Before Date as well">(Optional)</span>',
            isset($this->options['publishedAfter']) ? esc_attr($this->options['publishedAfter']) : ''
        );
    }

    public function publishedBefore_callback()
    {
        if (isset($this->options['publishedBefore'])) {
            $this->scs_publishedBefore = $this->options['publishedBefore'];} else { $this->scs_publishedBefore = "";}

        printf(
            '<input type="date" id="publishedBefore" class="scsytapdate" name="scs_ytap_options[publishedBefore]" value="%s" /> <span title="If facing issues, use Published After Date as well">(Optional)</span>',
            isset($this->options['publishedBefore']) ? esc_attr($this->options['publishedBefore']) : ''
        );

    }

    public function cronDay_callback()
    {
        if (isset($this->options['cronDay'])) {
            $this->scs_cronDay = $this->options['cronDay'];} else { $this->scs_cronDay = "OFF";}

        $post_cronDay_code = scs_ytap_post_cronDay_array_loop($this->scs_cronDay);

        printf(
            '<select id="cronDay" name="scs_ytap_options[cronDay]" value="%s">
            ' . $post_cronDay_code . '
      </select> (Optional: Automatically create posts using <a href="https://developer.wordpress.org/plugins/cron/"
      title="WP-Cron does not run constantly as the system cron does; it is only triggered on page load." target="_blank">WPcron</a>)',
            isset($this->options['cronDay']) ? esc_attr($this->options['cronDay']) : 'OFF'
        );

        if (isset($this->scs_cronDay)) {
            if ($this->scs_cronDay != "OFF") {
                //unschedule the cron first to set it with new value
                if (wp_next_scheduled('scs_ytap_cron_event')) {
                    $timestamp = wp_next_scheduled('scs_ytap_cron_event');
                    wp_unschedule_event($timestamp, 'scs_ytap_cron_event');
                }

                //check to see if cron is already scheduled
                if (!wp_next_scheduled('scs_ytap_cron_event')) {
                    wp_schedule_event(time(), $this->scs_cronDay, 'scs_ytap_cron_event');
                }
            } else {
                //this will unschedule the cron
                if (wp_next_scheduled('scs_ytap_cron_event')) {
                    $timestamp = wp_next_scheduled('scs_ytap_cron_event');
                    wp_unschedule_event($timestamp, 'scs_ytap_cron_event');
                }
            }

        }
    }

    public function scs_ytap_main()
    {

        $scs_ytap_output_result = "";
        $allwpytids = scs_ytap_getYtIdsFromPosts();

        $data = scs_ytap_getYtVideoListData($this->scs_apikey, $this->scs_channelId, $this->scs_noofvids, $this->scs_publishedAfter, $this->scs_publishedBefore);

        for ($j = 0; $j < $this->scs_noofvids; $j++) {

            $currvidid = $data['items'][$j]['id']['videoId'];
            $currvidtitle = $data['items'][$j]['snippet']['title'];
            $scs_yt_post_date = $data['items'][$j]['snippet']['publishedAt'];
            $vidno = $j + 1;

            //first we check if post was already created in wordpress by video id
            if (!in_array($currvidid, $allwpytids)) {
                $scs_ytap_output_result .= "<div class='scsposted'><b>Video $vidno:</b> $currvidtitle <i>[$currvidid]</i> posted!</div>";

                $viddata = scs_ytap_getYtVideoIndividualData($this->scs_apikey, $currvidid);

                //echo "ID: " . $currvidid . "<br>";
                $currviddes = $viddata['items'][0]['snippet']['description'];
                //fix bug if video doesn't have description
                if (!isset($currviddes)) {$currviddes = "";}
                //echo "DESCRIPTION: " . $currviddes . "<br>";
                $curthumb = $viddata['items'][0]['snippet']['thumbnails']['high']['url'];
                //echo "THUMBNAIL: " . $curthumb . "<br>";
                //todo category id and matches category from site
                $currvidcatid = $viddata['items'][0]['snippet']['categoryId'];
                //echo "CATEGORY ID: " . $currvidcatid . "<br>";
                //fix bug if video doesn't have tags
                //if((isset($viddata['items'][0]['snippet']['tags']))||($viddata['items'][0]['snippet']['tags'] != false))
                if (isset($viddata['items'][0]['snippet']['tags'])) {$currvidtags = $viddata['items'][0]['snippet']['tags'];} else { $currvidtags = ["yt", "youtube"];}
                //echo "TAGS: " . $currvidtags . "<br>";

                for ($i = 0; $i < count($currvidtags); $i++) {
                    // echo $currvidtags[$i] . ", ";
                }
                //then we get the autogenerated captions
                if ($this->autogencaptionsswitch) {$autogencaptions = scs_ytap_getClosedCaptionsForVideo($currvidid);} else { $autogencaptions = "";}

                scs_ytap_createPost($currvidtitle, $this->scs_post_status, $currvidid, $currviddes, $autogencaptions, $currvidtags, $curthumb, $this->scs_ytap_shortcodes, $this->scs_post_category, $this->scs_post_author, $this->scs_post_date, $scs_yt_post_date);

            } else { $scs_ytap_output_result .= "<div class='scsalreadyposted'><b>Video $vidno:</b> $currvidtitle <i>[$currvidid]</i> already posted!</div>";}

        }

        echo "<div class='scsemulatetextarea'><b>Output log:</b><br>" . $scs_ytap_output_result . "</div>";

    }

}

//if (is_admin()) {
$my_settings_page = new scs_ytap_SettingsPage();
//}

//set a cron job
/*register_activation_hook(__FILE__, 'scs_ytap_cron_activation');

function scs_ytap_cron_activation() {
if (! wp_next_scheduled ( 'scs_ytap_cron_event' )) {
wp_schedule_event(time(), 'hourly', 'scs_ytap_cron_event');
}
}
 */
add_action('scs_ytap_cron_event', 'do_scs_ytap_cron');

function do_scs_ytap_cron()
{
//do something
    require_once dirname(__FILE__) . "/cron.php";

}

register_deactivation_hook(__FILE__, 'scs_ytap_cron_deactivation');

function scs_ytap_cron_deactivation()
{
    wp_clear_scheduled_hook('scs_ytap_cron_event');
}