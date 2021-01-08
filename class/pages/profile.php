<?php
/**
 * A class that contains code to handle any requests for  /profile/
 *
 * @author Michael Coviello <b8034196@newcastle.ac.uk>
 * @copyright 2021 Michael Coviello
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
/**
 * Support /profile/
 */
    class Profile extends \Framework\Siteaction
    {
/**
 * Handle profile operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $fdt = $context->formdata('post');
            if($fdt->exists('email'))
            { //there is a post
                $change = FALSE;
                $user = $context->user();
                
                try
                {
                    $email = $fdt->mustfetch('email', FILTER_VALIDATE_EMAIL);
                    if($email !== $user->email)
                    {//it is not the same as the current email
                        $user->email = $email;
                        $change = TRUE;
                    }
                }

                catch(\Framework\Exception\BadValue $e)
                {
                    $context->local()->message(\Framework\Local::ERROR, 'Invalid Email Address');
                }

                $pw = $fdt->mustfetch('pw');
                if($pw !== '')
                { // if there has been a password change attempt
                    if($pw !== $fdt->mustfetch('cpw'))
                    { // The password and confirm password aren't the same
                        $context->local()->message(\Framework\Local::ERROR, 'Passwords do not match'); 
                    }
                    elseif(!\Model\User::pwValid($pw))
                    { // Doesnt meet valid password rules
                        $context->local()->message(\Framework\Local::ERROR, 'Password is too weak'); 
                    }
                    else
                    {
                        $user->setpw($pw);
                        $change = TRUE;
                    }
                }
                if($change)
                {
                    \R::store($user);
                    $context->local()->message(\Framework\Local::MESSAGE, 'Details Updated'); 
                }
            }
            return '@content/profile.twig';
        }
    }
?>