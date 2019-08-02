<?php


namespace App\Services;

use App\Http\Controllers\InfusionsoftController;
use App\Http\Helpers\InfusionsoftHelper;
use App\Module;
use App\Tag;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * It manage the decision logic related to Reminder Assigner.
 */
class ReminderService implements Reminder
{
    /**
     * @param string $customer_email
     * @throws Exception
     */
    public function assignModuleReminder(string $customer_email)
    {
        $coursesOfCustomer = $this->getCustomersCourses($customer_email);
        $this->handleTagAttachments($coursesOfCustomer, $customer_email);
    }

    /**
     * @param string $customer_email
     * @param int $tag_id
     * @throws Exception
     */
    public function attachTagToCustomer(string $customer_email, int $tag_id)
    {
        $customerId = $this->getCustomerInfo($customer_email)['Id'];
        $this->getInfusionControllerInstance()->testInfusionsoftIntegrationAddTag($customerId, $tag_id);
    }

    /**
     * @param string $customer_email
     * @throws Exception
     */
    public function attachTagForFirstCourse(string $customer_email)
    {
        $firstCourse = $this->getFirstCourseKey();
        $firstModule = $this->getFirstModule($firstCourse);
        $tagIdOfFirstCourse = $this->getTagId($firstCourse, $firstModule->module_order);
        $this->attachTagToCustomer($customer_email, $tagIdOfFirstCourse);
    }

    /**
     * @param string $customer_email
     * @throws Exception
     */
    public function attachTagForAllCompletedCourses(string $customer_email)
    {
        $tagId = $this->getTagIdForAllCompletedCourses();
        $this->attachTagToCustomer($customer_email, $tagId);
    }

    /**
     * @param string $customer_email
     * @return bool
     * @throws Exception
     */
    public function isTheCustomerHasNotCompletedAnyCourses(string $customer_email)
    {
        $userModules = User::with('completed_modules')->where('email', $customer_email)->get();
        if ($userModules && $userModules->count() > 0) {
            $completedModules = $userModules->first()->completed_modules;
            if ($completedModules && $completedModules->count() > 0) {
                return true;
            }
        } else {
            return false;
        }
        return false;
    }

