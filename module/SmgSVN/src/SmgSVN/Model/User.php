<?php
/**
 * Created by PhpStorm.
 * User: smeagol
 * Date: 17.05.14
 * Time: 16:45
 */

namespace SmgSVN\Model;


class User {

    protected $name;
    protected $password;

    /**
     * @param $name
     * @param $password
     */
    function __construct($name, $password)
    {
        $this->name = $name;
        $this->password = $password;
    }

    /**
     * @param mixed $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function __toString()
    {
        return $this->getName();
    }

} 