<?php
/**
 * A model class for the RedBean object Project
 *
 * @author Michael Coviello <b8034196@newcastle.ac.uk>
 * @copyright 2021 Michael Coviello
 * @package Framework
 * @subpackage SystemModel
 */
    namespace Model;

    use \Config\Framework as FW;
    use \Support\Context;
/**
 * A class implementing a RedBean model for User beans
 * @psalm-suppress UnusedClass
 */
    class Project extends \RedBeanPHP\SimpleModel
    {

/**
 * @var string   The type of the bean that stores roles for this page
 * @phpcsSuppress SlevomatCodingStandard.Classes.UnusedPrivateElements
 */
        private $roletype = FW::ROLE;
/**
 * @var Array   Key is name of field and the array contains flags for checks
 * @phpcsSuppress SlevomatCodingStandard.Classes.UnusedPrivateElements
 */
        private static $editfields = [
            'title'     => [TRUE, FALSE],         // [NOTEMPTY]
            'description'     => [TRUE, FALSE],         // [NOTEMPTY]
        ];

        use \ModelExtend\FWEdit;
        use \ModelExtend\MakeGuard;
        use \Framework\Support\HandleRole;
/**
 * Add a Project from a form - invoked by the AJAX bean operation
 *
 * @param Context    $context    The context object for the site
 *
 * @throws \Framework\Exception\BadValue
 * @return \RedBeanPHP\OODBBean
 */
        public static function add(Context $context) : \RedBeanPHP\OODBBean
        {
            $now = $context->utcnow(); // make sure time is in UTC
            $fdt = $context->formdata('post');
            $title = $fdt->mustFetch('title'); // make sure we have a title...
            if (self::titleValid($title))
            {
                $user = $context->user();
                $desc = $fdt->fetch('description');
                //Dispense a project bean
                $project = \R::dispense('project');
                //Set its parameters
                $project->title = $title;
                $project->description = $desc;
                $project->created = $now;
                $project->user = $user;

                if($fdt->fetch('private',0) == 1)
                {
                    $project->private = 1;
                }
                else
                {
                    $project->private = 0;
                }
                \R::store($project);
                //Need to made this Many-Many instead of 1-Many when multiple user support is added
                return $project;
            }
            // bad password return
            throw new \Framework\Exception\BadValue('Invalid Title');
            
        }

/**
 * A function to ensure that the title being used for a project is valid.
 *
 * @param string    $title  The title
 *
 * @throws \Framework\Exception\BadValue If a bad password is detected this could be thrown
 *
 * @return bool
 */
        public static function titleValid(string $title) : bool
        {
            if ($title === '')
            {
                return false;
            }
            if (!preg_match('/^[a-z0-9]+/i', $title))
            {
                return false;
            }
            
            return true;
        }
    }
?>