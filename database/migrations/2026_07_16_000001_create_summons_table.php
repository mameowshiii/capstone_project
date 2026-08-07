<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('summons', function (Blueprint $table) {
            $table->id();
            $table->string('case_number', 100)->unique();
            $table->string('complainant_name', 255);
            $table->string('complainant_contact', 100)->nullable();
            $table->string('respondent_name', 255);
            $table->string('respondent_contact', 100)->nullable();
            $table->text('complain_details');
            $table->dateTime('schedule_date');
            $table->enum('status', ['pending', 'scheduled', 'resolved', 'cancelled'])->default('pending');
            $table->text('hearing_remarks')->nullable();
            $table->foreignId('complainant_resident_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->foreignId('respondent_resident_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('summons');
    }
};
