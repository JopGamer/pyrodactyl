# OpenID Connect Configuration

This file demonstrates how to configure OpenID Connect authentication for Pyrodactyl.

## Environment Variables

Add the following variables to your `.env` file:

```env
# OpenID Connect Configuration
OPENID_CLIENT_ID=your-client-id
OPENID_CLIENT_SECRET=your-client-secret
OPENID_ISSUER=https://your-oidc-provider.com
OPENID_REDIRECT_URI=https://your-pyrodactyl-domain.com/auth/openid/callback

# Optional: Discovery URL (if different from issuer + /.well-known/openid_configuration)
# OPENID_DISCOVERY_URL=https://your-oidc-provider.com/.well-known/openid_configuration
```

## Common Provider Examples

### Keycloak
```env
OPENID_CLIENT_ID=pyrodactyl
OPENID_CLIENT_SECRET=your-secret
OPENID_ISSUER=https://keycloak.example.com/auth/realms/your-realm
```

### Auth0
```env
OPENID_CLIENT_ID=your-auth0-client-id
OPENID_CLIENT_SECRET=your-auth0-client-secret
OPENID_ISSUER=https://your-tenant.auth0.com
```

### Okta
```env
OPENID_CLIENT_ID=your-okta-client-id
OPENID_CLIENT_SECRET=your-okta-client-secret
OPENID_ISSUER=https://your-org.okta.com/oauth2/default
```

### Azure AD
```env
OPENID_CLIENT_ID=your-azure-client-id
OPENID_CLIENT_SECRET=your-azure-client-secret
OPENID_ISSUER=https://login.microsoftonline.com/your-tenant-id/v2.0
```

## How it Works

1. Users can access `/auth/openid` to start the OpenID Connect authentication flow
2. They will be redirected to your configured OpenID provider
3. After authentication, they return to `/auth/openid/callback`
4. The system will:
   - Find existing users by `external_id` (OpenID sub claim)
   - Link existing users by email if no external_id match
   - Create new users if neither match exists
   - Automatically log them in

## User Linking

- **Existing users**: If a user exists with the same email, they will be linked automatically
- **New users**: Created with a generated username and random password (since they use OpenID)
- **External ID**: The OpenID `sub` claim is stored as `external_id` for future logins

## Security Notes

- Users created via OpenID Connect have random passwords and should use OpenID for login
- The system respects all existing user permissions and roles
- Activity logging tracks all OpenID authentication attempts

## Testing the Implementation

Once configured, users can test the OpenID Connect authentication by:

1. Visiting `https://your-domain.com/auth/openid`
2. Being redirected to your OpenID provider
3. Logging in with their provider credentials
4. Being redirected back and automatically logged into Pyrodactyl

## Troubleshooting

### Common Issues

1. **"OpenID Connect authentication is not configured"**
   - Ensure all required environment variables are set
   - Check that `OPENID_CLIENT_ID` is not empty

2. **Redirect URI mismatch**
   - Ensure the `OPENID_REDIRECT_URI` matches exactly what's configured in your provider
   - Default is `https://your-domain.com/auth/openid/callback`

3. **Discovery endpoint issues**
   - Some providers may not support the standard `/.well-known/openid_configuration`
   - Set `OPENID_DISCOVERY_URL` manually if needed

4. **SSL/Certificate issues**
   - Ensure your Pyrodactyl instance has a valid SSL certificate
   - Most OpenID providers require HTTPS for callbacks

### Activity Logging

All OpenID authentication attempts are logged in the activity log with events:
- `auth:openid.success` - Successful authentication
- `auth:openid.fail` - Failed authentication attempts

## Integration Notes

- This implementation is designed to be as simple as possible while being secure
- It follows Laravel and Socialite best practices
- No database migrations are required (uses existing `external_id` field)
- Fully compatible with existing Pyrodactyl authentication and authorization systems
