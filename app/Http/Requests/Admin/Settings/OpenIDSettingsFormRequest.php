<?php

namespace Pterodactyl\Http\Requests\Admin\Settings;

use Pterodactyl\Http\Requests\Admin\AdminFormRequest;

class OpenIDSettingsFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $rules = [
            'enabled' => 'nullable|boolean',
            'auto_redirect' => 'nullable|boolean',
            'name' => 'nullable|string|max:191',
            'icon' => 'nullable|string|max:191',
            'redirect' => 'nullable|url|max:512',
        ];

        // Only require OIDC configuration if enabled
        if ($this->boolean('enabled')) {
            $rules['client_id'] = 'required|string|max:191';
            $rules['client_secret'] = 'required|string|max:512';
            $rules['issuer'] = 'required|url|max:512';
        } else {
            $rules['client_id'] = 'nullable|string|max:191';
            $rules['client_secret'] = 'nullable|string|max:512';
            $rules['issuer'] = 'nullable|url|max:512';
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'enabled' => 'Enable OpenID Connect',
            'auto_redirect' => 'Automatic Redirect',
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret',
            'issuer' => 'Issuer URL',
            'redirect' => 'Redirect URI',
            'name' => 'Provider Name',
            'icon' => 'Icon Class',
        ];
    }
}
