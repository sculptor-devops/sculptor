<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sculptor\Agent\Enums\CertificatesTypes;
use Sculptor\Agent\Enums\DomainType;
use Sculptor\Agent\Enums\VersionControlType;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('alias')->nullable();
            $table->string('type')->default(DomainType::LARAVEL);
            $table->string('certificate')->default(CertificatesTypes::SELF_SIGNED);

            $table->string('user')->default(SITES_USER);
            $table->string('home')->default(SITES_PUBLIC);

            $table->string('deployer')->nullable();
            $table->string('vcs_tye')->default(VersionControlType::GIT);
            $table->string('vcs')->nullable();

            $table->unsignedInteger('database_id')->nullable();
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
        Schema::dropIfExists('domains');
    }
}
