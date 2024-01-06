<?php

namespace SUPPostsDuplicate;

use SUPPostsDuplicate\abstracts\AbstractBasePlugin;
use SUPPostsDuplicate\admin\Controller as AdminController;

class Plugin extends AbstractBasePlugin
{
	public function register_components(): array
	{
		return [
			Localization::class,
			AdminController::class,
		];
	}

}