    /**
     * @param string $customer_email
     * @return bool
     * @throws Exception
     */
    public function isTheCustomerCompletedAllCourses(string $customer_email)
    {
        $courses = $this->getCustomersCourses($customer_email);
        foreach ($courses as $course) {
            $result = $this->isTheCustomerCompletedTheRelatedCourse($customer_email, $course);

            if (!$result) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $customer_email
     * @param string $course_key
     * @return bool
     * @throws Exception
     */
    public function isTheCustomerCompletedTheRelatedCourse(string $customer_email, string $course_key)
    {
        //The completion of the last modules of the course is the same as completing them all.
        // In either case, it needs to be the last module.
        // Therefore only one condition is being used in the scenario.
        $completedModulesOfUser = $this->getUserCompletedModules($customer_email, $course_key);
        $lastModule = $this->getLastCompletedModule($course_key, $completedModulesOfUser);
        return $lastModule;
    }

    /**
     * @param array $coursesOfCustomer
     * @param string $customer_email
     * @return bool
     * @throws Exception
     */
    public function assignNextAvailableModules(array $coursesOfCustomer, string $customer_email)
    {
        foreach ($coursesOfCustomer as $course_key) {
            $result = $this->isTheCustomerCompletedTheRelatedCourse($customer_email, $course_key);
            if ($result == false) {
                $nextModule = $this->getNextAvailableModule($customer_email, $course_key);
                $tagId = $this->getTagId($course_key, $nextModule->module_order);
                $this->attachTagToCustomer($customer_email, $tagId);
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $customer_email
     * @return Module|Model|object|null
     * @throws Exception
     */
    public function handleNextAvailableModulesOfNextCourse(string $customer_email)
    {
        $nextAvailableCourse = $this->getNextAvailableCourse($customer_email);
        $nextAvailableModule = $this->getFirstModule($nextAvailableCourse->course_key);
        if ($nextAvailableModule) {
            return $nextAvailableModule;
        } else {
            throw new Exception(__('messages.mnf'), 404);
        }
    }

    /**
     * @param string $customer_email
     * @param string $course_key
     * @return mixed
     * @throws Exception
     */
    public function handleNextAvailableModules(string $customer_email, string $course_key)
    {
        $completedModulesOfUser = $this->getUserCompletedModules($customer_email, $course_key);
        if (count($completedModulesOfUser) > 0) {
            $modules = Module::whereIn('id', $completedModulesOfUser->pluck('id'))->orderBy('module_order')->get();
            $nextAvailableModuleOrder = $modules->last()->module_order + 1;
        } else {
            $nextAvailableModuleOrder = 1;
        }
        $nextAvailableModule = Module::where('module_order', $nextAvailableModuleOrder)->get();
        if ($nextAvailableModule && $nextAvailableModule->count() > 0) {
            return $nextAvailableModule->first();
        } else {
            throw new Exception(__('messages.mnf'), 404);
        }
    }

    /** It decisions to attach tag for customer.
     *
     * @param array $coursesOfCustomer
     * @param string $customer_email
     * @throws Exception
     */
    public function handleTagAttachments(array $coursesOfCustomer, string $customer_email)
    {
        if ($this->isTheCustomerHasNotCompletedAnyCourses($customer_email) === false) {
            $this->attachTagForFirstCourse($customer_email);
        } else if ($this->isTheCustomerCompletedAllCourses($customer_email) === true) {
            $this->attachTagForAllCompletedCourses($customer_email);
        } else {
            $this->assignNextAvailableModules($coursesOfCustomer, $customer_email);
        }
    }


    /**It decisions next available course by user.
     * @param string $customer_email
     * @return bool
     * @throws Exception
     */
    public function getNextAvailableCourse(string $customer_email)
    {
        $courses = $this->getAllCourses($customer_email);
        foreach ($courses as $course) {
            $result = $this->isTheCustomerCompletedTheRelatedCourse($customer_email, $course->course_key);
            if ($result == false) {
                return $course;
            }
        }
        return null;
    }

    /**It fetches first module of specified course.
     * @param $course_key
     * @return Module|Model|object|null
     * @throws Exception
     */
    public function getFirstModule(string $course_key)
    {
        $modules = $this->getModuleModel($course_key);
        if ($modules && $modules->count() > 0) {
            return $modules->first();
        } else {
            throw new Exception(__('messages.mnfwc', ['where' => 'First', 'course_key' => $this->getCourseName($course_key)]), 404);
        }
    }

    /**It fetches last module of specified course.
     * @param string $course_key
     * @return Module
     * @throws Exception
     */
    public function getLastModule(string $course_key)
    {
        $modules = $this->getModuleModel($course_key);
        if ($modules && $modules->count() > 0) {
            return $modules->last();
        } else {
            throw new Exception(__('messages.mnfwc', ['where' => 'Last', 'course_key' => $this->getCourseName($course_key)]), 404);
        }
    }

    /**It fetches first course.
     * @return mixed
     * @throws Exception
     */
    public function getFirstCourse()
    {
        $courses = $this->getCourseModel();
        if ($courses && $courses->count() > 0) {
            return $courses->first();
        } else {
            throw new Exception(__('messages.fcnf'), 404);
        }
    }

    /**It fetches key of first course.
     * @return mixed
     * @throws Exception
     */

    public function getFirstCourseKey()
    {
        return $this->getFirstCourse()->course_key;
    }

    /**It fetches all courses of specified user.
     * @param string $customer_email
     * @return Module[]|Collection|\Illuminate\Support\Collection
     * @throws Exception
     */
    public function getAllCourses(string $customer_email)
    {
        $courses = $this->getCourseModel($customer_email);
        if ($courses && $courses->count() > 0) {
            return $courses;
        } else {
            throw new Exception(__('messages.cnfac'), 404);
        }
    }

    /**It generates tag text by course_key and module_order to attach for customer.
     * @param string $course_key
     * @param int $module_order
     * @return
     * @throws Exception
     */

    public function getTagId(string $course_key, int $module_order)
    {
        $tagTextTemplate = 'Start ' . $this->getCourseName($course_key) . ' Module ' . $module_order . ' Reminders';
        $tags = Tag::where('name', $tagTextTemplate)->get();
        if ($tags && $tags->count() > 0) {
            return $tags->first()->id;
        } else {
            throw new Exception(__('messages.cnft', ['tagTextTemplate' => $tagTextTemplate]), 404);
        }
    }

    /**It fetches the tag text of the completed course.
     * @return mixed
     */

    public function getTagIdForAllCompletedCourses()
    {
        $tag = Tag::where('name', __('messages.mrc'))->get()->first();
        return $tag->id;
    }

    /**It converts course name to uppercase to use in the database search.
     * @param string $course_key
     * @return string
     */

    public function getCourseName(string $course_key)
    {
        return strtoupper($course_key);
    }

    /**It fetches module information from database by course_key
     * @param string $course_key
     * @return Module[]|Collection|\Illuminate\Support\Collection
     */

    public function getModuleModel(string $course_key)
    {
        return Module::where('course_key', $course_key)->orderBy('module_order')->get();
    }

    /**It fetches course information from database.
     * @return Module[]|Collection|\Illuminate\Support\Collection
     */
    public function getCourseModel()
    {
        return Module::select(['course_key', 'course_order'])
            ->groupBy(['course_key', 'course_order'])
            ->orderBy('course_order')
            ->get();
    }

    /** It fetches customer completed modules on course basis.
     * @param string $customer_email
     * @param string $course_key
     * @return Collection
     */
    public function getUserCompletedModules(string $customer_email, string $course_key): Collection
    {
        $user = User::where('email', $customer_email)->first();
        $modulesOfUser = $user->completed_modules()->where('course_key', $course_key)->orderBy('module_order')->get();
        return $modulesOfUser;
    }

    /**It fetches the customer's detailed information via Infusion API
     * @param string $customer_email
     * @return array
     * @throws Exception
     */
    public function getCustomerInfo(string $customer_email)
    {
        $response = $this->getInfusionControllerInstance()->testInfusionsoftIntegrationGetEmail($customer_email);

        if (is_bool($response->getData())) {
            return null;
        }

        return $response->getData(true);
    }

    /** It fetches the last completed module of the specified course.
     * @param string $course_key
     * @param User $completedModulesOfUser
     * @return bool
     * @throws Exception
     */
    public function getLastCompletedModule(string $course_key, $completedModulesOfUser)
    {
        $lastModule = $this->getLastModule($course_key);
        $result = $completedModulesOfUser->where('id', $lastModule->id)->count();
        if ($result > 0) {
            return true;
        }
        return false;
    }

    /**It decisions the next available module of the customer specified course.
     * @param string $customer_email
     * @param string $course_key
     * @return int
     * @throws Exception
     */
    public function getNextAvailableModule(string $customer_email, string $course_key)
    {
        $result = $this->isTheCustomerCompletedTheRelatedCourse($customer_email, $course_key);
        if ($result == true) {
            return $this->handleNextAvailableModulesOfNextCourse($customer_email, $course_key);
        } else {
            return $this->handleNextAvailableModules($customer_email, $course_key);
        }
    }

    /**It fetches customer's courses via Infusion API.
     * @param string $customer_email
     * @return array
     * @throws Exception
     */
    public function getCustomersCourses(string $customer_email)
    {
        if (!$customer_email) {
            throw new Exception(__('messages.cesbn'));
        }
        $customer = $this->getCustomerInfo($customer_email);
        $result = explode(",", $customer['_Products']);
        if ($result && count($result) > 0 && $result[0] != '') {
            return $result;
        } else {
            throw new Exception(__('messages.cnfacfu'), 404);
        }
    }

    private function getInfusionControllerInstance()
    {
        return (new InfusionsoftController(app(InfusionsoftHelper::class)));
    }
}