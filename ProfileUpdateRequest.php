<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // default allow — change if you have custom checks
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge([
                'email' => Str::lower($this->input('email')),
            ]);
        }

        // Normalize role if coming with spaces / slashes (e.g. "Partner/ Franchise")
        if ($this->has('role')) {
            $role = $this->input('role');
            // convert common separators to single underscore
            $roleClean = Str::of($role)->lower()->replaceMatches('/[\/\s]+/', '_')->__toString();
            // remove unsafe chars (keep a-z0-9 and underscore)
            $roleClean = preg_replace('/[^a-z0-9_]/', '', $roleClean);
            $this->merge(['role' => $roleClean]);
        }
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        // Try to build allowed roles from DB (preferred), then config, then fallback
        $allowedRoles = [];

        try {
            // ensure roles table exists before trying to pluck
            if (Schema::hasTable('roles') && class_exists(Role::class)) {
                $allowedRoles = Role::pluck('name')->map(function ($r) {
                    return trim((string) $r);
                })->filter()->values()->all();
            }
        } catch (\Throwable $e) {
            Log::warning('ProfileUpdateRequest: failed to load roles from DB: ' . $e->getMessage());
            $allowedRoles = [];
        }

        // If DB didn't provide roles, try an explicit config (if present)
        if (empty($allowedRoles)) {
            $configured = config('roles.list', null);
            if (is_array($configured) && count($configured)) {
                $allowedRoles = array_values(array_unique(array_map(function ($r) {
                    return trim((string) $r);
                }, $configured)));
            }
        }

        // final fallback — known roles (existing 15 + 3 extras)
        if (empty($allowedRoles)) {
            $allowedRoles = [
                'superadmin','admin','teacher','training','client','hr','finance','student',
                'intern','volunteer','donor','corporate','exam','usermgmt','service',
                'partner','franchise','consultant','affiliate'
            ];
        }

        // Build role rule: if allowedRoles is available use Rule::in, otherwise skip it.
        // (We always have a fallback above, but keep this robust in case someone empties the list)
        $roleRules = ['nullable', 'string', 'max:50'];
        if (!empty($allowedRoles)) {
            // ensure values are strings
            $roleRules[] = Rule::in($allowedRoles);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            // Accept role only if it exists in allowedRoles (DB/config/fallback).
            // This prevents unexpected role values from being saved unintentionally.
            'role' => $roleRules,
        ];
    }
}
