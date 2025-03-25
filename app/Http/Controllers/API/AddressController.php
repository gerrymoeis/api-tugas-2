<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Addresses",
 *     description="API Endpoints for managing contact addresses"
 * )
 */
class AddressController extends Controller
{
    private function validateContact(Request $request, $contactId)
    {
        $contact = Contact::where('id', $contactId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$contact) {
            throw new HttpResponseException(response([
                "errors" => ["message" => "Contact not found"]
            ], 404));
        }

        return $contact;
    }

    /**
     * @OA\Get(
     *     path="/api/contacts/{contact_id}/addresses",
     *     summary="Get all addresses for a specific contact",
     *     tags={"Addresses"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="contact_id",
     *         in="path",
     *         required=true,
     *         description="Contact ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of addresses",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="street", type="string", example="123 Main St"),
     *                     @OA\Property(property="city", type="string", example="New York"),
     *                     @OA\Property(property="province", type="string", example="NY"),
     *                     @OA\Property(property="postal_code", type="string", example="10001"),
     *                     @OA\Property(property="country", type="string", example="USA")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found"
     *     )
     * )
     */
    public function index(Request $request, $contactId)
    {
        $this->validateContact($request, $contactId);

        $addresses = Address::where('contact_id', $contactId)
            ->orderBy('id')
            ->get()
            ->map(function ($address) {
                return [
                    'id' => $address->id,
                    'street' => $address->street,
                    'city' => $address->city,
                    'province' => $address->province,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country
                ];
            });

        return response()->json([
            'data' => $addresses
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/contacts/{contact_id}/addresses",
     *     summary="Create a new address for a contact",
     *     tags={"Addresses"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="contact_id",
     *         in="path",
     *         required=true,
     *         description="Contact ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"country"},
     *             @OA\Property(property="street", type="string", example="123 Main St"),
     *             @OA\Property(property="city", type="string", example="New York"),
     *             @OA\Property(property="province", type="string", example="NY"),
     *             @OA\Property(property="postal_code", type="string", example="10001"),
     *             @OA\Property(property="country", type="string", example="USA")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Address created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="street", type="string", example="123 Main St"),
     *                 @OA\Property(property="city", type="string", example="New York"),
     *                 @OA\Property(property="province", type="string", example="NY"),
     *                 @OA\Property(property="postal_code", type="string", example="10001"),
     *                 @OA\Property(property="country", type="string", example="USA")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request, $contactId)
    {
        $this->validateContact($request, $contactId);

        $request->validate([
            'street' => 'nullable',
            'city' => 'nullable',
            'province' => 'nullable',
            'country' => 'required',
            'postal_code' => 'nullable'
        ]);

        $address = new Address([
            'contact_id' => $contactId,
            'street' => $request->street,
            'city' => $request->city,
            'province' => $request->province,
            'country' => $request->country,
            'postal_code' => $request->postal_code
        ]);
        $address->save();

        return response()->json([
            'data' => [
                'id' => $address->id,
                'street' => $address->street,
                'city' => $address->city,
                'province' => $address->province,
                'postal_code' => $address->postal_code,
                'country' => $address->country
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/contacts/{contact_id}/addresses/{id}",
     *     summary="Get a specific address",
     *     tags={"Addresses"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="contact_id",
     *         in="path",
     *         required=true,
     *         description="Contact ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="street", type="string", example="123 Main St"),
     *                 @OA\Property(property="city", type="string", example="New York"),
     *                 @OA\Property(property="province", type="string", example="NY"),
     *                 @OA\Property(property="postal_code", type="string", example="10001"),
     *                 @OA\Property(property="country", type="string", example="USA")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact or Address not found"
     *     )
     * )
     */
    public function show(Request $request, $contactId, $id)
    {
        $this->validateContact($request, $contactId);

        $address = Address::where('id', $id)
            ->where('contact_id', $contactId)
            ->first();

        if (!$address) {
            throw new HttpResponseException(response([
                "errors" => ["message" => "Address not found"]
            ], 404));
        }

        return response()->json([
            'data' => [
                'id' => $address->id,
                'street' => $address->street,
                'city' => $address->city,
                'province' => $address->province,
                'postal_code' => $address->postal_code,
                'country' => $address->country
            ]
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/contacts/{contact_id}/addresses/{id}",
     *     summary="Update a specific address",
     *     tags={"Addresses"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="contact_id",
     *         in="path",
     *         required=true,
     *         description="Contact ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="street", type="string", example="123 Main St"),
     *             @OA\Property(property="city", type="string", example="New York"),
     *             @OA\Property(property="province", type="string", example="NY"),
     *             @OA\Property(property="postal_code", type="string", example="10001"),
     *             @OA\Property(property="country", type="string", example="USA")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="street", type="string", example="123 Main St"),
     *                 @OA\Property(property="city", type="string", example="New York"),
     *                 @OA\Property(property="province", type="string", example="NY"),
     *                 @OA\Property(property="postal_code", type="string", example="10001"),
     *                 @OA\Property(property="country", type="string", example="USA")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact or Address not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $contactId, $id)
    {
        $this->validateContact($request, $contactId);

        $address = Address::where('id', $id)
            ->where('contact_id', $contactId)
            ->first();

        if (!$address) {
            throw new HttpResponseException(response([
                "errors" => ["message" => "Address not found"]
            ], 404));
        }

        $request->validate([
            'street' => 'sometimes|required',
            'city' => 'sometimes|required',
            'province' => 'nullable',
            'country' => 'sometimes|required',
            'postal_code' => 'nullable'
        ]);

        if ($request->has('street')) $address->street = $request->street;
        if ($request->has('city')) $address->city = $request->city;
        if ($request->has('province')) $address->province = $request->province;
        if ($request->has('country')) $address->country = $request->country;
        if ($request->has('postal_code')) $address->postal_code = $request->postal_code;

        $address->save();

        return response()->json([
            'data' => [
                'id' => $address->id,
                'street' => $address->street,
                'city' => $address->city,
                'province' => $address->province,
                'postal_code' => $address->postal_code,
                'country' => $address->country
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/contacts/{contact_id}/addresses/{id}",
     *     summary="Delete a specific address",
     *     tags={"Addresses"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="contact_id",
     *         in="path",
     *         required=true,
     *         description="Contact ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Address deleted successfully")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact or Address not found"
     *     )
     * )
     */
    public function destroy(Request $request, $contactId, $id)
    {
        $this->validateContact($request, $contactId);

        $address = Address::where('id', $id)
            ->where('contact_id', $contactId)
            ->first();

        if (!$address) {
            throw new HttpResponseException(response([
                "errors" => ["message" => "Address not found"]
            ], 404));
        }

        $address->delete();

        return response()->json([
            'data' => [
                'message' => 'Address deleted successfully'
            ]
        ]);
    }
}
