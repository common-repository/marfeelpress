<?php

namespace Base\Entities\Checks;

class Softchecks {

	/** @var string */
	public $tenant;

	/** @var string */
	public $pluginVersion;

	/** @var string */
	public $phpVersion;

	/** @var string */
	public $wordpressVersion;

	/** @var boolean */
	public $hasWordPressMinVersion;

	/** @var boolean */
	public $hasPhpMinVersion;

	/** @var boolean */
	public $hasXmlLib;

	/** @var boolean */
	public $hasAdsTxtWriteAccess;

	/** @var boolean */
	public $hasAdsTxtFile;

	/** @var boolean */
	public $hasCachePlugin;

	/** @var boolean */
	public $allCachePluginAreSupported;

	/** @var boolean */
	public $hasCachePluginNeedingFix;

	/** @var integer */
	public $numPosts;

	/** @var integer */
	public $numSections;

	/** @var array */
	public $postsByMonth;

	/** @var array */
	public $postsBySection;

	/** @var array */
	public $postsByAuthor;

	/** @var array */
	public $plugins;

	/** @var array */
	public $blacklistedCategories;

	/** @var array */
	public $incompatiblePlugins;

	/** @var bool */
	public $hasIncompatiblePlugins;
}
