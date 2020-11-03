<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTestTable
 */
final class CreateTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('test', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('user')->onDelete('cascade');
            $table->foreignId('assistant_id')->nullable()->constrained('user')->onDelete('SET NULL');
            $table->string('subject', 4);
            $table->string('name');
            $table->string('description')->nullable();
            $table->dateTime('start_date');
            $table->unsignedInteger('time_limit');
            $table->unsignedInteger('questions_number');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('test');
    }
}
