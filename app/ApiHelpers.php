<?php
namespace App;
class ApiHelpers
{
    public const API_RESP_MSG_GET_SUCCESS               = "Data fetched successfully.";
    public const API_RESP_MSG_UPLOAD_SUCCESS            = "Upload operation finished.";
    public const API_RESP_MSG_POST_SUCCESS              = "Item stored.";
    public const API_RESP_MSG_UPDATE_SUCCESS            = "Item updated.";
    public const API_RESP_MSG_DELETE_SUCCESS            = "Item deleted.";
    public const API_RESP_MSG_NOT_FOUND                 = "Item(s) not found.";
    public const API_RESP_MSG_BAD_REQUEST               = "Bad request.";
    public const API_RESP_MSG_EMPTY                     = "No item.";
    public const API_RESP_MSG_CONFLICT                  = "Item already exists.";
    public const API_RESP_MSG_CONFLICT_UNIQUE_NAME      = "The name is already taken.";
    public const API_RESP_MSG_INTERNAL_SERVER_ERROR     = "Internal server error";
    public const API_RESP_MSG_RESTORE_SUCCESS            = "Item restored.";
    public static function createApiResponse(int $code, $msg, $data)
    {
        return response()->json([
            "code"      => $code,
            "message"   => $msg,
            "data"      => $data,
        ], $code);
    }
}