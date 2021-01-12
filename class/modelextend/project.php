<?php
/**
 * A trait that allows extending the model class for the RedBean object Project
 *
 *
 * @author Michael Coviello <b8034196@newcastle.ac.uk>
 * @copyright 2021 Michael Coviello
 * @package Framework
 * @subpackage ModelExtend
 */
    namespace ModelExtend;

    use \Support\Context;

    trait Project
    {
/**
 * A function to ensure that any relevant title rules are applied when setting a project's title.
 *
 * @param string    $pw  The password
 *
 * @throws \Framework\Exception\BadValue If a bad password is detected this could be thrown
 *
 * @return bool
 */
        public static function titleValid(string $title) : bool
        { 
            if(title === ''){
                return false;
            }

            if (!preg_match('/^[a-z0-9]+/i', title)){
                return false;
            }

            return true;
        }

        public function update() : void
        {
            if (!preg_match('/^[a-z0-9]+/i', $this->bean->login))
            {
                throw new \Framework\Exception\BadValue('Invalid login name');
            }
            if (!filter_var($this->bean->email, FILTER_VALIDATE_EMAIL))
            {
                throw new \Framework\Exception\BadValue('Invalid email address');
            }
        }

/**
 * Returns the logs associated with a project
 *
 * @return int;
 */
        public function logs()
        {
            return 0;
        }
    }
?>
