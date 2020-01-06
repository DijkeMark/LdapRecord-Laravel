<?php

namespace LdapRecord\Laravel\Tests;

use LdapRecord\Laravel\Auth\NoDatabaseUserProvider;
use LdapRecord\Laravel\Events\AuthenticatedWithCredentials;
use LdapRecord\Laravel\Events\DiscoveredWithCredentials;
use LdapRecord\Laravel\LdapUserAuthenticator;
use LdapRecord\Laravel\LdapUserRepository;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\Entry;
use Mockery as m;

class PlainUserProviderTest extends TestCase
{
    public function test_user_repository_can_be_retrieved()
    {
        $repo = new LdapUserRepository(User::class);
        $provider = new NoDatabaseUserProvider($repo, new LdapUserAuthenticator);
        $this->assertSame($repo, $provider->getLdapUserRepository());
    }

    public function test_user_authenticator_can_be_retrieved()
    {
        $auth = new LdapUserAuthenticator;
        $provider = new NoDatabaseUserProvider(new LdapUserRepository(User::class), $auth);
        $this->assertSame($auth, $provider->getLdapUserAuthenticator());
    }

    public function test_retrieve_by_id_returns_model_instance()
    {
        $model = new Entry;
        $repo = m::mock(LdapUserRepository::class);
        $repo->shouldReceive('findByGuid')->once()->withArgs(['id'])->andReturn($model);

        $provider = new NoDatabaseUserProvider($repo, new LdapUserAuthenticator());
        $this->assertSame($model, $provider->retrieveById('id'));
    }

    public function test_retrieve_by_credentials_returns_model_instance()
    {
        $this->expectsEvents([DiscoveredWithCredentials::class]);

        $model = new Entry;
        $repo = m::mock(LdapUserRepository::class);
        $repo->shouldReceive('findByCredentials')->once()->withArgs([['username' => 'foo']])->andReturn($model);

        $provider = new NoDatabaseUserProvider($repo, new LdapUserAuthenticator());
        $this->assertSame($model, $provider->retrieveByCredentials(['username' => 'foo']));
    }

    public function test_validate_credentials_attempts_authentication()
    {
        $this->expectsEvents([AuthenticatedWithCredentials::class]);

        $user = new User;
        $auth = m::mock(LdapUserAuthenticator::class);
        $auth->shouldReceive('attempt')->once()->withArgs([$user, 'secret'])->andReturnTrue();

        $provider = new NoDatabaseUserProvider(new LdapUserRepository(Entry::class), $auth);
        $this->assertTrue($provider->validateCredentials($user, ['password' => 'secret']));
    }
}
