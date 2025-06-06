<?php

namespace Pterodactyl\Http\Controllers\Admin\Settings;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Contracts\Repository\SettingsRepositoryInterface;
use Pterodactyl\Http\Requests\Admin\Settings\OpenIDSettingsFormRequest;

class OpenIDController extends Controller
{
    /**
     * @var \Prologue\Alerts\AlertsMessageBag
     */
    protected $alert;

    /**
     * @var \Pterodactyl\Contracts\Repository\SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * OpenIDController constructor.
     */
    public function __construct(
        AlertsMessageBag $alert,
        SettingsRepositoryInterface $settings
    ) {
        $this->alert = $alert;
        $this->settings = $settings;
    }

    /**
     * Render OpenID Connect settings page.
     */
    public function index(): View
    {
        return view('admin.settings.openid', [
            'current' => [
                'enabled' => $this->settings->get('settings::openid:enabled', false),
                'auto_redirect' => $this->settings->get('settings::openid:auto_redirect', false),
                'client_id' => $this->settings->get('settings::openid:client_id', ''),
                'client_secret' => $this->settings->get('settings::openid:client_secret', ''),
                'issuer' => $this->settings->get('settings::openid:issuer', ''),
                'redirect' => $this->settings->get('settings::openid:redirect', ''),
                'name' => $this->settings->get('settings::openid:name', 'OpenID Connect'),
                'icon' => $this->settings->get('settings::openid:icon', 'fas fa-sign-in-alt'),
            ],
        ]);
    }

    /**
     * Handle OpenID Connect settings update.
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function update(OpenIDSettingsFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Save all OpenID Connect settings
        $this->settings->set('settings::openid:enabled', $data['enabled'] ?? false);
        $this->settings->set('settings::openid:auto_redirect', $data['auto_redirect'] ?? false);
        $this->settings->set('settings::openid:client_id', $data['client_id'] ?? '');
        $this->settings->set('settings::openid:client_secret', $data['client_secret'] ?? '');
        $this->settings->set('settings::openid:issuer', $data['issuer'] ?? '');
        $this->settings->set('settings::openid:redirect', $data['redirect'] ?? '');
        $this->settings->set('settings::openid:name', $data['name'] ?? 'OpenID Connect');
        $this->settings->set('settings::openid:icon', $data['icon'] ?? 'fas fa-sign-in-alt');

        $this->alert->success('OpenID Connect settings have been updated successfully.')->flash();
        
        return redirect()->route('admin.settings.openid');
    }
}
