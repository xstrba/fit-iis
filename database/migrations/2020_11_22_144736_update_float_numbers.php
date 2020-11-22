<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class UpdateFloatNumbers
 */
final class UpdateFloatNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // float is default of 2 decimal places and 10 at total

        Schema::table('question', static function (Blueprint $table) {
            $table->float('min_points')->default(0.00)->change();
            $table->float('max_points')->default(1.00)->change();
        });

        Schema::table('option', static function (Blueprint $table) {
            $table->float('points')->default(0.00)->change();
        });

        Schema::table('question_student', static function (Blueprint $table) {
            $table->float('points')->default(0.00)->change();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // to not lose data after changing ints to floats dont do anything here
    }
}
