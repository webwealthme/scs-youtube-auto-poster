<?php
/**
 * Plugin Name:       SCS YouTube Auto Poster
 * Description:       Auto Posts newest YouTube videos from the Youtube channel(s) of your choice.
 * Version:           2018.11.23
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
            //array($this, 'tytttap')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        include dirname(__FILE__) . "/functions.php";
       $this->options = get_option( 'scs_ytap_options' );

        ?>
         <div class="wrap">
            <h1>SCS YouTube Auto Poster Settings</h1>
            <form method="post" action="options.php">
            <?php
// This prints out all hidden setting fields
        settings_fields('scs_ytap_option_group');
        do_settings_sections('scs_ytap');
        submit_button();
        ?>
            </form>
        </div>
        <form method="post" action="">
        <input type="text" name="action" value="start" hidden>
        <input class="scs_ytap_ytbutton" type="submit" value="Make YT Posts!">
        </form>
      
        
        <?php
if (isset($_POST['action'])) {
           // echo $_POST['action'];
        $this->tytttap();}
        scs_ytap_outputcss();
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
            'My Custom Settings', // Title
            array($this, 'print_section_info'), // Callback
            'scs_ytap' // Page
        );

              
        add_settings_field(
            'apikey',
            'Api Key',
            array($this, 'apikey_callback'),
            'scs_ytap',
            'setting_section_scs_ytap'
        );

        add_settings_field(
            'channelId',
            'Channel Id',
            array($this, 'channelId_callback'),
            'scs_ytap',
            'setting_section_scs_ytap'
        );

        
        add_settings_field(
            'noofvids',
            'Number of Videos',
            array($this, 'noofvids_callback'),
            'scs_ytap',
            'setting_section_scs_ytap'
        );

        
        add_settings_field(
            'post_status',
            'Post Status',
            array($this, 'post_status_callback'),
            'scs_ytap',
            'setting_section_scs_ytap'
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

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
   

    public function apikey_callback()
    {
        global $scs_apikey;
        if (isset($this->options['apikey'])){
        $scs_apikey=$this->options['apikey'];}else{$scs_apikey="";}


        printf(
            '<input type="text" id="apikey" name="scs_ytap_options[apikey]" value="%s" /> (How to get one?)',
            isset($this->options['apikey']) ? esc_attr($this->options['apikey']) : ''
        );
    }

    public function channelId_callback()
    { global $scs_channelId;
        if (isset($this->options['channelId'])){
            $scs_channelId=$this->options['channelId'];}else{$scs_channelId="";}           
        printf(
            '<input type="text" id="channelId" name="scs_ytap_options[channelId]" value="%s" /> (eg: https://www.youtube.com/channel/<b>UC3f86MEyfT0DLaa6uxbFF9w</b>)',
            isset($this->options['channelId']) ? esc_attr($this->options['channelId']) : ''
        );
    }

    public function noofvids_callback()
    { global $scs_noofvids;
        if (isset($this->options['noofvids'])){
            $scs_noofvids=$this->options['noofvids'];}else{$scs_noofvids="";}
        
        printf(
            '<input type="text" id="noofvids" name="scs_ytap_options[noofvids]" value="%s" /> (Maximum 20 videos at once at the moment)',
            isset($this->options['noofvids']) ? esc_attr($this->options['noofvids']) : ''
        );
    }

    public function post_status_callback()
    {
        global $scs_post_status;
        if (isset($this->options['post_status'])){
            $scs_post_status=$this->options['post_status'];}else{$scs_post_status="";}

     
$post_status_code = post_status_array_loop($scs_post_status);

        printf(
            '<select id="post_status" name="scs_ytap_options[post_status]" value="%s">
            '.$post_status_code.'
      </select>',
      isset($this->options['post_status']) ? esc_attr($this->options['post_status']) : ''
    );

    }

    public function tytttap()
    {

        global $scs_apikey;
        global $scs_channelId;
global $scs_noofvids;
global $scs_post_status;


        echo "<h1>POSTS CREATED! ...</h1>";

        $allwpytids = scs_ytap_getYtIdsFromPosts();

        $data = scs_ytap_getYtVideoListData($scs_apikey, $scs_channelId, $scs_noofvids);
        //echo $data;

        for ($j = 0; $j < $scs_noofvids; $j++) {
            echo "<br><h2>Video $j:</h2>";
            $currvidid = $data['items'][$j]['id']['videoId'];
            $currvidtitle = $data['items'][$j]['snippet']['title'];

            //first we check if post was already created in wordpress by video id
            if (!in_array($currvidid, $allwpytids)) {

                $viddata = scs_ytap_getYtVideoIndividualData($scs_apikey, $currvidid);

                echo "ID: " . $currvidid . "<br>";
                $currviddes = $viddata['items'][0]['snippet']['description'];
                //echo "DESCRIPTION: " . $currviddes . "<br>";
                $curthumb = $viddata['items'][0]['snippet']['thumbnails']['high']['url'];
                //echo "THUMBNAIL: " . $curthumb . "<br>";
                //todo category id and matches category from site
                $currvidcatid = $viddata['items'][0]['snippet']['categoryId'];
                //echo "CATEGORY ID: " . $currvidcatid . "<br>";
                $currvidtags = $viddata['items'][0]['snippet']['tags'];
                //echo "TAGS: " . $currvidtags . "<br>";

                for ($i = 0; $i < count($currvidtags); $i++) {
                    // echo $currvidtags[$i] . ", ";
                }
                //then we get the autogenerated captions
                //$autogencaptions = getClosedCaptionsForVideo($currvidid);
                $autogencaptions = "";

                scs_ytap_createPost($currvidtitle, $scs_post_status, $currvidid, $currviddes, $autogencaptions, $currvidtags, $curthumb);

            } else {echo "Video <b>'" . $currvidtitle . "'</b> already posted! <br>";}

        }

    }

}

if (is_admin()) {
    $my_settings_page = new MySettingsPage();
}
