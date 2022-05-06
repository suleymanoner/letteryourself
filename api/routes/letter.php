<?php

/**
 * @OA\Get(path="/person/letter", tags={"x-person", "letter"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(type="integer", in="query", name="offset", default=0, description="Offset for pagination"),
 *     @OA\Parameter(type="integer", in="query", name="limit", default=25, description="Limit for pagination"),
 *     @OA\Parameter(type="string", in="query", name="search", description="Search string for account. Case insensetive."),
 *     @OA\Parameter(type="string", in="query", name="order", default="-id", description="Sorting : '-column_name' ascending order, '+column_name' descending order"),
 *     @OA\Response(response="200", description="List letters")
 * )
 */
Flight::route('GET /person/letter', function () {
    $account_id = Flight::get('person')['aid'];;
    $offset = Flight::query('offset', 0);
    $limit = Flight::query('limit', 10);
    $search = Flight::query('search');
    $order = Flight::query('order', '-id');
    Flight::json(Flight::letterService()->get_letter($account_id, $offset, $limit, $search, $order));
});

/**
 * @OA\Get(path="/person/letter/receiver/{id}", tags={"x-person", "letter"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(type="integer", in="path", name="id", default=1, description="Id of letter"),
 *     @OA\Response(response="200", description="Fetch receiver id")
 * )
 */
Flight::route('GET /person/letter/receiver/@id', function ($letter_id) {
    $receiver_id_array = Flight::communicationService()->get_receiver_id_by_letter_id($letter_id);
    $receiver_id = $receiver_id_array['receiver_id'];
    Flight::json(Flight::receiverService()->get_receiver_email_with_id($receiver_id));
});

/**
 * @OA\Get(path="/person/letter/{id}", tags={"x-person", "letter"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(type="integer", in="path", name="id", default=1, description="Id of letter"),
 *     @OA\Response(response="200", description="Fetch individual letter")
 * )
 */
Flight::route('GET /person/letter/@id', function ($letter_id) {
    $account_id = Flight::get('person')['aid'];
    Flight::json(Flight::letterService()->get_letter_with_account_and_letter_id($account_id, $letter_id));
});

/**
 * @OA\Post(path="/person/letter", tags={"x-person", "letter"}, security={{"ApiKeyAuth": {}}},
 *   @OA\RequestBody(description="Basic letter info", required=true,
 *       @OA\MediaType(mediaType="application/json",
 *    			@OA\Schema(
 *    				 @OA\Property(property="title", required="true", type="string", example="My Letter",	description="Title of the letter" ),
 *    				 @OA\Property(property="body", required="true", type="string", example="My Dear friend..",	description="Body of the letter" ),
 *             @OA\Property(property="send_at", required="true", type="DATE_FORMAT", example="2021-03-31 22:15:00",	description="Send date of your letter" ),
 *             @OA\Property(property="receiver_email", required="true", type="string", example="ahmet@galp.com",	description="Receiver email for letter." )
 *          )
 *       )
 *     ),
 *  @OA\Response(response="200", description="Saved letter info.")
 * )
 */
Flight::route('POST /person/letter', function () {
    $account_id = Flight::get('person')['aid'];

    $receiver_email = Flight::request()->data->getData()['receiver_email'];
    Flight::receiverService()->add_receiver($receiver_email);
    $receiver_id = Flight::receiverService()->get_receiver_id_by_email($receiver_email);

    Flight::json(Flight::letterService()->add_letter($account_id, Flight::request()->data->getData()));

    $letter_id = Flight::letterService()->get_letter_id_by_title(Flight::request()->data->getData()['title']);
    Flight::communicationService()->add_communication($letter_id, $receiver_id, $account_id);
});

/**
 * @OA\Put(path="/person/letter/{id}", tags={"x-person", "letter"}, security={{"ApiKeyAuth": {}}},
 *   @OA\Parameter(type="integer", in="path", name="id", default=1),
 *   @OA\RequestBody(description="Basic letter info that is going to be updated", required=true,
 *       @OA\MediaType(mediaType="application/json",
 *    			@OA\Schema(
 *    				 @OA\Property(property="title", required="true", type="string", example="My Letter",	description="Title of the letter" ),
 *    				 @OA\Property(property="body", required="true", type="string", example="My Dear friend..",	description="Body of the letter" ),
 *             @OA\Property(property="send_at", required="true", type="DATE_FORMAT", example="2021-03-31 22:15:00",	description="Send date of your letter" ),
 *             @OA\Property(property="receiver_email", required="true", type="string", example="hello@galp.com",	description="Receiver email of letter" ),
 *          )
 *       )
 *     ),
 *     @OA\Response(response="200", description="Update letter")
 * )
 */
