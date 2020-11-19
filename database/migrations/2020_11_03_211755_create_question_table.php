<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateQuestionTable
 */
final class CreateQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('question', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('group')->onDelete('cascade');
            $table->string('name');
            $table->string('text')->nullable();
            $table->integer('type')->default(\App\Enums\QuestionTypesEnum::OPTIONS);
            $table->integer('files_number')->default(0);
            $table->unsignedInteger('min_points')->default(0);
            $table->unsignedInteger('max_points')->default(1);
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
        Schema::dropIfExists('question');
    }
}
