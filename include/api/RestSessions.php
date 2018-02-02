<?php
namespace Fitapp\api;

use Fitapp\classes\Sets;
use Fitapp\classes\WorkoutInstances;

/*
Service is responsible for the following calls:
sessions
    GET /sessions/workout/:id - get sessions for a particular workout id
    POST /sessions/workout/ - create a new session (id in payload)
    
sets/:id
    GET /sessions/:id - get a particular session
    PUT /sessions/:id - update a session
    DELETE /sessions/:id - delete the session

*/

class RestSessions extends RestService {
    protected $supportedMethods = ['GET', 'POST', 'PUT', 'OPTIONS', 'DELETE'];
    protected $nounsList = ['ids', 'workout', 'exercises', 'group', 'types', 'units'];

    /**
     * @param $arguments
     * @param $accept
     */
    public function performGet($arguments, $accept) {
        $success = true;
        $data = [];
        if (array_key_exists('types', $arguments)) {
            $message = 'Set Types';
            $data = Sets::$set_types;
            $this->sendResponse($success, $message, $data, $arguments);
        }

        if (array_key_exists('units', $arguments)) {
            // @todo possibly take type of time/distance/[weight]
            $message = 'Set Units';
            $data = array_values(Sets::$units);
            $this->sendResponse($success, $message, $data, $arguments);
        }

        if ($arguments['workout'] > 0) {
            $WorkoutInstance = WorkoutInstances::get($arguments['workout']);
            $message = 'Sets for this workout';
            if ($WorkoutInstance) {
                $data = $WorkoutInstance->getSets();
            } else {
                $success = false;
                $message = "Workout instance {$arguments['workout']} does not exist";
            }
            $this->sendResponse($success, $message, $data, $arguments);
        } elseif ($arguments['ids'] > 0) {
            $Set = Sets::get($arguments['sets']);
            if ($Set) {
                $data = $Set->getFields();
                $message = "Set data";
            } else {
                $success = false;
                $message = "Set does not exist";
            }
            // @todo build out the GET for group+exercise(s)
        } else {
            $success = false;
            $message = "No set specified";
        }

        $this->sendResponse($success, $message, $data, $arguments);

    }

    /**
     * @param $arguments
     * @param $accept  
     */
    public function performPost($arguments, $accept) {

        $data = ['campaigns' => $project_ids, 'errors'=>$errors];

        $this->sendResponse($success, $message, $data, $arguments);

    }

    /**
     * @param $arguments
     * @param $accept
     */
    public function performPut($arguments, $accept) {
        $success = TRUE;
        $message = "Campaign {$arguments['campaigns']} PUT";


        $this->sendResponse($success, $message, $data, $arguments);
    }
    
    /**
     * @param $arguments
     * @param $accept
     */
    public function performDelete($arguments, $accept) {
        $success = TRUE;

    
        $this->sendResponse($success, $message, $data, $arguments);
    }
    
}

/**
 * @api {get} /campaigns    Get All Campaigns for a user
 * @apiExample {url} Short Example:
 *  /campaigns
 * @apiName GetAllCampaigns
 * @apiGroup Campaigns
 *
 * @apiSuccess {String} status      Success or failure
 * @apiSuccess {Bool}   success     Successful?
 * @apiSuccess {String} message     Response message of what was asked for
 * @apiSuccess {Json}   data        Json array of Industries/Sub Industries
 * @apiSuccess {String} debug       Json array of debug info, what is passed along
 *
 * @apiSuccessExample {json} Success-Response:
 *  HTTP/1.1 200 OK
 *  {
 *    "status": "success",
 *    "success": true,
 *    "message": "All Campaigns for User",
 *    "error": [],
 *    "data": [],
 *
 *    "debug": {
 *    }
 *  }
 *
 *
 */

/**
 * @api {get} /campaigns/:project_id    Get Campaign Data
 * @apiExample {url} Short Example:
 *  /campaigns
 * @apiName GetOneCampaign
 * @apiGroup Campaigns
 *
 * @apiParam project_id Project id of the campaign
 * @apiDescription Returns data for campaign
 *
 * @apiSuccess {String} status      Success or failure
 * @apiSuccess {Bool}   success     Successful?
 * @apiSuccess {String} message     Response message of what was asked for
 * @apiSuccess {Json}   data        Json array of Industries/Sub Industries
 * @apiSuccess {String} debug       Json array of debug info, what is passed along
 *
 * @apiSuccessExample {json} Success-Response:
 *  HTTP/1.1 200 OK
 *  {
 *    "status": "success",
 *    "success": true,
 *    "message": "Get Campaign Data",
 *    "error": [],
 *    "data": [],
 *
 *    "debug": {
 *    }
 *  }
 *
 *
 */
/**
 * @api {put} /campaigns/:project_id    Update Campaign Data
 * @apiExample {url} Short Example:
 *  /campaigns
 * @apiName UpdateOneCampaign
 * @apiGroup Campaigns
 *
 * @apiParam project_id Project id of the campaign
 * @apiDescription Returns data for campaign
 *
 * @apiSuccess {String} status      Success or failure
 * @apiSuccess {Bool}   success     Successful?
 * @apiSuccess {String} message     Response message of what was asked for
 * @apiSuccess {Json}   data        Json array of Industries/Sub Industries
 * @apiSuccess {String} debug       Json array of debug info, what is passed along
 *
 * @apiSuccessExample {json} Success-Response:
 *  HTTP/1.1 200 OK
 *  {
 *    "status": "success",
 *    "success": true,
 *    "message": "Put Campaign Data",
 *    "error": [],
 *    "data": [],
 *
 *    "debug": {
 *    }
 *  }
 *
 *
 */

/**
 * @api {post} /campaigns/    Save New Campaign
 * @apiExample {url} Short Example:
 *  /campaigns
 * @apiName SaveCampaign
 * @apiGroup Campaigns
 *
 * @apiDescription Returns campaign id (project_id) for campaign
 *
 * @apiSuccess {String} status      Success or failure
 * @apiSuccess {Bool}   success     Successful?
 * @apiSuccess {String} message     Response message of what was asked for
 * @apiSuccess {Json}   data        Json array of Industries/Sub Industries
 * @apiSuccess {String} debug       Json array of debug info, what is passed along
 *
 * @apiSuccessExample {json} Success-Response:
 *  HTTP/1.1 200 OK
 *  {
 *    "status": "success",
 *    "success": true,
 *    "message": "Campaign created from data",
 *    "error": [],
 *    "data": ['campaigns':123],
 *
 *    "debug": {
 *    }
 *  }
 *
 *
 */

