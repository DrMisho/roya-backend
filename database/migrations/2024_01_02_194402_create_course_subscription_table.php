<?php

use App\Models\Course;
use App\Models\Subscription;
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
        Schema::create('course_subscription', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Course::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Subscription::class)->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_subscription');
    }
};
