<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Images;
use App\Support\RichText;
use App\Support\SettingGroups;
use Illuminate\Http\Request;

/**
 * Une page par groupe de réglages (accueil, ambassadeur, contacts, site),
 * construite à partir de App\Support\SettingGroups.
 */
class SettingController extends Controller
{
    public function edit(string $group)
    {
        $definition = $this->definition($group);

        return view('admin.settings.edit', [
            'current' => 'settings-' . $group,
            'group' => $group,
            'definition' => $definition,
            'values' => Setting::group($group),
        ]);
    }

    public function update(Request $request, string $group)
    {
        $fields = SettingGroups::fields($group);
        $this->definition($group);

        $request->validate(collect($fields)->mapWithKeys(fn ($field, $key) => [$key => match ($field['type']) {
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:8192'],
            'url' => ['nullable', 'url:https,http', 'max:500'],
            'email' => ['nullable', 'email', 'max:150'],
            'boolean' => ['nullable', 'boolean'],
            'text' => ['nullable', 'string', 'max:255'],
            default => ['nullable', 'string', 'max:20000'],
        }])->all());

        foreach ($fields as $key => $field) {
            $settingKey = "{$group}.{$key}";

            match ($field['type']) {
                'image' => $this->saveImage($request, $key, $settingKey, $field),
                'boolean' => Setting::set($settingKey, $request->boolean($key) ? '1' : '0'),
                'rich' => Setting::set($settingKey, RichText::clean($request->input($key))),
                default => Setting::set($settingKey, $request->filled($key) ? trim($request->input($key)) : null),
            };
        }

        return back()->with('success', 'Modifications enregistrées.');
    }

    private function saveImage(Request $request, string $input, string $settingKey, array $field): void
    {
        $current = Setting::get($settingKey);

        if ($request->hasFile($input)) {
            Setting::set($settingKey, Images::store($request->file($input), 'settings', $field['max_side'] ?? 1600, $current));
        } elseif ($request->boolean("remove_{$input}") && $current) {
            Images::delete($current);
            Setting::set($settingKey, null);
        }
    }

    private function definition(string $group): array
    {
        $definition = SettingGroups::get($group) ?? abort(404);

        // Les réglages du site (coordonnées, référencement) : administrateurs seulement
        if (($definition['sensitive'] ?? false) && !auth('admin')->user()->canManageAdmins()) {
            abort(403, 'Ces réglages sont réservés aux administrateurs.');
        }

        return $definition;
    }
}
