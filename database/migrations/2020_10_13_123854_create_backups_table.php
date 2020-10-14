<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sculptor\Agent\Enums\BackupStatusType;
use Sculptor\Agent\Enums\BackupType;

class CreateBackupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default(BackupType::DATABASE);
            $table->string('cron')->default(BACKUP_CRON);
            $table->string('path')->nullable();
            $table->string('destination')->nullable();
            $table->string('status')->default(BackupStatusType::NEVER);
            $table->string('error')->nullable();
            $table->dateTime('run')->nullable();
            $table->unsignedInteger('rotate')->default(BACKUP_ROTATE);
            $table->unsignedInteger('database_id')->nullable();
            $table->unsignedInteger('domain_id')->nullable();
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
        Schema::dropIfExists('backups');
    }
}
