<?php

namespace MinVWS\Laravel\Puzi;

use Illuminate\Contracts\Auth\Authenticatable;
use MinVWS\PUZI\UziUser as BaseUziUser;

class AuthenticatableUziUser extends BaseUziUser implements Authenticatable
{
    static function fromUziUser(BaseUziUser $base): self {
        $user = new self();
        $user->setAgbCode($base->getAgbCode());
        $user->setCardType($base->getCardType());
        $user->setGivenName($base->getGivenName());
        $user->setOidCa($base->getOidCa());
        $user->setRole($base->getRole());
        $user->setSubscriberNumber($base->getSubscriberNumber());
        $user->setSurName($base->getSurName());
        $user->setUziNumber($base->getUziNumber());
        $user->setUziVersion($base->getUziVersion());

        return $user;
    }

    public function getAuthIdentifierName()
    {
        return "uzi_number";
    }

    public function getAuthIdentifier()
    {
        return $this->getUziNumber();
    }

    public function getAuthPassword()
    {
        return "";
    }

    public function getRememberToken()
    {
        return false;
    }

    public function setRememberToken($value)
    {
    }

    public function getRememberTokenName()
    {
        return false;
    }
}
