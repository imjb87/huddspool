<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UseLongtextForAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('email_log') || ! Schema::hasColumn('email_log', 'attachments')) {
            return;
        }

        Schema::table('email_log', function (Blueprint $table) {
            $table->longText('attachments')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('email_log') || ! Schema::hasColumn('email_log', 'attachments')) {
            return;
        }

        Schema::table('email_log', function (Blueprint $table) {
            $table->text('attachments')->nullable()->change();
        });
    }
}
