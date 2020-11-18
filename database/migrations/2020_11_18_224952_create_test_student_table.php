<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTestStudentTable
 */
final class CreateTestStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('test_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('test')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('user')->onDelete('cascade');
            $table->boolean('accepted')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_student');
    }
}
