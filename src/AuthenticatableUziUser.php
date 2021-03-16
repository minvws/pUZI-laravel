<?php

namespace MinVWS\PUZI\Laravel;

use Illuminate\Contracts\Auth\Authenticatable;
use MinVWS\PUZI\UziUser as BaseUziUser;

class AuthenticatableUziUser extends BaseUziUser implements Authenticatable
{
    /**
     * @param BaseUziUser $base
     * @return self
     */
    public static function fromUziUser(BaseUziUser $base): self
    {
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

    /**
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return "uzi_number";
    }

    /**
     * @return mixed|string
     */
    public function getAuthIdentifier()
    {
        return $this->getUziNumber();
    }

    /**
     * @return string
     */
    public function getAuthPassword()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getRememberToken()
    {
        return "";
    }

    /**
     * @param string $value
     */
    public function setRememberToken($value)
    {
    }

    /**
     * @return string
     */
    public function getRememberTokenName()
    {
        return "";
    }
}
