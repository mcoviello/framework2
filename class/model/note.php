<?php
/**
 * A model class for the RedBean object Note
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
    class Note extends \RedBeanPHP\SimpleModel
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
            $note = $fdt->mustFetch('note');   //and a description...
            if (self::titleValid($title) && self::noteValid($note))
            {
                $rest = $context->rest();
                //@todo fix the note project id
                $project = $rest[1];
                //Dispense a project bean
                $noteBean = \R::dispense('note');
                //Set its parameters
                $noteBean->title = $title;
                $noteBean->note = $note;
                $noteBean->created = $now;
                $noteBean->project = $project;
                \R::store($noteBean);
                return $noteBean;
            } 
            else
            {
            // bad return
            throw new \Framework\Exception\BadValue('Invalid Title or Note');
            }
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
            trim($title);

            if ($title === '')
            {
                return false;
            }
            if (!preg_match('/^[a-z0-9\s]+/i', $title))
            {
                return false;
            }
            if(strlen($title) < 3)
            {
                return false;
            }
            return true;
        }

/**
 * A function to ensure that the description being used for a project is valid.
 *
 * @param string    $desc  The description
 *
 * @throws \Framework\Exception\BadValue If a bad password is detected this could be thrown
 *
 * @return bool
 */
        public static function noteValid(string $note) : bool
        {
            //The description isn't empty, but contains invalid characters
            if ($note !== '' && !preg_match('/^[a-z0-9.,\s]+/i', $note))
            {
                return false;
            }
            return true;
        }
    }
?>