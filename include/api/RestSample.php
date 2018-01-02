<?php
namespace FitApp\api;

use FitApp\api\RestService;
use FitApp\classes\SelfServiceApp;
use FitApp\classes\Project;

/*

Service is responsible for the following calls:
campaigns
    GET /campaigns - get a list of current campaigns (this is an optional endpoint)
    POST /campaigns - create a new campaign

campaigns/{ident}
    GET /campaigns/{ident} - get all information for a campaign
    PUT /campaigns/{ident} - update all information for a campaign 
    i'm expecting the "save or approve" call will be this api call with an updated status
    DELETE /campaigns/{ident} - delete the campaign data (need to build)

*/

class RestCampaigns extends RestService {
    protected $supportedMethods = ['GET', 'POST', 'PUT', 'OPTIONS', 'DELETE'];
    protected $nounslist = ['campaigns',  'user_id', 'status', 'deal_id'];

    /**
     * @param $arguments
     * @param $accept
     */
    public function performGet($arguments, $accept) {
        $success = true;
        if ($arguments['campaigns'] == NULL) {
            $message = "All Campaigns for User";
            $projects = SelfServiceApp::getUserProjects($arguments['user_id']);
            $data = $projects;
        } else {
            $projects = SelfServiceApp::getProjectData($arguments['campaigns']);
            $data = $projects;
        }

        $this->sendResponse($success, $message, $data, $arguments);

    }

    /**
     * @param $arguments
     * @param $accept  
     */
    public function performPost($arguments, $accept) {
        $success = TRUE;
        $post_campaigns = $arguments['data'];
        if (!is_array($post_campaigns)) {
            $post_campaigns = [$post_campaigns];
        }
        $project_ids = [];
        $errors = [];
        $message = "Campaign(s) created from data";
        foreach ($post_campaigns as $campaign) {
            //sometimes we get something deeply nested
            while (is_array($campaign) && !isset($campaign['CampaignInfo_product_name'])) {
                $campaign = array_shift($campaign);
            }
            if (!is_array($campaign)) {
                error_log("CampaignInfo_product_name not found, when campaign submitted returning false");
                return false;
            }
            $campaign['data'] = $campaign;
            $name = strip_tags($campaign['data']['CampaignInfo_product_name']);
            $project_id = NULL;
            if (preg_match("/#([0-9]+)#/", $name, $matches) || $campaign['data']['campaign_id'] > 0) {
                if ($campaign['data']['campaign_id'] > 0) {
                    $project_id = $campaign['data']['campaign_id']; 
                } else {
                    $project_id = $matches[1];
                }
                $Project = Project::get($project_id);
                if (!$Project || $Project->getField('id') == NULL || $Project->getField('status_id') >= Project::LIVE) { // it doesn't exist, or is already running
                    $project_id = NULL;
                }
                $SSA = SelfServiceApp::getSSAFromProjectId($project_id);
                if ($SSA && $SSA->getField('status') == 'a') {
                    $project_id = NULL;
                }
            }
            if ($project_id) {
                $campaign['data']['CampaignInfo_product_name'] = trim(str_replace($matches[0], '', $name));
                $campaign['data']['project_id'] = $project_id;
                $message .= " Project $project_id found in data. ";
            }
            $project_id = SelfServiceApp::createNewProject($arguments['user_id'], $campaign);
            if ($project_id) {
                $project_ids[] = $project_id;
            } else {
                // @todo determine what to send back
                $errors[] = $campaign['name'];
            }
        }
        if (count($project_ids) == 0) {
            $success = FALSE;
            $message = "Unable to create campaign from overview data";
        }
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
        $data = ['campaigns' => $arguments['campaigns'], 'user_id'=>$arguments['user_id']];
        if (!isset($arguments['user_id'])) {
            $data['header'] = 'HTTP/1.1 401 Unauthorized';
            $message = "User token expired";
        }
        if (isset($arguments['data']) && $arguments['data'] !='') {
            $result = SelfServiceApp::setProjectData($arguments['campaigns'], $arguments['user_id'], $arguments['data']);
            if (!$result) {
                $message = "Unable to PUT campaign content for {$arguments['campaigns']}";
                $success = FALSE;
            }
            if(!isset($arguments['status'])) {
               $decoded = json_decode($arguments['data'], TRUE);
               if (isset($decoded['status'])) {
                   $arguments['status'] = strtolower($decoded['status']);
                   $arguments['deal_id'] = $decoded['deal_id'];
               }
            }
        }
        if (isset($arguments['status'])) {
            $project_id = $arguments['campaigns'];
            $SSA = SelfServiceApp::getSSAFromProjectId($project_id);
            $status = FALSE;
            if (isset($arguments['deal_id'])) {
                $SSA->setField('deal_id',$arguments['deal_id']);
                $SSA->save();
                $message .= " and deal_id added";
            }
            if ($arguments['status'] == 'submitted' && !isset($arguments['deal_id']) && !$SSA->hasDeal()) {
                $success = FALSE;
                $message = "Deal ID not sent through with submitted campaign";
                $data['header'] = 'HTTP/1.1 400 Bad Request';
            } elseif ($SSA) {
                $message .= " $project_id set to {$arguments['status']}"; 
                $SSA->setField('status',$arguments['status']);
                $SSA->save();
                $status = $SSA->getField('status');
                $message .= " and complete";
                $success = TRUE;
            }
            if ($SSA && ($status == 'rejected' || $status == 'approved')) {
                $SSA->setField('approval_id', $arguments['user_id']);
                $SSA->save();
            }
            if ($SSA && $SSA->message != '') {
                $success = FALSE;
                $data['header'] = 'HTTP/1.1 400 Bad Request';
                $message = $SSA->message;
            }
            if (is_null($SSA)) {
                $success = FALSE;
                $data['header'] = 'HTTP/1.1 400 Bad Request';
                $message = $SSA->message;
            }
        }

        $this->sendResponse($success, $message, $data, $arguments);
    }
    
    /**
     * @param $arguments
     * @param $accept
     */
    public function performDelete($arguments, $accept) {
        $success = TRUE;
        if ($arguments['campaigns'] == NULL) {
            $message = "No id specified for delete";
            $success = FALSE;
            $data = ['campaigns' => NULL];
        } else {
            $message = "Specific campaign(s) marked as deleted";
            $project_list = explode(',', $arguments['campaigns']);
            foreach ($project_list as $project_id) {
                $SSA = SelfServiceApp::getSSAFromProjectId($project_id);
                if (!$SSA) {
                    $success = FALSE;
                    $errors = ['campaigns' => $project_id];
                } else {
                    $SSA->setField('status', 'delete');
                    $SSA->save();
                    $data = ['campaigns' => $SSA->getField('id')];
                }
            }
        }
    
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

