<?php

namespace gn36\hookup;

class ext extends \phpbb\extension\base
{

	/**
	 * An array of all defined notification types so they can be properly enabled/disabled
	 * @var array
	 */
	protected $notification_types = array(
		'gn36.hookup.notification.type.base',
		'gn36.hookup.notification.type.active_date_reset',
		'gn36.hookup.notification.type.active_date_set',
		'gn36.hookup.notification.type.date_added',
		'gn36.hookup.notification.type.date_added_rotation',
		'gn36.hookup.notification.type.invited',
		'gn36.hookup.notification.type.user_added',
	);

	/**
	 * List extra dependencies of this extensions not added to composer.json (e.g. because composer doesn't know them)
	 * @return multitype:string
	 */
	protected function extra_dependencies()
	{
		return array();
	}

	/**
	 * Split Version info up into known parts
	 * @param unknown $string
	 * @return array|boolean|NULL
	 */
	protected function split_version_info($string)
	{
		$pattern = '#(\^|~|>=|>|gt(?:;=?)?|ge|==?|eq|!=|<>|ne|<=|<|le|lt(?:;=?)?(?:gt;)?)([0-9._*-PLAHBETRCDV]+?)(?:$|,)#is';

		$matches = null;
		preg_match_all($pattern, $string, $matches);
		if (!$matches)
		{
			return false;
		}

		$ret = array();
		$ret['version']  = $matches[2];
		$ret['operator'] = $matches[1];
		$ret['operator'] = str_replace(array('gt;', 'lt;'), array('>', '<'), $ret['operator']);

		return $ret;
	}

	/**
	 * @see \phpbb\extension\base::is_enableable()
	 */
	function is_enableable()
	{
		$config 	= $this->container->get('config');
		$mgr  		= $this->container->get('ext.manager');
		$template 	= $this->container->get('template');

		$meta_mgr 	= $mgr->create_extension_metadata_manager($this->extension_name, $template);

		$meta = $meta_mgr->get_metadata();
		if (isset($meta['require']))
		{
			$require = $meta['require'];
		}
		else
		{
			$require = array();
		}

		if (isset($meta['extra']['soft_require']))
		{
			$require = array_merge($require, $meta['extra']['soft_require']);
		}

		$require = array_merge($require, $this->extra_dependencies());

		/** @var $user \phpbb\user */
		$user = $this->container->get('user');
		$user->add_lang_ext($this->extension_name, 'install');

		$dep_files_to_search = array();

		foreach ($require as $key => $value)
		{
			$info = $this->split_version_info($value);
			
			foreach ($info['version'] as $vkey => $version)
			{
				switch (strtolower($key))
				{
					case 'php':
						if (!phpbb_version_compare(PHP_VERSION, $version, $info['operator'][$vkey]))
						{
							trigger_error($user->lang('WRONG_PHP_VERSION') . adm_back_link(append_sid('index.' . $this->container->getParameter('core.php_ext'), 'i=acp_extensions&amp;mode=main')), E_USER_WARNING);
							return false;
						}
						break;
					case 'phpbb':
					case 'phpbb/phpbb':
						if (!phpbb_version_compare($config['version'], $version, $info['operator'][$vkey]))
						{
							trigger_error($user->lang('WRONG_PHPBB_VERSION') . adm_back_link(append_sid('index.' . $this->container->getParameter('core.php_ext'), 'i=acp_extensions&amp;mode=main')), E_USER_WARNING);
							return false;
						}
						break;
					case 'gn36/phpbb-oo-posting-api':
						$dep_files_to_search[$key] = __DIR__ . '/vendor/gn36/phpbb-oo-posting-api/src/Gn36/OoPostingApi/post.php';
						break;
					case 'eonasdan/bootstrap-datetimepicker':
						$dep_files_to_search[$key] = __DIR__ . '/vendor/eonasdan/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js';
						$dep_files_to_search[$key] = __DIR__ . '/vendor/moment/moment/min/moment.min.js';
						break;
					case 'components/bootstrap':
						$dep_files_to_search[$key] = __DIR__ . '/vendor/components/bootstrap/css/bootstrap.min.css';
						break;
					case 'fortawesome/font-awesome':
						$dep_files_to_search[$key] = __DIR__ . '/vendor/fortawesome/font-awesome/fonts/fontawesome-webfont.eot';
						break;
					case 'composer/package-versions-deprecated':
					case 'ocramius/proxy-manager':
						// Fallback case for correct error message
						$dep_files_to_search[$key] = __DIR__ . '/vendor/' . $key . '/composer.json';
						break;
					default:
						// First check if this is a dependency in the vendor dir that we missed above
						if (file_exists(__DIR__ . '/vendor/' . $key . '/composer.json'))
						{
							// Assume installed for simplicity
							break;
						}
						
						// Apparently not a vendor dir. This should be an extension as a requirement
						if (!$mgr->is_enabled($key))
						{
							trigger_error($user->lang('MISSING_EXTENSION', $key) . adm_back_link(append_sid('index.' . $this->container->getParameter('core.php_ext'), 'i=acp_extensions&amp;mode=main')), E_USER_WARNING);
							return false;
						}

						$ext_meta_mgr	= $mgr->create_extension_metadata_manager($key, $template);
						$ext_meta 		= $ext_meta_mgr->get_metadata();
						$ext_version 	= $ext_meta['version'];

						if (!phpbb_version_compare($ext_version, $version, $info['operator'][$vkey]))
						{
							trigger_error($user->lang('WRONG_EXTENSION_VERSION', $key) . adm_back_link(append_sid('index.' . $this->container->getParameter('core.php_ext'), 'i=acp_extensions&amp;mode=main')), E_USER_WARNING);
							return false;
						}
						break;
				}
			}
		}
		
		// Now check the files to search for:
		foreach ($dep_files_to_search as $key => $file)
		{
			if (!file_exists($file))
			{
				trigger_error($user->lang('MISSING_DEPENDENCIES', $key) . adm_back_link(append_sid('index.' . $this->container->getParameter('core.php_ext'), 'i=acp_extensions&amp;mode=main')), E_USER_WARNING);
				return false;
			}
		}

		// Apparently passed all checks
		return true;
	}

	/**
	 * @see \phpbb\extension\base::enable_step()
	 */
	public function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '':
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach ($this->notification_types as $notification_type)
				{
					$phpbb_notifications->enable_notifications($notification_type);
				}

				return 'notifications';
			break;
			default:
				return parent::enable_step($old_state);
			break;
		}
	}

	/**
	 * @see \phpbb\extension\base::disable_step()
	 */
	public function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '':
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach ($this->notification_types as $notification_type)
				{
					$phpbb_notifications->disable_notifications($notification_type);
				}
				return 'notifications';
			break;
			default:
				return parent::disable_step($old_state);
			break;
		}
	}

	/**
	 * @see \phpbb\extension\base::purge_step()
	 */
	public function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '':
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach ($this->notification_types as $notification_type)
				{
					$phpbb_notifications->purge_notifications($notification_type);
				}
				return 'notifications';
			break;
			default:
				return parent::purge_step($old_state);
			break;
		}
	}
}
