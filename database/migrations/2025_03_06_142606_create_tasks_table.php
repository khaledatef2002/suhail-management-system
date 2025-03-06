<?php

use App\Enum\TaskStatus;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamp('due_date');
            $table->enum('status', [TaskStatus::NEW->value, TaskStatus::REVIEW->value, TaskStatus::WORKING->value, TaskStatus::FEEDBACK->value, TaskStatus::DONE->value])->default(TaskStatus::NEW->value);
            $table->unsignedBigInteger('assignee_id')->index();
            $table->foreign('assignee_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('creator_id')->index();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
