<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateUsersTable
 */
final class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('nickname')->unique();
            $table->integer('role')->default(\App\Enums\RolesEnum::ROLE_STUDENT);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('birth');
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->default('CZ');
            $table->string('gender', 6)->default(\App\Enums\GendersEnum::MALE);
            $table->string('phone')->nullable();
            $table->string('language', 2)->default(\App\Enums\LanguagesEnum::CZ);
            $table->rememberToken();
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
        Schema::dropIfExists('user');
    }
}
