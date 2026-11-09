<?php

namespace MinVWS\PUZI\Laravel;

use Illuminate\Contracts\Auth\Authenticatable;
use MinVWS\PUZI\UziUser as BaseUziUser;

/**
 * Class AuthenticatableUziUser
 * SPDX-License-Identifier: EUPL-1.2
 * @package MinVWS\PUZI\Laravel
 */
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
        $user->setSerialNumber($base->getSerialNumber());

        return $user;
    }

    /**
     * @return string
     */
    #[\Override]
    public function getAuthIdentifierName()
    {
        return "uzi_number";
    }

    /**
     * @return mixed|string
     */
    #[\Override]
    public function getAuthIdentifier()
    {
        return $this->getUziNumber();
    }

    /**
     * UZI users authenticate with a client certificate and have no password.
     *
     * @return string
     */
    #[\Override]
    public function getAuthPasswordName()
    {
        return "password";
    }

    /**
     * @return string
     */
    #[\Override]
    public function getAuthPassword()
    {
        return "";
    }

    /**
     * @return string
     */
    #[\Override]
    public function getRememberToken()
    {
        return "";
    }

    /**
     * @param string|null $value
     */
    #[\Override]
    public function setRememberToken($value)
    {
    }

    /**
     * @return string
     */
    #[\Override]
    public function getRememberTokenName()
    {
        return "";
    }
}
