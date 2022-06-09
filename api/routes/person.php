<?php

/**
 * @OA\Get(path="/admin/persons", tags={"x-admin", "persons"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(type="integer", in="query", name="offset", default=0, description="Offset for pagination"),
 *     @OA\Parameter(type="integer", in="query", name="limit", default=25, description="Limit for pagination"),
 *     @OA\Parameter(type="string", in="query", name="search", description="Search string for account. Case insensetive."),
 *     @OA\Parameter(type="string", in="query", name="order", default="-id", description="Sorting : '-column_name' ascending order, '+column_name' descending order"),
 *     @OA\Response(response="200", description="List persons")
 * )
 */
Flight::route('GET /admin/persons', function () {
    $offset = Flight::query('offset', 0);
    $limit = Flight::query('limit', 10);
    $search = Flight::query('search');
    $order = Flight::query('order', '-id');

    Flight::json(Flight::personService()->get_all_persons("USER", $offset, $limit, $search, $order));
});

/**
 * @OA\Get(path="/person/profile", tags={"x-person", "person"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Response(response="200", description="Fetch individual person")
 * )
 */
Flight::route('GET /person/profile', function () {
    $account_id = Flight::get('person')['aid'];
    $profile = Flight::personService()->get_person_by_account_id($account_id);
    $array = array();
    array_push($array, $profile);
    Flight::json($array);
});

/**
 * @OA\Get(path="/person/communication", tags={"communication"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(type="integer", in="query", name="offset", default=0, description="Offset for pagination"),
 *     @OA\Parameter(type="integer", in="query", name="limit", default=25, description="Limit for pagination"),
 *     @OA\Response(response="200", description="List of communications")
 * )
 */
Flight::route('GET /person/communication', function () {
    $account_id = Flight::get('person')['aid'];
    $offset = Flight::query('offset', 0);
    $limit = Flight::query('limit', 10);
    Flight::json(Flight::communicationService()->get_all($account_id, $offset, $limit));
});

/**
 * @OA\Get(path="/person/receiver/{id}", tags={"receiver"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(type="integer", in="path", name="id", default=1, description="Id of receiver"),
 *     @OA\Response(response="200", description="Get receiver by id")
 * )
 */
Flight::route('GET /person/receiver/@id', function ($id) {
    Flight::json(Flight::receiverService()->get_receiver_email_with_id($id));
});

/**
 * @OA\Post( path="/register", tags={"login"},
 *    @OA\RequestBody(description="Person info", required=true,
 *        @OA\MediaType(
 *            mediaType="application/json",
 *            @OA\Schema(
 *                @OA\Property(property="account", required="true", type="string", example="suleyman_oner", description="Name of account"),
 *                @OA\Property(property="name", required="true", type="string", example="Suleyman", description="Name of person"),
 *                @OA\Property(property="surname", required="true", type="string", example="Oner", description="Surname of person"),
 *                @OA\Property(property="email", required="true", type="string", example="suleyman_oner@galp.com", description="Email of person"),
 *                @OA\Property(property="password", required="true", type="string", example="12345", description="Password")
 *            )
 *        )
 * ),
 *    @OA\Response(response="200", description="Person has been created.")
 * )
 */
Flight::route('POST /register', function () {
    Flight::personService()->register(Flight::request()->data->getData());
    Flight::json(["message" => "Confirmation mail has been sent. Please check your email."]);
});

/**
 * @OA\Post( path="/confirm", tags={"login"},
 *     @OA\RequestBody(description="Token", required=true,
 *         @OA\MediaType(
 *            mediaType="application/json",
 *            @OA\Schema(
 *                @OA\Property(property="token", required="true", type="string", example="token", description="Token for confirmation."),
 *            )
 *        )
 * ),
 *     @OA\Response(response="200", description="Successfull activation.")
 * )
 */
Flight::route('POST /confirm', function () {
    $token = Flight::request()->data->token;
    Flight::json(Flight::jwt(Flight::personService()->confirm($token)));
});

/**
 * @OA\Post( path="/login", tags={"login"},
 *    @OA\RequestBody(description="Person info", required=true,
 *        @OA\MediaType(
 *            mediaType="application/json",
 *            @OA\Schema(
 *                @OA\Property(property="email", required="true", type="string", example="suleyman_oner@galp.com", description="Email of person"),
 *                @OA\Property(property="password", required="true", type="string", example="12345", description="Password")
 *            )
 *        )
 * ),
 *    @OA\Response(response="200", description="Person has been logged.")
 * )
 */
Flight::route('POST /login', function () {
    Flight::json(Flight::jwt(Flight::personService()->login(Flight::request()->data->getData())));
});

/**
 * @OA\Post( path="/forgot", tags={"login"}, description="Send recovery email",
 *    @OA\RequestBody(description="Person info", required=true,
 *        @OA\MediaType(
 *            mediaType="application/json",
 *            @OA\Schema(
 *                @OA\Property(property="email", required="true", type="string", example="suleyman_oner@galp.com", description="Email of person")
 *            )
 *        )
 * ),
 *    @OA\Response(response="200", description="Recovery mail has been sent.")
 * )
 */
Flight::route('POST /forgot', function () {
    Flight::personService()->forgot(Flight::request()->data->getData());
    Flight::json(["message" => "Recovery token has been sent to your email."]);
});

/**
 * @OA\Post( path="/reset", tags={"login"}, description="Reset password using recovery token",
 *    @OA\RequestBody(description="Person info", required=true,
 *        @OA\MediaType(
 *            mediaType="application/json",
 *            @OA\Schema(
 *                @OA\Property(property="token", required="true", type="string", example="123", description="Recovery token"),
 *                @OA\Property(property="password", required="true", type="string", example="12345", description="New password")
 *            )
 *        )
 * ),
 *    @OA\Response(response="200", description="Person has changed password.")
 * )
 */
Flight::route('POST /reset', function () {
    Flight::json(Flight::jwt(Flight::personService()->reset(Flight::request()->data->getData())));
});
