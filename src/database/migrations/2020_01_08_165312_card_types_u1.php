<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CardTypesU1 extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		//
		Schema::table('card_types', function (Blueprint $table) {
			$table->unsignedInteger('logo_id')->nullable()->after('display_order');;
			$table->foreign('logo_id')->references('id')->on('attachments')->onDelete('SET NULL')->onUpdate('cascade');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		//
	}
}
