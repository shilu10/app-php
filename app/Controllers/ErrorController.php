<?php 


namespace App\Controllers;

/*
* ErrorController: manages the errors in router and other controllers
*/
class ErrorController {

    /*
    * static function notFound: for errors where resources are not found eg: /details/djdjdjdjd -> no data.
    * @params: null
    * @return: null
    */
    public static function notFound() {
        http_response_code(404);
        $data = [
            "message" => "The requested resource was not found.",
            "error" => "404 Not Found"
        ];
        loadView("error", $data);
    }

    /*
    * static function serverError: for errors Internal server error. eg: database initialization error or queue downtime.
    * @params: null
    * @return: null
    */
    public static function serverError() {
        http_response_code(500);
        $data = [
            "message" => "An unexpected error occurred on the server.",
            "error" => "500 Internal Server Error"
        ];
        loadView("error", $data);
    }

    /*
    * static function forbidden: for errors where users doesnt have permission to access resources eg: deleting job post which is from another user.
    * @params: null
    * @return: null
    */
    public static function forbidden() {
        http_response_code(403);
        $data = [
            "message" => "You do not have permission to access this resource.",
            "error" => "403 Forbidden"
        ];
        loadView("error", $data);
    }

    /*
    * static function unauthorized: for errors user is not logged in eg: user must login to post a job.
    * @params: null
    * @return: null
    */
    public static function unauthorized() {
        http_response_code(401);
        $data = [
            "message" => "You must be logged in to access this resource.",
            "error" => "401 Unauthorized"
        ];
        loadView("error", $data);
    }

    /*
    * static function methodNotAllowed: for errors specific method http verb is not allowed for a resource, eg: POSt job where we only support PUT.
    * @params: null
    * @return: null
    */
    public static function methodNotAllowed() {
        http_response_code(405);
        $data = [
            "message" => "The requested method is not allowed for this resource.",
            "error" => "405 Method Not Allowed"
        ];
        loadView("error", $data);
    }

    /*
    * static function badRequest: for errors user is not logged in eg: user must login to post a job.
    * @params: null
    * @return: null
    */
    public static function badRequest() {
        http_response_code(400);

        $data = [
            "message" => "The request could not be understood by the server due to malformed syntax.",
            "error" => "400 Bad Request"
        ];
        loadView("error", $data);
    }
}