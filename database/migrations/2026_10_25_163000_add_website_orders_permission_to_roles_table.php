<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $websiteOrdersPermissions = [
            'index' => true,
            'details' => true,
            'updateDetails' => true,
            'approve' => true,
            'updateItem' => true,
            'addItem' => true,
            'removeItem' => true,
            'addNote' => true,
            'show' => true,
            'update-status' => true,
            'quick-status' => true,
            'quick-note' => true,
            'bulk-action' => true,
            'dispatch-courier' => true,
            'destroy' => true,
            'print' => true,
            'convert' => true,
            'send-to-online' => true,
            'bulk-convert' => true,
        ];

        // Roles to grant full website-orders permissions: Admin, Super Admin, Moderator
        $roles = Role::whereIn('id', [1, 2, 4])
            ->orWhereIn('slug', ['admin', 'super-admin', 'moderator'])
            ->get();

        foreach ($roles as $role) {
            $permissions = json_decode($role->permission, true) ?: [];
            if (!isset($permissions['website-orders']) || empty($permissions['website-orders'])) {
                $permissions['website-orders'] = $websiteOrdersPermissions;
                $role->permission = json_encode($permissions);
                $role->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
