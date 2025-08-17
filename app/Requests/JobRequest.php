<?php 

namespace App\Requests;

use \Respect\Validation\Validator;


/*
* JobRequest: handles validation for job-related requests
*/
class JobRequest{
    /**
     * Validate job creation request parameters.
     *
     * @param array $params
     * @return bool
     */
    public static function validateCreate(array $params): bool {
        $body = $params["body"] ?? [];

        // Validate required fields
        $requiredFields = ["title", "description", "salary", "requirements", "benefits", "company", "address", "city", "state", "phone", "email"];
        foreach ($requiredFields as $field) {
            if (!isset($body[$field]) || empty($body[$field])) {
                return false;
            }
        }

        // Validate phone format (optional, can be customized)
        if (isset($body["phone"]) && !preg_match('/^\+?[0-9\s\-()]+$/', $body["phone"])) {
            return false;
        }

        // validate description length 
        if (isset($body["description"]) && strlen($body["description"]) < 50) {
            return false;
        }

        // validate salary format (optional, can be customized)
        if (isset($body["salary"]) && !is_numeric($body["salary"])) {
            return false;
        }

        // validate benefits format (optional, can be customized)
        if (isset($body["benefits"]) && !is_array($body["benefits"])) {
            return false;
        }

        // validate tags format (optional, can be customized)
        if (isset($body["tags"]) && !is_array($body["tags"])) {
            return false;
        }

        // validate state 
        if (isset($body["state"]) && len($body["state"]) !== 2) {
            return false;
        }

        // validate city length 
        if (isset($body["city"]) && strlen($body["city"]) < 20) {
            return false;
        }

        // validate company name length
        if (isset($body["company"]) && strlen($body["company"]) < 20) {
            return false;
        }

        // validate title length
        if (isset($body["title"]) && strlen($body["title"]) < 10
            && strlen($body["title"]) > 100) {
            return false;
        }

        // Validate email format
        if (!Validator::email()->validate($body["email"])) {
            return false;
        }

        return true;
    }

    /**
     * Validate job details request parameters.
     *
     * @param array $params
     * @return bool
     */
    public static function validateGetDetails(array $params): bool {
        // Validate that job ID is provided
        if (!isset($params["pathParams"]["id"]) || empty($params["pathParams"]["id"])) {
            return false;
        }

        // Optionally, you can add more validation for the job ID format
        // For example, if it's expected to be a UUID or numeric
        return true;
    }

    /**
     * Validate job update request parameters.
     *
     * @param array $params
     * @return bool
     */

    public static function validateUpdate(array $params): bool {
        $body = $params["body"] ?? [];
        
        // Validate that job ID is provided
        if (!isset($params["pathParams"]["id"]) || empty($params["pathParams"]["id"])) {
            return false;
        }

        return $this->validateCreate($params); // Reuse create validation
    }

    public static function validateUpdateGet(array $params): bool {
        // Validate that job ID is provided
        if (!isset($params["pathParams"]["id"]) || empty($params["pathParams"]["id"])) {
            return false;
        }

        // Optionally, you can add more validation for the job ID format
        // For example, if it's expected to be a UUID or numeric
        return true;
    }

    public static function validateDelete(array $params): bool {
        // Validate that job ID is provided
        if (!isset($params["pathParams"]["id"]) || empty($params["pathParams"]["id"])) {
            return false;
        }

        // Optionally, you can add more validation for the job ID format
        // For example, if it's expected to be a UUID or numeric
        return true;
    }
}