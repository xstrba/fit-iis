<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTestAssistantTable
 */
final class CreateTestAssistantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('test_assistant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('test')->onDelete('cascade');
            $table->foreignId('assistant_id')->constrained('user')->onDelete('cascade');
            $table->boolean('accepted')->default(false);
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
        Schema::dropIfExists('test_assistant');
    }
}
