<?php


namespace App\Services;

use App\Http\Helpers\HttpClientHelper;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * It manage the decision logic related to Reminder Assigner.
 */
class ReminderService
{
    public function assignModuleReminder(string $customer_email)
    {
    }

    /**
     * @param string $customer_email
     * @param int $tag_id
     * @throws Exception
     */
    private function attachTagToCustomer(string $customer_email, int $tag_id)
    {
        $customerId = $this->getCustomerInfo($customer_email)['Id'];
        $http = new HttpClientHelper();
        $http->getWithUrl('infusionsoft_test_add_tag/' . $customerId . '/' . $tag_id);
    }

    /**
     * @param string $customer_email
     * @throws Exception
     */
    private function attachTagForFirstCourse(string $customer_email)
    {
        $firstCourse = $this->getFirstCourseKey();
    }

    /**
     * @param string $customer_email
     * @throws Exception
     */
    private function attachTagForAllCompletedCourses(string $customer_email)
    {
    }

    /**
     * @param string $customer_email
     * @return bool
     * @throws Exception
     */
    private function isTheCustomerHasNotCompletedAnyCourses(string $customer_email)
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
    private function isTheCustomerCompletedAllCourses(string $customer_email)
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
    private function isTheCustomerCompletedTheRelatedCourse(string $customer_email, string $course_key)
    {
    }

    /**
     * @param array $coursesOfCustomer
     * @param string $customer_email
     * @return bool
     * @throws Exception
     */
    private function assignNextAvailableModules(array $coursesOfCustomer, string $customer_email)
    {
    }

    /**
     * @param string $customer_email
     * @return Module|Model|object|null
     * @throws Exception
     */
    private function handleNextAvailableModulesOfNextCourse(string $customer_email)
    {
    }

    /**
     * @param string $customer_email
     * @param string $course_key
     * @return mixed
     * @throws Exception
     */
    private function handleNextAvailableModules(string $customer_email, string $course_key)
    {
    }

    /** It decisions to attach tag for customer.
     *
     * @param array $coursesOfCustomer
     * @param string $customer_email
     * @throws Exception
     */
    private function handleTagAttachments(array $coursesOfCustomer, string $customer_email)
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
    private function getNextAvailableCourse(string $customer_email)
    {
        return 1;
    }

    /**It fetches first module of specified course.
     * @param $course_key
     * @return Module|Model|object|null
     * @throws Exception
     */
    private function getFirstModule(string $course_key)
    {
        return 1;
    }

    /**It fetches last module of specified course.
     * @param string $course_key
     * @return Module
     * @throws Exception
     */
    private function getLastModule(string $course_key)
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
    private function getFirstCourse()
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

    private function getFirstCourseKey()
    {
        return $this->getFirstCourse()->course_key;
    }

    /**It fetches all courses of specified user.
     * @param string $customer_email
     * @return void
     */

    private function getAllCourses(string $customer_email)
    {
    }

    /**It generates tag text by course_key and module_order to attach for customer.
     * @param string $course_key
     * @param int $module_order
     * @return
     * @throws Exception
     */

    private function getTagId(string $course_key, int $module_order)
    {
    }

    /**It fetches the tag text of the completed course.
     * @return mixed
     */

    private function getTagIdForAllCompletedCourses()
    {
    }

    /**It converts course name to uppercase to use in the database search.
     * @param string $course_key
     * @return string
     */

    private function getCourseName(string $course_key)
    {
        return strtoupper($course_key);
    }

    /**It fetches module information from database by course_key
     * @param string $course_key
     * @return Module[]|Collection|\Illuminate\Support\Collection
     */

    private function getModuleModel(string $course_key)
    {
        return Module::where('course_key', $course_key)->orderBy('module_order')->get();
    }

    /**It fetches course information from database.
     * @return Module[]|Collection|\Illuminate\Support\Collection
     */
    private function getCourseModel()
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
    private function getUserCompletedModules(string $customer_email, string $course_key): Collection
    {
        $users = User::where('email', $customer_email)->get();
        $user = User::find($users->first()->id);
        $modulesOfUser = $user->completed_modules()->where('course_key', $course_key)->orderBy('module_order')->get();
        return $modulesOfUser;
    }

    /**It fetches the customer's detailed information via Infusion API
     * @param string $customer_email
     * @return array
     * @throws Exception
     */
    private function getCustomerInfo(string $customer_email): array
    {
        $http = new HttpClientHelper();
        $customer = $http->getWithUrl('/infusionsoft_test_get_by_email/' . $customer_email . '');
        return $customer;
    }

    /** It fetches the last completed module of the specified course.
     * @param string $course_key
     * @param User $completedModulesOfUser
     * @return bool
     * @throws Exception
     */
    private function getLastCompletedModule(string $course_key, $completedModulesOfUser)
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
    private function getNextAvailableModule(string $customer_email, string $course_key)
    {
    }

    /**It fetches customer's courses via Infusion API.
     * @param string $customer_email
     * @return array
     * @throws Exception
     */
    private function getCustomersCourses(string $customer_email)
    {
        if (!$customer_email) {
            throw new Exception(__('messages.cesbn'));
        }

        $customer = $this->getCustomerInfo($customer_email);
        $result = explode(",", $customer['_Products']);
        if ($result && count($result) > 0 && $result[0] != '') {
            return $result;
        } else {
            throw new Exception(__('messages.cnfacfu'));
        }
    }
}