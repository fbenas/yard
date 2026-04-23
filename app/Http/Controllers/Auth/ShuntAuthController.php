<?php

namespace App\Http\Controllers\Auth;

use App\Api\Models\User as YardUser;
use App\Models\AdminUser;
use App\Models\User;
use App\Support\PermissionScopes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ShuntAuthController
{
    public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);

        $request->session()->put('oauth_state', $state);

        $query = http_build_query([
            'client_id' => config('services.shunt.client_id'),
            'redirect_uri' => config('services.shunt.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'profile.read',
            'state' => $state,
        ]);

        return redirect()->away(
            rtrim(config('services.shunt.base_url'), '/') . '/oauth/authorize?' . $query
        );
    }

    public function callback(Request $request): RedirectResponse
    {
        abort_unless(
            $request->filled('state') &&
            hash_equals((string) $request->session()->pull('oauth_state'), (string) $request->state),
            403
        );

        abort_unless($request->filled('code'), 400);

        $tokenResponse = Http::asForm()->post(
            rtrim(config('services.shunt.base_url'), '/') . '/oauth/token',
            [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.shunt.client_id'),
                'client_secret' => config('services.shunt.client_secret'),
                'redirect_uri' => config('services.shunt.redirect_uri'),
                'code' => $request->code,
            ]
        )->throw()->json();

        $accessToken = $tokenResponse['access_token'];
        // $refreshToken = $tokenResponse['refresh_token'] ?? null;
        // $expiresIn = $tokenResponse['expires_in'] ?? null;

        $profile = Http::withToken($accessToken)
            ->acceptJson()
            ->get(rtrim(config('services.shunt.base_url'), '/') . '/api/me')
            ->throw()
            ->json('data');

        $user = User::query()
            ->where('auth_user_id', $profile['id'])
            ->first();

        abort_unless($user, 403, 'No user is linked to this Shunt account.');

        $organisations = collect($profile['memberships'] ?? [])
            ->filter(fn ($membership) => is_array($membership) && ! empty($membership['organisation']['id']))
            ->map(fn (array $membership) => [
                'id' => $membership['organisation']['id'],
                'name' => $membership['organisation']['name'] ?? $membership['organisation']['id'],
            ])
            ->values()
            ->all();

        $organisationIds = collect($organisations)
            ->pluck('id')
            ->filter()
            ->values();

        if (! $user->current_organisation_id || ! $organisationIds->contains($user->current_organisation_id)) {
            $user->current_organisation_id = $organisationIds->first();
            $user->save();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/admin');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
