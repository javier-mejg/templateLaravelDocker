<?php

namespace Dcblogdev\MsGraph;

/*
 * msgraph api documentation can be found at https://developer.msgraph.com/reference
 **/

use Dcblogdev\MsGraph\Events\NewMicrosoft365SignInEvent;
use Dcblogdev\MsGraph\Models\MsGraphToken;
use Dcblogdev\MsGraph\Resources\Contacts;
use Dcblogdev\MsGraph\Resources\Emails;
use Dcblogdev\MsGraph\Resources\Files;
use Dcblogdev\MsGraph\Resources\Tasks;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
// --------------------------
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Session;

class MsGraph
{
    public function contacts()
    {
        return new Contacts();
    }

    public function emails()
    {
        return new Emails();
    }

    public function files()
    {
        return new Files();
    }

    public function tasks()
    {
        return new Tasks();
    }

    /**
     * Set the base url that all API requests use.
     * @var string
     */
    protected static $baseUrl = 'https://graph.microsoft.com/v1.0/';

    /**
     * Make a connection or return a token where it's valid.
     * @return mixed
     */
  public function connect($id = null)
{

    $id = $this->getUserId($id);
    $provider = $this->getProvider();

    // 1. Si viene de Microsoft con código
    if (request()->has('code')) {

        try {
            $accessToken = $provider->getAccessToken('authorization_code', ['code' => request('code')]);

            // Limpiar sesión anterior
            if (Auth::check()) {
                Auth::logout();
            }

            Session::flush();
            request()->session()->invalidate();
            request()->session()->regenerate();

            $response = Http::withToken($accessToken->getToken())->get(self::$baseUrl . 'me');
            $graphUser = $response->json();

            // Crear usuario si no existe
            $user = User::firstOrCreate([
                'email' => base64_encode($graphUser['userPrincipalName']),
            ], [
                'name' => Crypt::encryptString($graphUser['displayName']),
                'email' => base64_encode($graphUser['mail'] ?? $graphUser['userPrincipalName']),
                'password' => '',
                'us_uID' => Str::uuid(),
                'rol_uID' => '',
                'camp_uID' => '',
                'ar_uID' => '525a5305-f721-424f-9930-49d0407650f4',
                'sta_uID' => 'f567f85f-096c-4173-ac41-6b624d7024d4',
                'us_banner_guid' => '',
                'us_banner_id' => 0,
                'cat_uID' => 'e8597dc7-0d81-4ca5-a415-6dc2b9df52f4',
            ]);

            // Validacion de status
            if ($user->sta_uID == 'f567f85f-096c-4173-ac41-6b624d7024d4') {
                //Log::info('Redirección a login por status inválido');
                return redirect()->route('login', ['pending' => 'invalid']);
            }

            if ($user->sta_uID == '9569bcce-0869-40b9-b6c5-c7b72c91996a' || $user->sta_uID == '6d4f9fd1-65bf-4eba-af2c-f785b62c1554') {
                //Log::info('Redirección a login por status inválido');
                return redirect()->route('login', ['fallo' => 'fail']);
            }

            // Validacion de categoria
            if (in_array($user->cat_uID, ['e8597dc7-0d81-4ca5-a415-6dc2b9df52f4','53e69a5f-12a5-4785-a563-c6f76282e06f']) && $user->sta_uID == "023584f1-5547-429a-a131-3b3810d156c7") {
                //Log::info('Redirección a login por categoria inválida');
                return redirect()->route('login', ['domain' => 'invalid']);
            }

            // Eliminar tokens anteriores
            MsGraphToken::where('user_id', $user->id)->delete();

            Auth::login($user);

            // Guardar token nuevo
            DB::transaction(function () use ($user, $accessToken) {
                $this->storeToken(
                    $accessToken->getToken(),
                    $accessToken->getRefreshToken(),
                    $accessToken->getExpires(),
                    $user->id,
                    $user->email
                );

                $token = MsGraphToken::where('user_id', $user->id)->first();
                $token->id_session = session()->getId();
                $token->save();
            });

            // Cargar sesión del usuario
            try {
                $menuData = app(\App\Http\Controllers\Main\MenuController::class)->loadMenu($user);

                if (is_array($menuData)) {
                    session(['info' => $menuData]);
                    session(['login_complete' => true]);
                    session()->save();
                } else {
                }
            } catch (\Throwable $e) {
            }

            $redirectTo = session('redirect_to', '/principal/menu');
            return redirect($redirectTo);

        } catch (\Throwable $e) {
            return redirect('/login')->withErrors(['error' => 'Error de autenticación.']);
        }
    }

    if (!$this->isConnected($id)) {
        $authUrl = $provider->getAuthorizationUrl();
        return redirect($authUrl);
    }

    return redirect('/principal/menu');
}



        /**
         * @param $id
         * @return bool
         */
        public function isConnected($id = null)
        {
            $token = $this->getTokenData($id);

            if ($token === null) {
                return false;
            }

            if ($token->expires < time()) {
                return false;
            }

            return true;
    }

    /**
     * logout of application and Microsoft 365, redirects back to the provided path.
     * @param  string  $redirectPath
     * @return RedirectResponse
     */
    public function disconnect($redirectPath = '/', $logout = true)
    {
        if ($logout === true && auth()->check()) {
            auth()->logout();
        }

        return redirect()->away('https://login.microsoftonline.com/common/oauth2/v2.0/logout?post_logout_redirect_uri=' . url($redirectPath));
    }

