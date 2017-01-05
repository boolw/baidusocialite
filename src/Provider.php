<?php

namespace Boolw\BaiduSocialite;

use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     * http://developer.baidu.com/wiki/index.php?title=docs/oauth/authorization
     */
    const IDENTIFIER = 'BAIDU';

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['basic'];

    /**
     * {@inheritdoc}.
     *
     * @see \Laravel\Socialite\Two\AbstractProvider::getAuthUrl()
     */
    protected function getAuthUrl($state)
    {
    	$this->with(array("display"=>"popup"));
        return $this->buildAuthUrlFromBase('https://openapi.baidu.com/oauth/2.0/authorize', $state);
    }

    /**
     * {@inheritdoc}.
     *
     * @see \Laravel\Socialite\Two\AbstractProvider::getTokenUrl()
     */
    protected function getTokenUrl()
    {
        return 'https://openapi.baidu.com/oauth/2.0/token';
    }

    /**
     * {@inheritdoc}.
     *
     * @see \Laravel\Socialite\Two\AbstractProvider::getUserByToken()
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://openapi.baidu.com/rest/2.0/passport/users/getLoggedInUser?access_token='.$token);
        
//      $user = json_decode($this->removeCallback($response->getBody()->getContents()), true);
//      
//      $response = $this->getHttpClient()->get('https://openapi.baidu.com/rest/2.0/passport/users/getInfo?access_token='.$token);
        
        return json_decode($this->removeCallback($response->getBody()->getContents()), true);
    }

    /**
     * {@inheritdoc}.
     *
     * @see \Laravel\Socialite\Two\AbstractProvider::mapUserToObject()
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['uid'], 'nickname' => $user['uname'],
            'name' => null, 'email' => null, 'avatar' => 'http://tb.himg.baidu.com/sys/portrait/item/'.$user['portrait'],
        ]);
    }

    /**
     * {@inheritdoc}.
     *
     * @see \Laravel\Socialite\Two\AbstractProvider::getTokenFields()
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * @param mixed $response
     *
     * @return string
     */
    protected function removeCallback($response)
    {
        if (strpos($response, 'callback') !== false) {
            $lpos = strpos($response, '(');
            $rpos = strrpos($response, ')');
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }

        return $response;
    }
}
