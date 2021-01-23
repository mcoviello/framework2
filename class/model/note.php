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
            'note'     => [TRUE, FALSE],         // [NOTEMPTY]
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
            $projectID = $fdt->mustFetch('project'); // and a project ID
            $note = $fdt->mustFetch('note'); // and a note ...
            if (!self::titleValid($title))
            {

            }
            if (!self::noteValid($note))
            {
                
            }
                //Dispense a note bean
                $noteBean = \R::dispense('note');
                //Set its parameters
                $noteBean->title = $title;
                $noteBean->note = $note;
                $noteBean->created = $now;

                if ($fdt->fetch('incTime',0) == 1)
                {
                    $started = $fdt->mustFetch('startDate').':00';
                    $finished = $fdt->mustFetch('endDate').':00';
                    if(self::datesValid($started, $finished, $now))
                    {
                        $noteBean->started = $started;
                        $noteBean->finished = $finished;
                    }
                    else
                    {
                        //Invalid dates
                        throw new \Framework\Exception\BadValue('Invalid Start and/or End Dates');
                    }
                }
                
                //Set is as cascade delete for the project
                $project = \R::load('project', $projectID);
                $project->xownNote[]= $noteBean;
                \R::store($project);
                return $noteBean;
        }     
/**
 * A function to ensure that the title being used for a project is valid.
 *
 * @param string    $title  The title
 *
 * @return bool
 */
        public static function titleValid(string $title) : bool
        {
            if ($title === '')
            {
                return false;
            }
            if (strlen($title) < 3)
            {
                return false;
            }
            if (!preg_match('/^[a-zA-Z0-9\s]*$/', $title))
            {
                return false;
            }
            return true;
        }

/**
 * A function to ensure that the note being used for a note is valid.
 *
 * @param string    $title  The title
 *
 * @return bool
 */
        public static function noteValid(string $note) : bool
        {
            if ($note === '')
            {
                return false;
            }
            if (!preg_match('/^[a-zA-Z0-9\s.,?]*$/', $note))
            {
                return false;
            }
            return true;
        }

/**
 * A function to ensure that the dates being used for a project are valid.
 *
 * @param string    $startdate  The starting date and time on the form
 * @param string    $startdate  The ending date and time on the form
 * @param string    $startdate  The current date and time
 *
 * @return bool
 */
        public static function datesValid(string $startDate, string $endDate, string $now) : bool
        {
            if($startDate >= $endDate)
            {
                return false;
            }
            //Regex for the specified date format
            if(!preg_match('/^([0-9]{4}[-][0-9]{2}[-][0-9]{2}[" "][0-9]{2}[:][0-9]{2}[:][0-9]{2})+/i', $startDate) ||
            !preg_match('/^([0-9]{4}[-][0-9]{2}[-][0-9]{2}[" "][0-9]{2}[:][0-9]{2}[:][0-9]{2})+/i', $endDate))
            {
                return false;
            }
            if($startDate > $now || $endDate > $now)
            {
                return false;
            }
            return true;
        }

/**
 * Handle an edit form for this project
 *
 * @param Context   $context    The context object
 *
 * @return array [TRUE if error, [error messages]]
 */
        public function edit(Context $context) : array
        {
            $now = $context->utcnow();
            $fdt = $context->formdata('post');
            $title = $fdt->mustFetch('title'); // make sure we have a title...
            $note = $fdt->mustFetch('note');
            if (!self::titleValid($title))
            {
                throw new \Framework\Exception\BadValue('Invalid Title (Must be at least 3 characters long and can only contain letters, numbers, and spaces.)');
            } 
            if (!self::noteValid($note))
            {
                throw new \Framework\Exception\BadValue('Invalid Note (Note can only contain letters, numbers, spaces, full-stops, commas and question-marks.)');
            }
            $emess = $this->dofields($fdt);
            $context->local()->addval('note', $this->bean);
            return [!empty($emess), $emess];
        }

    }
?>