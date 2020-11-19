<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddSignedToQuestion
 */
final class AddSignedToQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('question', function (Blueprint $table) {
            $table->integer('min_points')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('question', function (Blueprint $table) {
            $table->unsignedInteger('min_points')->default(0)->change();
        });
    }
}