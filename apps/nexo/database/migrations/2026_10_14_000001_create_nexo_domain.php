<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('name');
        });
        Schema::table('users', function (Blueprint $t) {
            $t->string('organization_id')->references('id')->on('organizations');
            $t->string('role');
        });
        Schema::create('inventory_items', function (Blueprint $t) {
            $t->string('organization_id')->references('id')->on('organizations');
            $t->string('sku');
            $t->boolean('active')->default(true);
            $t->integer('fisico');
            $t->integer('reservado')->default(0);
            $t->primary(['organization_id', 'sku']);
        });
        Schema::create('requests', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('organization_id')->references('id')->on('organizations');
            $t->foreignId('solicitante_id')->constrained('users');
            $t->string('codigo');
            $t->string('estado')->default('BORRADOR');
            $t->string('zona');
            $t->string('prioridad');
            $t->timestampTz('fecha_requerida');
            $t->text('observaciones')->nullable();
            $t->timestampsTz();
            $t->unique(['organization_id', 'codigo']);
            $t->unique(['id', 'organization_id']);
        });
        Schema::create('request_lines', function (Blueprint $t) {
            $t->uuid('request_id');
            $t->string('organization_id');
            $t->string('sku');
            $t->integer('cantidad');
            $t->primary(['request_id', 'sku']);
            $t->foreign(['request_id', 'organization_id'])->references(['id', 'organization_id'])->on('requests');
            $t->foreign(['organization_id', 'sku'])->references(['organization_id', 'sku'])->on('inventory_items');
        });
        Schema::create('audit_events', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('organization_id');
            $t->foreignId('actor_id')->constrained('users');
            $t->string('resource_id');
            $t->string('action');
            $t->string('before_state')->nullable();
            $t->string('after_state')->nullable();
            $t->timestampTz('occurred_at');
            $t->uuid('correlation_id');
            $t->index(['organization_id', 'resource_id', 'occurred_at']);
        });
        Schema::create('idempotency_records', function (Blueprint $t) {
            $t->id();
            $t->string('organization_id');
            $t->foreignId('user_id')->constrained('users');
            $t->string('operation');
            $t->string('idempotency_key', 128);
            $t->string('request_hash', 64);
            $t->integer('response_status');
            $t->json('response_body');
            $t->timestampTz('created_at');
            $t->timestampTz('expires_at');
            $t->unique(['organization_id', 'user_id', 'operation', 'idempotency_key'], 'idempotency_scope');
        });
        Schema::create('ai_summaries', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('organization_id');
            $t->foreignId('coordinador_id')->constrained('users');
            $t->json('payload');
            $t->string('status')->default('BORRADOR');
            $t->foreignId('approved_by')->nullable()->constrained('users');
            $t->timestampTz('approved_at')->nullable();
            $t->timestampTz('created_at');
        });
        Schema::create('ai_attempts', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('summary_id')->references('id')->on('ai_summaries');
            $t->string('result');
            $t->timestampTz('created_at');
        });
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE inventory_items ADD CONSTRAINT stock_nonnegative CHECK (fisico >= 0 AND reservado >= 0 AND reservado <= fisico)');
            DB::statement('ALTER TABLE request_lines ADD CONSTRAINT valid_quantity CHECK (cantidad BETWEEN 1 AND 500)');
            DB::statement("ALTER TABLE requests ADD CONSTRAINT valid_state CHECK (estado IN ('BORRADOR','ENVIADA','RESERVADA','DESPACHADA','ENTREGADA','CANCELADA'))");
            DB::statement("CREATE OR REPLACE FUNCTION reject_audit_mutation() RETURNS trigger AS $$ BEGIN RAISE EXCEPTION 'audit append only'; END; $$ LANGUAGE plpgsql");
            DB::statement('CREATE TRIGGER audit_immutable BEFORE UPDATE OR DELETE ON audit_events FOR EACH ROW EXECUTE FUNCTION reject_audit_mutation()');
        }
    }

    public function down(): void
    {
        foreach (['ai_attempts', 'ai_summaries', 'idempotency_records', 'audit_events', 'request_lines', 'requests', 'inventory_items'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['organization_id', 'role']));
        Schema::dropIfExists('organizations');
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP FUNCTION IF EXISTS reject_audit_mutation()');
        }
    }
};
