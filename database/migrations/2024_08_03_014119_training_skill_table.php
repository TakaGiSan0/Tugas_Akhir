<?php

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
        Schema::create("training_skill", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("job_skill")->nullable();
            $table->string("skill_code")->nullable();
            $table->timestamps();
            $table->softDeletes();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
