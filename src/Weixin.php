<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/2 22:18
 * Email: sgenmi@gmail.com
 */

namespace Weida\Oauth2;

use Throwable;
use Weida\Oauth2Core\AbstractApplication;
use RuntimeException;

class Weixin extends AbstractApplication
{
    protected array $scopes=['snsapi_base','snsapi_userinfo'];
    protected function getAuthUrl(): string
    {
        $params=[
            'appid'=>$this->getConfig()->get('client_id'),
            'redirect_uri'=>$this->getConfig()->get('redirect'),
            'response_type'=>'code',
            'scope'=>implode(',',$this->scopes),
            'state'=> $this->state,
        ];
        //开放平台 登录网站
        if($params['scope']=='snsapi_login'){
            return sprintf('https://open.weixin.qq.com/connect/qrconnect?%s#wechat_redirect',http_build_query($params));
        }
        return sprintf('https://open.weixin.qq.com/connect/oauth2/authorize?%s#wechat_redirect',http_build_query($params));
    }

    /**
     * 微信第三方开放平台代公众号实现网页授权 这里比较特殊，后面在做兼容，有点困了
     * https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=APPID&code=CODE&grant_type=authorization_code&component_appid=COMPONENT_APPID&component_access_token=COMPONENT_ACCESS_TOKEN
     * @return string
     * @author Weida
     */
    public function getTokenUrl(): string
    {
        $params=[
            'appid'=>$this->getConfig()->get('client_id'),
            'secret'=>$this->getConfig()->get('client_secret'),
            'code'=>$this->getConfig()->get('code'),
            'grant_type'=>'authorization_code'
        ];
        return 'https://api.weixin.qq.com/sns/oauth2/access_token?'.http_build_query($params);
    }

    /**
     * @return array
     * @throws Throwable
     * @author Weida
     */
    public function tokenFromCode(): array
    {
        $url =  $this->getTokenUrl();
        $resp = $this->getHttpClient()->request('GET',$url);
        if($resp->getStatusCode()!=200){
            throw new RuntimeException('Request access_token exception');
        }
        $arr = json_decode($resp->getBody()->getContents(),true);
        if (empty($arr['access_token'])) {
            throw new RuntimeException('Failed to get access_token: ' . json_encode($arr, JSON_UNESCAPED_UNICODE));
        }
        return $arr;
    }

    public function getUserByToken(): string
    {
        return '';
    }

    protected function getUserInfoUrl(string $accessToken,string $openid): string
    {
        $params=[
            'access_token'=>$accessToken,
            'openid'=>$openid,
            'lang'=>'zh_CN'
        ];
        return 'https://api.weixin.qq.com/sns/userinfo?'.http_build_query($params);
    }
}
