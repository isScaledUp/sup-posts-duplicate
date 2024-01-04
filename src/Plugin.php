<?php

namespace SUPPostsDuplicate;

use SUPPostsDuplicate\abstracts\AbstractBasePlugin;
use SUPPostsDuplicate\admin\DuplicatePost;
use SUPPostsDuplicate\admin\OptionPage;

class Plugin extends AbstractBasePlugin
{
	public function register_components(): array
	{
		return [
			OptionPage::class,
			DuplicatePost::class
		];
	}

}
