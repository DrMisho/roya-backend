<?php

use App\Http\Constants\Constant;
use App\Models\Package;
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
        Schema::create('cobons', function (Blueprint $table) {
            $table->id();
            $table->ulid('cobon', 255);
            $table->unsignedTinyInteger('status')->default(Constant::COBON_STATUS['معلق']);
            $table->foreignIdFor(Package::class)->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobons');
    }
};
