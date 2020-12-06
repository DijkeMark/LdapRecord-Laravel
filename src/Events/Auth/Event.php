<?php

namespace LdapRecord\Laravel\Events\Auth;

use LdapRecord\Models\Model as LdapModel;
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Event
{
    /**
     * The LDAP user object.
     *
     * @var LdapModel
     */
    public $object;

    /**
     * The LDAP users authenticatable Eloquent model.
     *
     * @var EloquentModel|null
     */
    public $eloquent;

    /**
     * Constructor.
     *
     * @param LdapModel          $object
     * @param EloquentModel|null $eloquent
     */
    public function __construct(LdapModel $object, EloquentModel $eloquent = null)
    {
        $this->object = $object;
        $this->eloquent = $eloquent;
    }
}
