<?php
/**
 * Maintenance banner
 *
 * Plugin to announce maintenance periods
 *
 * @version 1.0
 * @author duk3luk3
 * @url http://roundcube.net/plugins/maintenance
 */
class maintenance extends rcube_plugin
{
	private $rc;
	private $is_maint = false;
	private $upcoming_maint = false;
	private $maint_start = 0;
	private $maint_end = 0;

	function init()
	{
		$this->rc = rcmail::get_instance();

		if (!$this->rc || !$this->rc->output) {
			return;
		}
		// load config
		$this->load_config();
		$this->maint_start = $this->rc->config->get('maintenance_start');
		$this->maint_end = $this->rc->config->get('maintenance_end');
		$maint_pre = $this->rc->config->get('maintenance_pre');
		$this->maint_light = $this->rc->config->get('maintenance_light');
		// calculate timeframe
		$now = time();

		$this->is_maint = (($now >= $this->maint_start) && ($now <= $this->maint_end));
		$this->upcoming_maint = (($now >= $this->maint_start - $maint_pre) && ($now < $this->maint_start));

		$this->rc->output->set_env('maintenance_is_maint', $this->is_maint && !$this->maint_light);
		$this->rc->output->set_env('maintenance_maint_light', $this->maint_light);

		if ($this->is_maint || $this->upcoming_maint)
		{
			$this->add_texts('localization/', true);

			if ($this->rc->task == 'login' || $this->rc->task == 'logout')
			{
				$this->include_script('maintenance.js');
				$this->include_stylesheet($this->local_skin_path() .'/maintenance.css');
				$this->add_hook('template_object_message', array($this, 'addMaintBanner'));
			}
			else if (!$this->maint_light)
			{
				if (!isset($_SESSION['maintenance_banner_seen']) || !$_SESSION['maintenance_banner_seen'])
				{
					$this->rc->output->show_message($this->getMaintBannerText());
					$_SESSION['maintenance_banner_seen'] = true;
				}
			}
		}
	}

	function getMaintBannerText()
	{
		$text = array(
			'name' => ($this->is_maint) ? 'maintenanceBanner' : 'preMaintenanceBanner',
			'vars' => array(
				'start' => $this->rc->format_date($this->maint_start),
				'end' => $this->rc->format_date($this->maint_end)
			),
		);
		if ($this->maint_light) {
			$text['name'] .= 'Light';
		}
		return $this->gettext($text);
	}

	function addMaintBanner($p) {
		$text = $this->getMaintBannerText();
		$p['content'] = '<div id="maintbanner">'.$text.'</div>'.$p['content'];
		return $p;
	}
}
