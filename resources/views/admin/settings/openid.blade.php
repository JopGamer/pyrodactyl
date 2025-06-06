@extends('layouts.admin')
@include('partials/admin.settings.nav', ['activeTab' => 'openid'])

@section('title')
    OpenID Connect Settings
@endsection

@section('content-header')
    <h1>OpenID Connect Settings<small>Configure OpenID Connect authentication for your panel.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Settings</li>
    </ol>
@endsection

@section('content')
    @yield('settings::nav')
    <div class="row">
        <div class="col-xs-12">
            <form action="{{ route('admin.settings.openid') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">OpenID Connect Configuration</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">Enable OpenID Connect</label>
                                <div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enabled" value="1" 
                                                @if(old('enabled', $current['enabled'])) checked @endif> 
                                            Enable OpenID Connect authentication
                                        </label>
                                    </div>
                                    <p class="text-muted small">Allow users to login using an external OpenID Connect provider.</p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Automatic Redirect</label>
                                <div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="auto_redirect" value="1" 
                                                @if(old('auto_redirect', $current['auto_redirect'])) checked @endif> 
                                            Automatically redirect to OpenID Connect provider
                                        </label>
                                    </div>
                                    <p class="text-muted small">When enabled, users visiting the login page will be automatically redirected to the OpenID Connect provider.</p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Disable Registration</label>
                                <div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="disable_registration" value="1" 
                                                @if(old('disable_registration', $current['disable_registration'])) checked @endif> 
                                            Disable new account creation via OpenID
                                        </label>
                                    </div>
                                    <p class="text-muted small">When enabled, only existing users can login via OpenID. New users must have accounts created manually first.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box" id="openid-config" style="@if(!old('enabled', $current['enabled'])) display: none; @endif">
                    <div class="box-header with-border">
                        <h3 class="box-title">Provider Configuration</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="control-label">Issuer URL <span class="field-required"></span></label>
                                <input type="url" class="form-control" name="issuer" 
                                    value="{{ old('issuer', $current['issuer']) }}" 
                                    placeholder="https://your-provider.com/auth/realms/master" />
                                <p class="text-muted small">
                                    The OpenID Connect issuer URL. This should be the base URL of your OIDC provider.
                                    <br><strong>Examples:</strong>
                                    <br>• Keycloak: https://keycloak.example.com/auth/realms/master
                                    <br>• Auth0: https://your-tenant.auth0.com
                                    <br>• Azure AD: https://login.microsoftonline.com/your-tenant-id/v2.0
                                </p>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Client ID <span class="field-required"></span></label>
                                <input type="text" class="form-control" name="client_id" 
                                    value="{{ old('client_id', $current['client_id']) }}" />
                                <p class="text-muted small">The Client ID provided by your OpenID Connect provider.</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="control-label">Client Secret <span class="field-required"></span></label>
                                <input type="password" class="form-control" name="client_secret" 
                                    value="{{ old('client_secret', $current['client_secret']) }}" />
                                <p class="text-muted small">The Client Secret provided by your OpenID Connect provider. This will be encrypted when stored.</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="control-label">Redirect URI</label>
                                <input type="url" class="form-control" name="redirect" 
                                    value="{{ old('redirect', $current['redirect']) }}" 
                                    placeholder="{{ url('/auth/openid/callback') }}" />
                                <p class="text-muted small">
                                    The redirect URI configured in your OpenID Connect provider. Leave blank to use the default: 
                                    <code>{{ url('/auth/openid/callback') }}</code>
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="control-label">Provider Name</label>
                                <input type="text" class="form-control" name="name" 
                                    value="{{ old('name', $current['name']) }}" 
                                    placeholder="OpenID Connect" />
                                <p class="text-muted small">The display name for the login button (e.g., "Login with Keycloak").</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Icon Class</label>
                                <input type="text" class="form-control" name="icon" 
                                    value="{{ old('icon', $current['icon']) }}" 
                                    placeholder="fas fa-sign-in-alt" />
                                <p class="text-muted small">FontAwesome icon class for the login button.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Setup Instructions</h3>
                    </div>
                    <div class="box-body">
                        <div class="alert alert-info">
                            <h4><i class="icon fa fa-info"></i> Configuration Required</h4>
                            <p>To use OpenID Connect authentication, you need to configure your OIDC provider with the following redirect URI:</p>
                            <code>{{ url('/auth/openid/callback') }}</code>
                            <p class="mt-2">
                                <strong>User Account Linking:</strong> Users will be matched by email address. 
                                If a user with the same email exists, they will be automatically linked to the OIDC account.
                                If no matching user exists and registration is disabled, the user will be shown an error page 
                                explaining they need to purchase a server to access the panel.
                            </p>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h4><i class="icon fa fa-warning"></i> Security Notice</h4>
                            <p>
                                • Ensure your OIDC provider is properly secured with HTTPS<br>
                                • Only configure trusted OIDC providers<br>
                                • The client secret will be encrypted when stored in the database<br>
                                • Test the configuration thoroughly before enabling for all users
                            </p>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" name="_method" value="PATCH" class="btn btn-sm btn-primary pull-right">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('input[name="enabled"]').change(function() {
                if ($(this).is(':checked')) {
                    $('#openid-config').show();
                } else {
                    $('#openid-config').hide();
                }
            });
        });
    </script>
@endsection
