<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('timestamps');
            $table->boolean('can_delete')->default(true)->after('is_default');
            $table->softDeletes()->after('can_delete');//
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('is_default');
            $table->dropColumn('can_delete');
            $table->softDeletes();
        });
    }
};
