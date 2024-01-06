<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\admin;

use SUPPostsDuplicate\abstracts\AbstractController;

/**
 * The controller that will be used to register the admin entities.
 */
class Controller extends AbstractController
{
	public function register_entities(): array
	{
		return [
			//OptionPage::class, // Not implemented yet
			DuplicatePost::class
		];
	}
}
