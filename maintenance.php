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
		// load config
		$this->load_config();
		$this->maint_start = $this->rc->config->get('maintenance_start');
		$this->maint_end = $this->rc->config->get('maintenance_end');
		$maint_pre = $this->rc->config->get('maintenance_pre');

		// calculate timeframe
		$now = time();

		$this->is_maint = (($now >= $this->maint_start) && ($now <= $this->maint_end));
		$this->upcoming_maint = (($now >= $this->maint_start - $maint_pre) && ($now < $this->maint_start));

		$this->api->output->set_env('maintenance_is_maint', $this->is_maint);

		if ($this->is_maint || $this->upcoming_maint)
		{
			$this->include_script('maintenance.js');
			$this->add_hook('template_object_message', array($this, 'addMaintBanner'));
			$this->add_texts('localization/', true);
		}
	}

	function addMaintBanner($p) {
		$text = array(
			'name' => ($this->is_maint) ? 'maintenanceBanner' : 'preMaintenanceBanner',
			'vars' => array(
				'start' => $this->rc->format_date($this->maint_start),
				'end' => $this->rc->format_date($this->maint_end)
			),
		);
		$p['content'] = '<div id="maintbanner" style="text-align:center; background-color:#F9EDBE;">'.$this->gettext($text).'</div>'.$p['content'];
		return $p;
	}
}
