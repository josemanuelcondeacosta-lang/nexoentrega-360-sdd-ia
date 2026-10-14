<?php

namespace App\Modules\Audit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Audit
{
    public static function record($user, string $resource, string $action, ?string $before, ?string $after): void
    {
        DB::table('audit_events')->insert(['id' => (string) Str::uuid(), 'organization_id' => $user->organization_id, 'actor_id' => $user->id,
            'resource_id' => $resource, 'action' => $action, 'before_state' => $before, 'after_state' => $after, 'occurred_at' => now(),
            'correlation_id' => request()->attributes->get('correlationId', (string) Str::uuid())]);
    }
}
