<?php

namespace Abs\CardPkg;
use Abs\Basic\Attachment;
use Abs\CardPkg\CardType;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use File;
use Illuminate\Http\Request;
use Validator;
use Yajra\Datatables\Datatables;

class CardTypeController extends Controller {

	private $company_id;
	public function __construct() {
		$this->data['theme'] = config('custom.admin_theme');
		$this->company_id = config('custom.company_id');
	}

	public function getCardTypes(Request $request) {
		$this->data['card_types'] = CardType::
			select([
			'card_types.question',
			'card_types.answer',
		])
			->where('card_types.company_id', $this->company_id)
			->orderby('card_types.display_order', 'asc')
			->get()
		;
		$this->data['success'] = true;

		return response()->json($this->data);

	}

	public function getCardTypeList(Request $request) {
		$card_types = CardType::withTrashed()
			->select([
				'card_types.*',
				DB::raw('IF(card_types.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('card_types.company_id', $this->company_id)
		/*->where(function ($query) use ($request) {
				if (!empty($request->question)) {
					$query->where('card_types.question', 'LIKE', '%' . $request->question . '%');
				}
			})*/
			->orderby('card_types.id', 'desc');

		return Datatables::of($card_types)
			->addColumn('name', function ($card_types) {
				$status = $card_types->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $card_types->name;
			})
			->addColumn('action', function ($card_types) {
				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img1_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow-active.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img_delete_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-active.svg');
				$output = '';
				$output .= '<a href="#!/card-pkg/card-type/edit/' . $card_types->id . '" id = "" ><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1_active . '" onmouseout=this.src="' . $img1 . '"></a>
					<a href="javascript:;" data-toggle="modal" data-target="#card-type-delete-modal" onclick="angular.element(this).scope().deleteCardType(' . $card_types->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete_active . '" onmouseout=this.src="' . $img_delete . '"></a>
					';
				return $output;
			})
			->make(true);
	}

	public function getCardTypeFormData(Request $r) {
		$id = $r->id;
		if (!$id) {
			$card_type = new CardType;
			$attachment = new Attachment;
			$action = 'Add';
		} else {
			$card_type = CardType::withTrashed()->find($id);
			$attachment = Attachment::where('id', $card_type->logo_id)->first();
			$action = 'Edit';
		}
		$this->data['card_type'] = $card_type;
		$this->data['attachment'] = $attachment;
		$this->data['action'] = $action;
		$this->data['theme'];

		return response()->json($this->data);
	}

	public function saveCardType(Request $request) {
		//dd($request->all());
		try {
			$error_messages = [
				'name.required' => 'Name is Required',
				'name.unique' => 'Name is already taken',
				'delivery_time.required' => 'Delivery Time is Required',
				'charge.required' => 'Charge is Required',
			];
			$validator = Validator::make($request->all(), [
				'name' => [
					'required:true',
					'unique:card_types,name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'delivery_time' => 'required',
				'charge' => 'required',
				'logo_id' => 'mimes:jpeg,jpg,png,gif,ico,bmp,svg|nullable|max:10000',
			], $error_messages);
			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();
			if (!$request->id) {
				$card_type = new CardType;
				$card_type->created_by_id = Auth::user()->id;
				$card_type->created_at = Carbon::now();
				$card_type->updated_at = NULL;
			} else {
				$card_type = CardType::withTrashed()->find($request->id);
				$card_type->updated_by_id = Auth::user()->id;
				$card_type->updated_at = Carbon::now();
			}
			$card_type->fill($request->all());
			$card_type->company_id = Auth::user()->company_id;
			if ($request->status == 'Inactive') {
				$card_type->deleted_at = Carbon::now();
				$card_type->deleted_by_id = Auth::user()->id;
			} else {
				$card_type->deleted_by_id = NULL;
				$card_type->deleted_at = NULL;
			}
			$card_type->save();

			if (!empty($request->logo_id)) {
				if (!File::exists(public_path() . '/themes/' . config('custom.admin_theme') . '/img/card_type_logo')) {
					File::makeDirectory(public_path() . '/themes/' . config('custom.admin_theme') . '/img/card_type_logo', 0777, true);
				}

				$attacement = $request->logo_id;
				$remove_previous_attachment = Attachment::where([
					'entity_id' => $request->id,
					'attachment_of_id' => 20,
				])->first();
				if (!empty($remove_previous_attachment)) {
					$remove = $remove_previous_attachment->forceDelete();
					$img_path = public_path() . '/themes/' . config('custom.admin_theme') . '/img/card_type_logo/' . $remove_previous_attachment->name;
					if (File::exists($img_path)) {
						File::delete($img_path);
					}
				}
				$random_file_name = $card_type->id . '_card_type_file_' . rand(0, 1000) . '.';
				$extension = $attacement->getClientOriginalExtension();
				$attacement->move(public_path() . '/themes/' . config('custom.admin_theme') . '/img/card_type_logo', $random_file_name . $extension);

				$attachment = new Attachment;
				$attachment->company_id = Auth::user()->company_id;
				$attachment->attachment_of_id = 20; //User
				$attachment->attachment_type_id = 40; //Primary
				$attachment->entity_id = $card_type->id;
				$attachment->name = $random_file_name . $extension;
				$attachment->save();
				$card_type->logo_id = $attachment->id;
				$card_type->save();
			}

			DB::commit();
			if (!($request->id)) {
				return response()->json([
					'success' => true,
					'message' => 'Card Added Successfully',
				]);
			} else {
				return response()->json([
					'success' => true,
					'message' => 'Card Updated Successfully',
				]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'error' => $e->getMessage(),
			]);
		}
	}

	public function deleteCardType(Request $request) {
		DB::beginTransaction();
		try {
			CardType::withTrashed()->where('id', $request->id)->forceDelete();
			Attachment::where('attachment_of_id', 20)->where('entity_id', $request->id)->forceDelete();
			DB::commit();
			return response()->json(['success' => true, 'message' => 'Card Deleted Successfully']);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}
}
