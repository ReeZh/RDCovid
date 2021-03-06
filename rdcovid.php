<?php
/**
 * Plugin Name: RDCovid
 * Plugin URI:  https://reezhdesign.com
 * Description: ReeZh Design COVID-19 WordPress Widget Plugin.
 * Version:     1.5.4
 * Author:      ReeZh Design
 * Author URI:  https://reezhdesign.com
 * Donate link: https://reezhdesign.com
 * License:     GPLv2
 * Text Domain: rdcovid
 * Domain Path: /languages
 *
 * @link https://reezhdesign.com
 *
 * @package RDCovid
 * @version 1.5.4
 */

/**
 * Copyright (c) 2020 ReeZh Design (email : reezhdesign@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined('ABSPATH') or die('Hey, you can\t access this file. Error Bro!');

/**
 * Autoloads files with classes when needed
 *
 * @since  1.0.0
 * @param  string $class_name Name of the class being requested.
 * @return void
 */
function rdcovid_autoload_classes($class_name)
{
    if (0 !== strpos($class_name, 'RDC_')) {
        return;
    }
  // Set up our filename
    $filename = strtolower(str_replace('_', '-', substr($class_name, strlen('RDC_'))));
  // Include our file
    RDCovid::include_file('includes/class-' . $filename);
}
spl_autoload_register('rdcovid_autoload_classes');

/**
 * Main initiation class
 *
 * @since  1.0.0
 */
final class RDCovid
{

  /**
   * Current version
   *
   * @var  string
   * @since  1.0.0
   */
    const VERSION = '1.5.4';

  /**
   * URL of plugin directory
   *
   * @var string
   * @since 1.0.0
   */
    protected $url = '';

  /**
   * Path of plugin directory
   *
   * @var string
   * @since 1.0.0
   */
    protected $path = '';

  /**
   * Plugin basename
   *
   * @var string
   * @since 1.0.0
   */
    protected $basename = '';

  /**
   * Detailed activation error messages
   *
   * @var array
   * @since 1.0.0
   */
    protected $activation_errors = array();

  /**
   * Singleton instance of plugin
   *
   * @var RDC
   * @since  1.0.0
   */
    protected static $single_instance = null;

  /**
   * Instance of RDC_Transients
   *
   * @since 1.5.0
   * @var RDC_Transients
   */
    protected $transients;

  /**
   * Instance of RDC_Dashboard
   *
   * @since 1.5.0
   * @var RDC_Dashboard
   */
    protected $dashboard;

  /**
   * Instance of RDC_Widget
   *
   * @since 1.5.1
   * @var RDC_Widget
   */
    protected $widget;

  /**
   * Instance of RDC_Announcements
   *
   * @since 1.5.1
   * @var RDC_Announcements
   */
    protected $announcement;


  /**
   * External cache checking
   *
   * @since 1.5.3
   * @var boolean
   */
    protected $extcache;

  /**
   * Creates or returns an instance of this class.
   *
   * @since  1.0.0
   * @return RDC A single instance of this class.
   */
    public static function get_instance()
    {
        if (null === self::$single_instance) {
            self::$single_instance = new self();
        }
        return self::$single_instance;
    }

  /**
   * Sets up our plugin
   *
   * @since  1.0.0
   */
    protected function __construct()
    {
        $this->basename = plugin_basename(__FILE__);
        $this->url      = plugin_dir_url(__FILE__);
        $this->path     = plugin_dir_path(__FILE__);
    }

  /**
   * Attach other plugin classes to the base plugin class.
   *
   * @since  1.5.0
   * @return void
   */
    public function plugin_classes()
    {
      // Attach other plugin classes to the base plugin class.
        $this->transients     = new RDC_Transients($this);
        $this->dashboard      = new RDC_Dashboard($this);
        $this->widget         = new RDC_Widget($this);
        $this->announcement   = new RDC_Announcements($this);
    } // END OF PLUGIN CLASSES FUNCTION

  /**
   * Init hooks
   *
   * @since 1.0.0
   * @return void
   */
    public function init()
    {
      // bail early if requirements aren't met
        if (! $this->check_requirements()) {
            return;
        }

      // initialize plugin classes
        $this->plugin_classes();
    }

  /**
   * Check External Cache
   *
   * @since 1.5.3
   * @return boolean
   */
    public function is_external_cache()
    {
      // check external cache status
        if (wp_using_ext_object_cache()) {
            return true;
        } else {
            return false;
        }
    }

  /**
   * Force Reload Data
   *
   * @since 1.5.3
   * @return boolean
   */
    public function force_reload()
    {
      // force cleanup data and syncronize
        $this->transients->cleanup();
        $this->transients->sync();
    }
  
  /**
   * Add hooks and filters
   *
   * @since 1.0.0
   * @return void
   */
    public function hooks()
    {
      // Initialize
      add_action('init', array($this, 'init'), 0);

      // add action scheduler
      add_action('rdcovid_scheduler', array($this, 'register_scheduler'), 10);

      // add action notification
      add_action('rdcovid_notification', array($this, 'register_notification'), 10);

      // add action languages
      add_action( 'plugins_loaded', array($this, 'load_languages'), 10);
      load_plugin_textdomain( 'rdcovid', false, dirname( $this->basename ) . '/languages/' );

    }

    public function register_scheduler()
    {
      // register scheduler cron hook
        if (!wp_next_scheduled('rdcovid_scheduler')) {
            wp_schedule_event(time(), 'hourly', 'rdcovid_scheduler');
        }
    }

