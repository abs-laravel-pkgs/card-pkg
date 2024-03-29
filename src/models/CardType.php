<?php

namespace Abs\CardPkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\Company;
use App\Config;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardType extends BaseModel {
	use SeederTrait;
	use SoftDeletes;
	protected $table = 'card_types';
	public $timestamps = true;
  protected $fillable = [
    'name',
    'display_order',
  ];
  protected $visible = [
    'name',
    'logo',
  ];

	public function logo() {
		return $this->belongsTo('Abs\BasicPkg\Models\Attachment', 'logo_id');
	}

	public static function createFromObject($record_data) {

		$errors = [];
		$company = Company::where('code', $record_data->company)->first();
		if (!$company) {
			dump('Invalid Company : ' . $record_data->company);
			return;
		}

		$admin = $company->admin();
		if (!$admin) {
			dump('Default Admin user not found');
			return;
		}

		$type = Config::where('name', $record_data->type)->where('config_type_id', 89)->first();
		if (!$type) {
			$errors[] = 'Invalid Tax Type : ' . $record_data->type;
		}

		if (count($errors) > 0) {
			dump($errors);
			return;
		}

		$record = self::firstOrNew([
			'company_id' => $company->id,
			'name' => $record_data->tax_name,
		]);
		$record->type_id = $type->id;
		$record->created_by_id = $admin->id;
		$record->save();
		return $record;
	}

}
