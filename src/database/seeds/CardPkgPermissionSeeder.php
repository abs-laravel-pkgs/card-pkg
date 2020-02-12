<?php
namespace Abs\CardPkg\Database\Seeds;

use App\Permission;
use Illuminate\Database\Seeder;

class CardPkgPermissionSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$permissions = [
			//FAQ
			[
				'display_order' => 99,
				'parent' => null,
				'name' => 'card-types',
				'display_name' => 'Card Types',
			],
			[
				'display_order' => 1,
				'parent' => 'card-types',
				'name' => 'add-card-type',
				'display_name' => 'Add',
			],
			[
				'display_order' => 2,
				'parent' => 'card-types',
				'name' => 'delete-card-type',
				'display_name' => 'Edit',
			],
			[
				'display_order' => 3,
				'parent' => 'card-types',
				'name' => 'delete-card-type',
				'display_name' => 'Delete',
			],

		];
		Permission::createFromArrays($permissions);
	}
}