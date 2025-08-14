<?php 

class ErrorController {
    public function notFound() {
        http_response_code(404);
        $data = [
            "message" => "The requested resource was not found."
            "error" => "404 Not Found"
        ];
        loadView("error", $data);
    }

    public function serverError() {
        http_response_code(500);
        $data = [
            "message" => "An unexpected error occurred on the server.",
            "error" => "500 Internal Server Error"
        ];
        loadView("error", $data);
    }

    public function forbidden() {
        http_response_code(403);
        $data = [
            "message" => "You do not have permission to access this resource.",
            "error" => "403 Forbidden"
        ];
        loadView("error", $data);
    }

    public function unauthorized() {
        http_response_code(401);
        $data = [
            "message" => "You must be logged in to access this resource.",
            "error" => "401 Unauthorized"
        ];
        loadView("error", $data);
    }

    public function methodNotAllowed() {
        http_response_code(405);
        $data = [
            "message" => "The requested method is not allowed for this resource.",
            "error" => "405 Method Not Allowed"
        ];
        loadView("error", $data);
    }

    public function badRequest() {
        http_response_code(400);

        $data = [
            "message" => "The request could not be understood by the server due to malformed syntax.",
            "error" => "400 Bad Request"
        ];
        loadView("error", $data);
    }
}