    /**
     * Return authenticated access token or request new token when expired.
     * @param  $id integer - id of the user
     * @param  $returnNullNoAccessToken null when set to true return null
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector string access token
     */
    public function getAccessToken($id = null, $redirectWhenNotConnected = true)
    {
        $token = $this->getTokenData($id);
        $id = $this->getUserId($id);

        if ($redirectWhenNotConnected) {
            if (!$this->isConnected()) {
                return redirect()->away(config('msgraph.redirectUri'));
            }
        }

        if ($token === null) {
            return null;
        }

        if ($token->expires < time() + 300) {
            $user = config('auth.providers.users.model')::find($id);
            return $this->renewExpiringToken($token, $id, $user->email);
        }

        return $token->access_token;
    }

    /**
     * @param  $id  - integar id of user
     * @return object
     */
    public function getTokenData($id = null)
    {
        $id = $this->getUserId($id);
        return MsGraphToken::where('user_id', $id)->first();
    }

    /**
     * Store token.
     * @param  $access_token string
     * @param  $refresh_token string
     * @param  $expires string
     * @param  $id integer
     * @return object
     */
    public function storeToken($access_token, $refresh_token, $expires, $id, $email)
    {
        return MsGraphToken::updateOrCreate(['user_id' => $id], [
            'user_id' => $id,
            'email' => $email,
            'access_token' => $access_token,
            'expires' => $expires,
            'refresh_token' => $refresh_token,
        ]);
    }

    /**
     * return array containing previous and next page counts.
     * @param  $data array
     * @param  $total array
     * @param  $limit  integer
     * @param  $skip integer
     * @return array
     */
    public function getPagination(array $data, int $total, int $limit, int $skip)
    {
        $previous = 0;
        $next = 0;

        if (isset($data['@odata.nextLink'])) {
            $parts = explode('skip=', $data['@odata.nextLink']);

            if (isset($parts[1])) {
                $previous = $parts[1] - $limit;
                $next = $parts[1];
            }

            if ($previous < 0) {
                $previous = 0;
            }

            if ($next == $total) {
                $next = 0;
            }
        }

        if ($total > $limit) {
            $previous = $skip - $limit;
        }

        if ($previous < 0) {
            $previous = 0;
        }

        return [
            'previous' => $previous,
            'next' => $next,
        ];
    }

    /**
     * @param $token
     * @param $id
     * @param $email
     * @return mixed|string
     * @throws IdentityProviderException
     */
    protected function renewExpiringToken($token, $id, $email)
    {
        $oauthClient = $this->getProvider();
        $newToken = $oauthClient->getAccessToken('refresh_token', ['refresh_token' => $token->refresh_token]);
        $this->storeToken($newToken->getToken(), $newToken->getRefreshToken(), $newToken->getExpires(), $id, $email);

        return $newToken->getToken();
    }

    /**
     * __call catches all requests when no found method is requested.
     * @param  $function  - the verb to execute
     * @param  $args  - array of arguments
     * @return json request
     * @throws Exception
     */
    public function __call($function, $args)
    {
        $options = ['get', 'post', 'patch', 'put', 'delete'];
        $path = (isset($args[0])) ? $args[0] : null;
        $data = (isset($args[1])) ? $args[1] : null;
        $headers = (isset($args[2])) ? $args[2] : null;
        $id = (isset($args[3])) ? $args[3] : auth()->id();

        if (in_array($function, $options)) {
            return self::guzzle($function, $path, $data, $headers, $id);
        } else {
            //request verb is not in the $options array
            throw new Exception($function . ' is not a valid HTTP Verb');
        }
    }

    /**
     * run guzzle to process requested url.
     * @param  $type string
     * @param  $request string
     * @param  $data array
     * @param  array  $headers
     * @param  $id integer
     * @return json object
     */
    protected function guzzle($type, $request, $data = [], $headers = [], $id = null)
    {
        try {
            $client = new Client;

            $mainHeaders = [
                'Authorization' => 'Bearer ' . $this->getAccessToken($id),
                'content-type' => 'application/json',
                'Prefer' => config('msgraph.preferTimezone'),
            ];

            if (is_array($headers)) {
                $headers = array_merge($mainHeaders, $headers);
            } else {
                $headers = $mainHeaders;
            }

            $response = $client->$type(self::$baseUrl . $request, [
                'headers' => $headers,
                'body' => json_encode($data),
            ]);

            $responseObject = $response->getBody()->getContents();

            $isJson = $this->isJson($responseObject);

            if ($isJson) {
                return json_decode($responseObject, true);
            }

            return $responseObject;

        } catch (ClientException $e) {
            return json_decode(($e->getResponse()->getBody()->getContents()));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $string
     * @return bool
     */
    protected function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param $id
     * @return int|mixed|string|null
     */
    protected function getUserId($id = null)
    {
        if ($id === null) {
            $id = auth()->id();
        }

        return $id;
    }

    /**
     * @return GenericProvider
     */
    protected function getProvider()
    {
        //set up the provides loaded values from the config
        return new GenericProvider([
            'clientId' => config('msgraph.clientId'),
            'clientSecret' => config('msgraph.clientSecret'),
            'redirectUri' => config('msgraph.redirectUri'),
            'urlAuthorize' => config('msgraph.urlAuthorize'),
            'urlAccessToken' => config('msgraph.urlAccessToken'),
            'urlResourceOwnerDetails' => config('msgraph.urlResourceOwnerDetails'),
            'scopes' => config('msgraph.scopes'),
        ]);
    }
}
