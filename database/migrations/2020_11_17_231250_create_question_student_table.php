<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateQuestionStudentTable
 */
final class CreateQuestionStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('question_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('question')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('user')->onDelete('cascade');
            $table->longText('text')->nullable();
            $table->longText('notes')->nullable();
            $table->integer('points')->default(0);
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
        Schema::dropIfExists('question_student');
    }
}