Flight::route('PUT /person/letter/@id', function ($id) {
    $receiver_id_array = Flight::communicationService()->get_receiver_id_by_letter_id($id);
    $receiver_id = $receiver_id_array['receiver_id'];

    Flight::receiverService()->update_receiver_email(Flight::request()->data['receiver_email'],  $receiver_id);
    Flight::letterService()->update_letter_new($id, Flight::request()->data['title'], Flight::request()->data['body'], Flight::request()->data['send_at']);
});

/**
 * @OA\Get(path="/admin/letter", tags={"x-admin", "letter"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(type="integer", in="query", name="account_id", default=0, description="Account id"),
 *     @OA\Parameter(type="integer", in="query", name="offset", default=0, description="Offset for pagination"),
 *     @OA\Parameter(type="integer", in="query", name="limit", default=25, description="Limit for pagination"),
 *     @OA\Parameter(type="string", in="query", name="search", description="Search string for account. Case insensetive."),
 *     @OA\Parameter(type="string", in="query", name="order", default="-id", description="Sorting : '-column_name' ascending order, '+column_name' descending order"),
 *     @OA\Response(response="200", description="List letters")
 * )
 */
Flight::route('GET /admin/letter', function () {
    $account_id = Flight::query('account_id');
    $offset = Flight::query('offset', 0);
    $limit = Flight::query('limit', 10);
    $search = Flight::query('search');
    $order = Flight::query('order', '-id');
    Flight::json(Flight::letterService()->get_letter($account_id, $offset, $limit, $search, $order));
});

/**
 * @OA\Get(path="/admin/letter/{id}", tags={"x-admin", "letter"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(type="integer", in="path", name="id", default=1, description="Id of letter"),
 *     @OA\Response(response="200", description="Fetch individual letter.")
 * )
 */
Flight::route('GET /admin/letter/@id', function ($id) {
    Flight::json(Flight::letterService()->get_by_id($id));
});

/**
 * @OA\Post(path="/admin/letter", tags={"x-admin", "letter"}, security={{"ApiKeyAuth": {}}},
 *   @OA\RequestBody(description="Basic letter info", required=true,
 *       @OA\MediaType(mediaType="application/json",
 *    			@OA\Schema(
 *    				 @OA\Property(property="account_id", required="true", type="integer", example=1,	description="Id of account" ),
 *    				 @OA\Property(property="title", required="true", type="string", example="My Letter",	description="Title of the letter" ),
 *    				 @OA\Property(property="body", required="true", type="string", example="My Dear friend..",	description="Body of the letter" ),
 *             @OA\Property(property="receiver_email", required="true", type="string", example="ahmet@galp.com",	description="Receiver email for letter" ),
 *             @OA\Property(property="send_at", required="true", type="DATE_FORMAT", example="2021-03-31 22:15:00",	description="Send date of your letter" )
 *          )
 *       )
 *     ),
 *  @OA\Response(response="200", description="Saved letter info.")
 * )
 */
Flight::route('POST /admin/letter', function () {
    $account_id = Flight::request()->data->getData()['account_id'];
    $receiver_email = Flight::request()->data->getData()['receiver_email'];
    Flight::receiverService()->add_receiver($receiver_email);
    $receiver_id = Flight::receiverService()->get_receiver_id_by_email($receiver_email);

    Flight::json(Flight::letterService()->add_letter($account_id, Flight::request()->data->getData()));

    $letter_id = Flight::letterService()->get_letter_id_by_account_id($account_id);
    Flight::communicationService()->add_communication($letter_id, $receiver_id);
});

/**
 * @OA\Put(path="/admin/letter/{id}", tags={"x-admin", "letter"}, security={{"ApiKeyAuth": {}}},
 *   @OA\Parameter(type="integer", in="path", name="id", default=1, description="Id of letter"),
 *   @OA\RequestBody(description="Basic letter info that is going to be updated", required=true,
 *       @OA\MediaType(mediaType="application/json",
 *    			@OA\Schema(
 *    				 @OA\Property(property="title", required="true", type="string", example="My Letter",	description="Title of the letter" ),
 *    				 @OA\Property(property="body", required="true", type="string", example="My Dear friend..",	description="Body of the letter" ),
 *             @OA\Property(property="send_at", required="true", type="DATE_FORMAT", example="2021-03-31 22:15:00",	description="Send date of your letter" )
 *          )
 *       )
 *     ),
 *     @OA\Response(response="200", description="Updated letter")
 * )
 */
Flight::route('PUT /admin/letter/@id', function ($id) {
    Flight::json(Flight::letterService()->update($id, Flight::request()->data->getData()));
});

/**
 * @OA\Get(path="/person/how_many_letter", tags={"x-person", "letter"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Response(response="200", description="Fetch individual letter.")
 * )
 */
Flight::route('GET /person/how_many_letter', function () {
    $account_id = Flight::get('person')['aid'];
    Flight::json(Flight::letterService()->how_many_letters($account_id));
});
