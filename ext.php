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
		//$pattern = '#(>=|>|ge|gt|==?|eq|!=|<>|ne|<=|<|le|lt)([0-9._*-PLAHBETRCDV]*?)$#is';
		$pattern = '#(>=|>|gt(?:;=?)?|ge|==?|eq|!=|<>|ne|<=|<|le|lt(?:;=?)?(?:gt;)?)([0-9._*-PLAHBETRCDV]+?)$#is';

		$matches = null;
		preg_match($pattern, $string, $matches);
		if (!$matches)
		{
			return false;
		}

		$ret = array(
			'version' => $matches[2],
			'operator' => str_replace(array('gt;', 'lt;'), array('>', '<'), $matches[1]),
		);

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

		foreach ($require as $key => $value)
		{
			$info = $this->split_version_info($value);

			switch (strtolower($key))
			{
				case 'php':
					if (!phpbb_version_compare(PHP_VERSION, $info['version'], $info['operator']))
					{
						return false;
					}
					break;
				case 'phpbb':
				case 'phpbb/phpbb':
					if (phpbb_version_compare($config['version'], $info['version'], $info['operator']))
					{
						return false;
					}
					break;
				case 'gn36/phpbb-oo-posting-api':
					if (!file_exists(__DIR__ . '/vendor/gn36/phpbb-oo-posting-api/src/Gn36/OoPostingApi/post.php'))
					{
						return false;
					}
					break;
				default:
					// This should be an extension as a requirement
					if (!$mgr->is_enabled($key))
					{
						return false;
					}

					$ext_meta_mgr	= $mgr->create_extension_metadata_manager($key, $template);
					$ext_meta 		= $ext_meta_mgr->get_metadata();
					$ext_version 	= $ext_meta['version'];

					if (!phpbb_version_compare($ext_version, $info['version'], $info['operator']))
					{
						return false;
					}
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
