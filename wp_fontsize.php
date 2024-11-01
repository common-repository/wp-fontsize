<?php 
/*
Plugin Name: WordPress FontSize Adjust 
Plugin URI: http://mycircletree.com/
Description: Adds Stateful Fontsize Adjustment Classes to the body of the Website 
Author: Circle Tree, LLC
Version: 0.7
Author URI: http://mycircletree.com/
*/ 
defined ('WP_FONTSIZE_CSS') or define('WP_FONTSIZE_CSS', true);
defined ('WP_FONTSIZE_HTML') or define('WP_FONTSIZE_HTML', true);
defined ('WP_FONTSIZE_MIN') or define('WP_FONTSIZE_MIN', 1);
defined ('WP_FONTSIZE_DEFAULT') or define('WP_FONTSIZE_DEFAULT', 3);
defined ('WP_FONTSIZE_MAX') or define('WP_FONTSIZE_MAX', 5);
class wp_fontsize {
	const SESSION_KEY = 'wp_fontsize';
	const MAX_SIZE = WP_FONTSIZE_MAX;
	const DEFAULT_SIZE  = WP_FONTSIZE_DEFAULT;
	const MIN_SIZE = WP_FONTSIZE_DEFAULT;
	private $font_size;
	function  __construct () {
		$this->font_size = self::DEFAULT_SIZE;
		if ( ! isset($_SESSION )) 
			session_start();
		if ( ! isset( $_SESSION[ self::SESSION_KEY ] ) ) 
			$this->setFontSize(self::DEFAULT_SIZE); 
		add_action('init', array($this, 'init'));
		add_action('wp_ajax_fontsize', array($this, 'ajax'));
		add_action('wp_ajax_nopriv_fontsize', array($this, 'ajax'));
	}
	function  init () {
		wp_enqueue_script('jquery');
		add_action('wp_footer', array($this, 'footer'));
		add_filter('body_class', array($this, 'body_class'));
	}
	function  ajax () {
		check_ajax_referer('wp_fontsize', 'nonce');
		$size = (int) $_REQUEST['size'];
		echo $this->setFontSize($size);
		die;
	}
	static public function  min ($string) {
		return str_replace(array("\t","\r\n", "\n", "\r","\t"), '', $string);
	}
	/**
	 * Sets the body class for adjusting font size
	 * @param int $index font size index class number
	 */
	public function setFontSize($index) {
		if ($index > self::MAX_SIZE) $index = self::MAX_SIZE;
		elseif ($index < self::MIN_SIZE) $index = self::MIN_SIZE; 
		$_SESSION[ self::SESSION_KEY ] = $index;
		return $index;
	}
	public function getFontSize () {
		return $_SESSION[self::SESSION_KEY];
	}
	function  body_class ($classes_array) {
		$classes_array[] = 'font-size-' . $this->getFontSize();
		return $classes_array;
	}
	private function css () {  ?>
		<style>
			.wp_fontsize_wrapper
			{
				position: fixed;
				right: 0px;
				top: 0px;
				-moz-border-radius: 0 0 0 3px;
				-webkit-border-radius: 0 0 0 3px;
				-khtml-border-radius: 0 0 0 3px;
				border-radius: 0 0 0 3px;
				width: auto;
				height: auto;
				background-color: #FFFFFF;
				overflow: hidden;
				padding-left: 5px;
				padding-right: 2.3em;
				line-height: 2;
			}
			.admin-bar .wp_fontsize_wrapper {
				top: 32px;
			}
			.wp_fontsize_wrapper LABEL
			{
				font-size: 10pt;
			}
			.admin-bar .wp_fontsize_wrapper
			{
				top: 30px;
			}
			.wp_fontsize_wrapper UL
			{
				list-style: none;
				position: absolute;
				right: 0px;
				top: 0px;
				margin: 0;
				padding: 0;
			}
			.wp_fontsize_wrapper UL LI
			{
				display: inline-block;
				padding: 0 0.3em;
				float: left;
				position: relative;
				left: 0px;
				top: 0.2em;
				cursor: pointer;
				line-height: 1.5;
				font-size: 12pt;
			}
			.wp_fontsize_wrapper UL LI:hover
			{
				background-color: #CDCDCD;
				-moz-border-radius: 5px;
				-webkit-border-radius: 5px;
				-khtml-border-radius: 5px;
				border-radius: 5px;
			}
		</style>
	<?php }
	public static function html () {  ?>
		<div class="wp_fontsize_wrapper" id="wp_fontsize_wrapper">
		<label title="<?php _e('Adjust the font size of the website', 'wp_fontsize')?>"><?php _e('Font Size:', 'wp_fontsize');?></label>
			<ul>
				<li id="wpFontMinus" class="minus"><?php _e('-', 'wp_fontsize')?></li>
				<li id="wpFontPlus" class="plus"><?php _e('+', 'wp_fontsize');?></li>
			</ul>
		</div>
	<?php }
	private function js () { 
		$js_vars = array(
				'ajaxurl'=>admin_url('admin-ajax.php'),
				'fontsize' => $this->getFontSize(),
				'nonce' => wp_create_nonce('wp_fontsize'),
				'min' => self::MIN_SIZE,
				'max' => self::MAX_SIZE,
				'default' => self::DEFAULT_SIZE,
			);
		$json_data = json_encode($js_vars); 
		?>
		<script>
		/* <![CDATA[ */
			var wp_fontsize = <?php echo $json_data; ?>,
			current_size = wp_fontsize.fontsize;
		/* ]]> */
		jQuery(function($) {
			<?php //A named function is used in place of jQuery.ajaxSetup to avoid plugin conflicts; ?>
			function do_ajax (operator) {
				var new_size = current_size;
				if (operator == 'plus')
					new_size++;
				else if (operator == 'minus')
					new_size--;
				$.ajax({
	  				url: wp_fontsize.ajaxurl,
	  				global: false,
	  				data: {
	  					action: "fontsize",
	  					size: new_size,
	  					nonce: wp_fontsize.nonce
					},
					success: function  (data) {
						new_size = parseInt(data);
						$("body").removeClass("font-size-" + current_size).addClass("font-size-" + new_size);
						current_size = new_size;
					}
				});
			};
  			$("#wpFontMinus").click(function  () {
  				do_ajax('minus');
  				return false;
			});
  			$("#wpFontPlus").click(function  () {
  				do_ajax('plus');
  				return false;
			});
		});
		</script>	
	<?php } 
	function  footer () {
		ob_start(array($this, 'min'));
		if ( WP_FONTSIZE_HTML )
			$this->html();
		if ( WP_FONTSIZE_CSS )
			$this->css();
		$this->js();
		ob_end_flush();
	}
}
new wp_fontsize();