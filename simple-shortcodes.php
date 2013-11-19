<?php
/*
Plugin Name: Shortcode Maker
Version: 1.0
Plugin URI: http://imakethe.com/
Description: Simple Shortcode Maker
Author: Cole 
Author URI: http://imakethe.com
*/

class Simple_Shortcode {
    
    var $settings;
    
    function Simple_Shortcode() {
        add_action('init', array(&$this,'sc_init_plugin') );
    }
    function sc_init_plugin() {
        $this->settings = get_option('sc_maker_settings');
        add_action('admin_menu', array(&$this,'sc_admin_menu'));
        add_action('admin_enqueue_scripts', array(&$this,'admin_enqueue_scripts'));
        
        if($this->settings) {
            $this->settings = $settings = maybe_unserialize($this->settings);
            $labels = $settings['labels'];
            $codes = $settings['codes'];
            foreach($labels as $key => $label) {
                $html_code = stripslashes($codes[$key]);
                preg_match_all('/%.+?%/', $html_code, $matches);
                if(isset($matches[0]) && is_array($matches[0])) {
                    foreach($matches[0] as $match) {
                        $clean_param = str_replace('%','',$match);
                        $clean_param = explode(':', $clean_param);
                        if(count($clean_param) == 2) {
                            $param_replace = '".(isset($atts["'.$clean_param[0].'"]) ? $atts["'.$clean_param[0].'"] : "'.$clean_param[1].'")."';
                            $html_code = str_replace($match, $param_replace, $html_code);
                        } else {
                            $param_replace = '".$atts["'.$clean_param[0].'"]."';
                            $html_code = str_replace($match, $param_replace, $html_code);
                        }
                    }
                }
                add_shortcode($label,create_function('$atts', 'return "'.$html_code.'";'));
            }
        }
    }
    function admin_enqueue_scripts($hook) {
        if($hook == 'settings_page_simple_shortcode_maker') {
            wp_enqueue_script('jquery');
        }
    }
    function sc_admin_menu() {
        add_options_page('Shortcode Maker', 'Shortcode Maker', 'manage_options', 'simple_shortcode_maker', array(&$this,'display_page'));
    }
    function display_page() {
        if(!empty($_POST)) {
            if(wp_verify_nonce($_POST['_wpnonce'],'save_sc_maker')) {
                update_option('sc_maker_settings',$_POST['simple_sc']);
                
            } else {
                echo '<div class="message below-h2">Error Saving Shortcodes</div>';
            }
            
        }
        $settings = maybe_unserialize(get_option('sc_maker_settings'));
        if(!$settings) {
            $settings = array(
                'labels' => array(''),
                'codes' => array('')
            );
        }
        ?>
        <h2>Simple Shortcode Maker</h2>
        <div style="padding: 20px;" id="simpleSC">
            <form id="scForm" action="" method="post">
                <?php wp_nonce_field( 'save_sc_maker' ); ?>
                <?php foreach($settings['labels'] as $key => $label) : ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th valign="top">
                                <label>Shortcode Label (Do not include brackets)</label>
                            </th>
                            <td>
                                <input class="widefat" name="simple_sc[labels][]" value="<?php echo $label; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th valign="top">
                                <label>Shortcode Replacement (HTML Allowed)</label>
                            </th>
                            <td>
                                <textarea class="widefat" name="simple_sc[codes][]"><?php echo stripslashes($settings['codes'][$key]); ?></textarea>
                            </td>
                        </tr>
                        <tr class="no-code">
                            <td colspan="2">
                                <p><a href="#" class="delShortcode">Remove Shortcode</a></p>
                            </td>                       
                        </tr>
                    </tbody>
                </table>
                <?php endforeach; ?>
                <input style="float:right;" type="submit" class="button button-primary" value="Save Changes" />
                <a style="float:right; margin-right: 40px;" class="button button-secondary" href="#" id="newShortcode">Add New Shortcode</a>
            </form> 
        </div>
        <p></p>
        <p></p>
        
        
        <script type="text/javascript">
            jQuery(document).ready( function($) {
                var clone = $('#simpleSC .form-table:last').clone();
                    clone.find('input').val('');
                    clone.find('textarea').html('');
                    
                $('#newShortcode').on('click', function() {
                    var lastone = $('#simpleSC .form-table:last');
                    
                    if(lastone.length === 0) {                      
                        $('#scForm').prepend(clone.clone());
                    } else {
                        lastone.after(clone.clone());
                    }
                    bind_stuff();
                    return false;
                });
                
                function bind_stuff() {
                    $('a.delShortcode').off('click').on('click', function() {
                        $(this).parents('table.form-table').remove();
                        return false;
                    });
                }
                
                bind_stuff();
            });
        </script>
        
        <?php
    }
}
global $sc_maker;
$sc_maker = new Simple_Shortcode();