<?php
/* Swagger documentation */

/**
 * @OA\Info(title="LetterYourself API", version="0.1")
 * @OA\OpenApi(
 *   @OA\Server(url="http://localhost/letteryourself/api/", description="Development Environment"),
 *   @OA\Server(url="Production URL HERE", description="Production Environment"),
 * ),
 * @OA\SecurityScheme(
 *      securityScheme="ApiKeyAuth",
 *      in="header",
 *      name="Authentication",
 *      type="apiKey"
 * )
 */

/**
 * @OA\Get(path="/admin/accounts", tags={"x-admin","account"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(type="integer", in="query", name="offset", default=0, description="Offset for pagination"),
 *     @OA\Parameter(type="integer", in="query", name="limit", default=10, description="Limit for pagination"),
 *     @OA\Parameter(type="string", in="query", name="search", description="Search string for account. Case insensetive."),
 *     @OA\Parameter(type="string", in="query", name="order", default="-id", description="Sorting : '-column_name' ascending order, '+column_name' descending order"),
 *     @OA\Response(response="200", description="List accounts from database.")
 * )
 */
Flight::route('GET /admin/accounts', function () {
    $offset = Flight::query('offset', 0);
    $limit = Flight::query('limit', 10);
    $search = Flight::query('search');
    $order = Flight::query('order', "-id");
    Flight::json(Flight::accountService()->get_accounts($search, $offset, $limit, $order));
});

/**
 * @OA\Get(path="/admin/accounts/{id}", tags={"x-admin","account"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter( type="integer", in="path", name="id", default=1, description="Id of account"),
 *     @OA\Response(response="200", description="Get spesific account from database.")
 * )
 */
Flight::route('GET /admin/accounts/@id', function ($id) {
    Flight::json(Flight::accountService()->get_by_id($id));
});

/**
 * @OA\Post(path="/admin/accounts", tags={"x-admin","account"}, security={{"ApiKeyAuth": {}}},
 *    @OA\RequestBody(description="Account info", required=true,
 *        @OA\MediaType(
 *            mediaType="application/json",
 *            @OA\Schema(
 *                @OA\Property(property="name", required="true", type="string", example="suleyman_oner", description="Name of account"),
 *                @OA\Property(property="status", type="string", example="ACTIVE", description="Status of account")
 *            )
 *        )
 * ),
 *    @OA\Response(response="200", description="Account added with assigned id into database.")
 * )
 */
Flight::route('POST /admin/accounts', function () {
    Flight::json(Flight::accountService()->add(Flight::request()->data->getData()));
});

/**
 * @OA\Put(path="/admin/accounts/{id}", tags={"x-admin","account"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter( @OA\Schema(type="integer"), in="path", name="id", default=1),
 *     @OA\RequestBody(description="Account info that is going to be updated.", required=true,
 *        @OA\MediaType(
 *            mediaType="application/json",
 *            @OA\Schema(
 *                @OA\Property(property="name", required="true", type="string", example="suleyman_oner", description="Name of account"),
 *                @OA\Property(property="status", type="string", example="ACTIVE", description="Status of account")
 *            )
 *        )
 * ),
 *     @OA\Response(response="200", description="Update account based on id.")
 * )
 */
Flight::route('PUT /admin/accounts/@id', function ($id) {
    Flight::json(Flight::accountService()->update($id, Flight::request()->data->getData()));
});

/**
 * @OA\Get(path="/person/account", tags={"x-person", "account"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Response(response="200", description="Fetch user account")
 * )
 */
Flight::route('GET /person/account', function () {
    $account_id = Flight::get('person')['aid'];
    Flight::json(Flight::accountService()->get_by_id($account_id));
});
