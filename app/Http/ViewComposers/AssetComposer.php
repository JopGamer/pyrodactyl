<?php

namespace Pterodactyl\Http\ViewComposers;

use Illuminate\View\View;
use Pterodactyl\Services\Helpers\AssetHashService;

class AssetComposer
{
  /**
   * Provide access to the asset service in the views.
   */
  public function compose(View $view): void
  {
    $view->with('siteConfiguration', [
      'name' => config('app.name') ?? 'Pyrodactyl',
      'locale' => config('app.locale') ?? 'en',
      'captcha' => [
        'driver' => config('captcha.driver', 'none'),
        'turnstile' => [
          'siteKey' => config('captcha.turnstile.site_key', '')
        ],
        'hcaptcha' => [
          'siteKey' => config('captcha.hcaptcha.site_key', '')
        ],
        'mcaptcha' => [
          'siteKey' => config('captcha.mcaptcha.site_key', '')
        ],
        'friendly' => [
          'siteKey' => config('captcha.friendly.site_key', '')
        ],
        'recaptcha' => [
          'siteKey' => config('captcha.recaptcha.site_key', '')
        ],
      ],
      'openid' => [
        'enabled' => config('openid.enabled', false),
        'client_id' => config('openid.client_id', ''),
        'issuer' => config('openid.issuer', ''),
        'name' => config('openid.name', ''),
        'icon' => config('openid.icon', ''),
      ],
    ]);
  }
}
