<?php

use App\Enum\InternshipRequestStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('internship_requests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->smallInteger('country_code');
            $table->string('phone_number');
            $table->string('cv');
            $table->enum('status', [InternshipRequestStatus::ACCEPTED->value, InternshipRequestStatus::PENDING->value, InternshipRequestStatus::REJECTED->value])->default(InternshipRequestStatus::PENDING->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_requests');
    }
};
