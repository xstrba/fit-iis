<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddDataToQuestion
 */
final class AddDataToQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('question', static function (Blueprint $table) {
            $table->softDeletes();
            $table->longText('text')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('question', static function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->string('text')->nullable()->change();
        });
    }
}
