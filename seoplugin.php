<?php
namespace SEOPlugin;

/**
 * @package seo-plugin
 * @version 0.1.0
 *
 * Plugin Name: SEO Plugin
 * Plugin URI: https://github.com/easterncoder/seoplugin/
 * Description: SEO Plugin
 * Version: 0.1.0
 *
 * Author: Mike Lopez & Benj Arriola
 * Author URI: https://github.com/easterncoder/seoplugin/
 *
 * Requires at least: 5.2
 * Requires PHP: 7.3
 *
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * Text Domain: seo-plugin
 * Domain path: /languages
 */

defined( 'ABSPATH' ) || die();

/**
 * Plugin file path
 *
 * @var string
 */
const PLUGIN_FILE = __FILE__;
require_once 'constants.php';

require_once 'autoloader.php';

Core\WP_Hooks::initialize();
Core\Features::load();
