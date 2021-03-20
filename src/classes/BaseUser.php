<?php

namespace nzt\classes;

use nzt\traits\EventDispatcher;

use nzt\interfaces\SessionInterface;
use nzt\interfaces\UserStorageInterface;

class BaseUser
{
    use EventDispatcher;

    private SessionInterface $session;
    private UserStorageInterface $storage;

    /**
     * @param SessionInterface $session
     * @param UserStorage $storage
     */
    public function __construct(SessionInterface $session, UserStorageInterface $storage)
    {
        $this->session = $session;
        $this->storage = $storage;
    }

    /**
     * @param string $login
     * @param string $password
     * @return boolean
     */
    public function login(string $login, string $password): bool
    {
        $user = $this->storage->getUser($login, $password);
        if ($user !== false) {
            $this->session->setValue('loggedUser', $user);
            $this->dispathFn('AfterLogin', [$user]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $id
     * @return boolean
     */
    public function loginByID(string $id): bool
    {
        $user = $this->storage->getUserById($id);
        if ($user !== false) {
            $this->session->setValue('loggedUser', $user);
            $this->dispathFn('AfterLogin', [$user]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->session->unsetValue('loggedUser');
        $this->dispathFn('AfterLogout');
    }

    /**
     * @return boolean
     */
    public function isLogged(): bool
    {
        return $this->session->hasValue('loggedUser');
    }

    /**
     * @param string $id
     * @return void
     */
    public function activate(string $id)
    {
        $this->storage->activateUser($id);
        $this->dispathFn('AfterActivate', [$id]);
    }

    /**
     * @return boolean
     */
    public function isActivated(): bool
    {
        $userdata = $this->session->getValue('loggedUser');

        if (!empty($userdata)) {
            return (bool) $this->storage->isActivated($userdata);
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        $userdata = $this->session->getValue('loggedUser');

        if (!empty($userdata)) {
            return $this->storage->getId($userdata);
        } else {
            return false;
        }
    }

    /**
     * @param array $data
     * @return void
     */
    public function register(string $login, string $password, array $data)
    {
        $this->storage->registerUser($login, $password, $data);
        $this->dispathFn('AfterRegister', [$data]);
    }

    /**
     * @return mixed
     */
    public function getUserdata()
    {
        return $this->session->getValue('loggedUser');
    }
}
