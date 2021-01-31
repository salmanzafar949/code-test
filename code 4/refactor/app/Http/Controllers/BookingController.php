<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * @var authenticatedUser
     */
    protected $__authenticatedUser;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
        $this->__authenticatedUser = request('__authenticatedUser');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        /**
         * $user_id is not defined
         * so this wont work
         * if it's defined then instead of '=' it should '=='
        */
        $user_id = 1;

        if($user_id == $request->get('user_id'))
        {
            $response = $this
                ->repository
                ->getUsersJobs($user_id);
        }

        if($this->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID'))
        {
            $response = $this
                ->repository
                ->getAll($request);
        }

        return response($response);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $job = $this->repository->with('translatorJobRel.user')->find($id);

        return response($job);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $response = $this
            ->repository
            ->store(
                $this->__authenticatedUser,
                $request->all()
            );

        return response($response);

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $response = $this
            ->repository
            ->updateJob($id,
                $request->except(['_token', 'submit']),
                $this->__authenticatedUser);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $response = $this
            ->repository
            ->storeJobEmail($request->all());

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        /**
         * $user_id is not defined
         * so this wont work
         * if it's defined then instead of '=' it should '=='
         */
        $user_id = 1;

        return $user_id == $request->get('user_id')
            ?
            response(
                $this
                    ->repository
                    ->getUsersJobsHistory($user_id, $request)
            )
            : null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $response = $this
            ->repository
            ->acceptJob(
                $request->all(),
                $this->__authenticatedUser
            );

        return response($response);
    }

    public function acceptJobWithId(Request $request)
    {
        $response = $this
            ->repository
            ->acceptJobWithId(
                $request->get('job_id'),
                $this->__authenticatedUser
            );

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $response = $this
            ->repository
            ->cancelJobAjax(
                $request->all(),
                $this->__authenticatedUser
            );

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $response = $this
            ->repository
            ->endJob($request->all());

        return response($response);

    }

    public function customerNotCall(Request $request)
    {
        $response = $this
            ->repository
            ->customerNotCall($request->all());

        return response($response);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $response = $this
            ->repository
            ->getPotentialJobs($this->__authenticatedUser);

        return response($response);
    }

    public function distanceFeed(Request $request)
    {
        return $this->handleDistanceFeed($request->all());

    }

    protected function handleDistanceFeed($data)
    {
        if (isset($data['distance']) && $data['distance'] != "") {
            $distance = $data['distance'];
        } else {
            $distance = "";
        }
        if (isset($data['time']) && $data['time'] != "") {
            $time = $data['time'];
        } else {
            $time = "";
        }
        if (isset($data['jobid']) && $data['jobid'] != "") {
            $jobid = $data['jobid'];
        }

        if (isset($data['session_time']) && $data['session_time'] != "") {
            $session = $data['session_time'];
        } else {
            $session = "";
        }

        if ($data['flagged'] == 'true') {
            if($data['admincomment'] == '') return "Please, add comment";
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }

        if ($data['manually_handled'] == 'true') {
            $manually_handled = 'yes';
        } else {
            $manually_handled = 'no';
        }

        if ($data['by_admin'] == 'true') {
            $by_admin = 'yes';
        } else {
            $by_admin = 'no';
        }

        if (isset($data['admincomment']) && $data['admincomment'] != "") {
            $admincomment = $data['admincomment'];
        } else {
            $admincomment = "";
        }
        if ($time || $distance) {

            $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $distance, 'time' => $time));
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {

            $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin));
        }

        return response('Record updated!');
    }

    public function reopen(Request $request)
    {
        $response = $this
            ->repository
            ->reopen($request->all());

        return response($response);
    }

    public function resendNotifications(Request $request)
    {
        $job = $this->repository->find($request->jobid);
        $job_data = $this->repository->jobToData($job);

        $this
            ->repository
            ->sendNotificationTranslator($job, $job_data, '*');

        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $job = $this->repository->find($request->jobid);
        $this
            ->repository
            ->jobToData($job);

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }
}