    public function register_notification()
    {
      // register notification cron hook
        if (!wp_next_scheduled('rdcovid_notification')) {
            wp_schedule_event(time(), 'daily', 'rdcovid_notification');
        }
    }

  /**
   * Activate the plugin
   *
   * @since 1.0.0
   * @return void
   */
    public function _activate()
    {
      // Make sure any rewrite functionality has been loaded.
      // flush_rewrite_rules();
      // register cron hook
        $this->register_scheduler();
        $this->register_notification();
    }

  /**
   * Deactivate the plugin
   * Uninstall routines should be in uninstall.php
   *
   * @since 1.0.0
   * @return void
   */
    public function _deactivate()
    {
      // cleanup data
        $this->transients->cleanup();

      // cleanup scheduler cron
        $timestamp = wp_next_scheduled('rdcovid_scheduler');
        wp_unschedule_event($timestamp, 'rdcovid_scheduler');

      // cleanup notification cron
        $timestamp = wp_next_scheduled('rdcovid_notification');
        wp_unschedule_event($timestamp, 'rdcovid_notification');
    }

  /**
   * Check if the plugin meets requirements and
   * disable it if they are not present.
   *
   * @since 1.0.0
   * @return boolean result of meets_requirements
   */
    public function check_requirements()
    {
      // bail early if pluginmeets requirements
        if ($this->meets_requirements()) {
            return true;
        }

      // Add a dashboard notice.
        add_action('all_admin_notices', array( $this, 'requirements_not_met_notice' ));

      // Deactivate our plugin.
        add_action('admin_init', array($this, 'deactivate_me'));
        return false;
    }

  /**
   * Deactivates this plugin, hook this function on admin_init.
   *
   * @since 1.0.0
   * @return void
   */
    public function deactivate_me()
    {
      // We do a check for deactivate_plugins before calling it, to protect
      // any developers from accidentally calling it too early and breaking things.
        if (function_exists('deactivate_plugins')) {
            deactivate_plugins($this->basename);
        }
    }

  /**
   * Check that all plugin requirements are met
   *
   * @since 1.0.0
   * @return boolean True if requirements are met.
   */
    public function meets_requirements()
    {
      // Do checks for required classes / functions
      // function_exists('') & class_exists('').
      // We have met all requirements.
      // Add detailed messages to $this->activation_errors array
        return true;
    }

  /**
   * Adds a notice to the dashboard if the plugin requirements are not met
   *
   * @since 1.0.0
   * @return void
   */
    public function requirements_not_met_notice()
    {
      // compile default message
        $default_message = sprintf(
            __('ReeZh Design Covid-19 Widget WordPress Plugin is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'rdcovid'),
            admin_url('plugins.php')
        );

      // default details to null
        $details = null;

      // add details if any exist
        if (!empty($this->activation_errors) && is_array($this->activation_errors)) {
            $details = '<small>' . implode('</small><br /><small>', $this->activation_errors) . '</small>';
        }

      // output errors
        echo '<div id="message" class="error"><p>' . $default_message . '</p>' . $details . '</div>';
    }

  /**
   * Magic getter for our object.
   *
   * @since 1.0.0
   * @param string $field Field to get.
   * @throws Exception Throws an exception if the field is invalid.
   * @return mixed
   */
    public function __get($field)
    {
        switch ($field) {
            case 'version':
              return self::VERSION;
            case 'basename':
            case 'url':
            case 'path':
            case 'transients':
            case 'dashboard':
            case 'widget':
            case 'announcement':
              return $this->$field;
            case 'extcache':
              return $this->is_external_cache();
            default:
              throw new Exception('Invalid ' . __CLASS__ . ' property: ' . $field);
        }
    }

  /**
   * Include a file from the includes directory
   *
   * @since 1.0.0
   * @param  string $filename Name of the file to be included.
   * @return bool   Result of include call.
   */
    public static function include_file($filename)
    {
        $file = self::dir($filename . '.php');
        if (file_exists($file)) {
            return include_once($file);
        }
        return false;
    }

  /**
   * This plugin's directory
   *
   * @since 1.0.0
   * @param  string $path (optional) appended path.
   * @return string       Directory and path
   */
    public static function dir($path = '')
    {
        static $dir;
        $dir = $dir ? $dir : trailingslashit(dirname(__FILE__));
        return $dir . $path;
    }

  /**
   * This plugin's url
   *
   * @since 1.0.0
   * @param  string $path (optional) appended path.
   * @return string       URL and path
   */
    public static function url($path = '')
    {
        static $url;
        $url = $url ? $url : trailingslashit(plugin_dir_url(__FILE__));
        return $url . $path;
    }

    /**
     * This plugin's languages
     *
     * @since 1.0.0
     * 
     */
    public static function load_languages() {
      load_plugin_textdomain( 'rdcovid', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

}

/**
 * Grab the RDCovid object and return it.
 * Wrapper for RDCovid::get_instance()
 *
 * @since  1.0.0
 * @return RDCovid Singleton instance of plugin class.
 */
function rdcovid19()
{
    return RDCovid::get_instance();
}

// Kick it off.
add_action('plugins_loaded', array( rdcovid19(), 'hooks' ));

register_activation_hook(__FILE__, array( rdcovid19(), '_activate' ));
register_deactivation_hook(__FILE__, array( rdcovid19(), '_deactivate' ));
