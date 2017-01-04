<?php

namespace Boolw\BaiduSocialite;

use SocialiteProviders\Manager\SocialiteWasCalled;

class BaiduExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('baidu', __NAMESPACE__.'\Provider');
    }
}
