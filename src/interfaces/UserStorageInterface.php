<?php

namespace nzt\interfaces;


interface UserStorageInterface
{
    /**
     * @param string $login
     * @param string $password
     * @return array|bool
     */
    public function getUser(string $login, string $password);

    /**
     * @param string $id
     * @return array|bool
     */
    public function getUserById(string $id);

    /**
     * @param string $login
     * @param string $password
     * @param array $data
     * @return boolean
     */
    public function registerUser(string $login, string $password, array $data): bool;

    /**
     * @param string $id
     * @return boolean
     */
    public function activateUser(string $id): bool;

    /**
     * @param array $userdata
     * @return boolean
     */
    public function isActivated(array $userdata): bool;

    /**
     * @param array $userdata
     * @return string
     */
    public function getId(array $userdata): string;
}
