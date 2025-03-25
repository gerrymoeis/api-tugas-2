<?php
namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function create(AddressRequest $request, $contactId): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where('id', $contactId)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response(["errors" => ["message" => "Contact not found"]], 404));
        }

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get($contactId, $addressId): AddressResource
    {
        $user = Auth::user();
        $contact = Contact::where('id', $contactId)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response(["errors" => ["message" => "Contact not found"]], 404));
        }

        $address = Address::where('id', $addressId)->where('contact_id', $contact->id)->first();
        if (!$address) {
            throw new HttpResponseException(response(["errors" => ["message" => "Address not found"]], 404));
        }

        return new AddressResource($address);
    }

    public function update(AddressRequest $request, $contactId, $addressId): AddressResource
    {
        $user = Auth::user();
        $contact = Contact::where('id', $contactId)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response(["errors" => ["message" => "Contact not found"]], 404));
        }

        $address = Address::where('id', $addressId)->where('contact_id', $contact->id)->first();
        if (!$address) {
            throw new HttpResponseException(response(["errors" => ["message" => "Address not found"]], 404));
        }

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete($contactId, $addressId): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where('id', $contactId)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response(["errors" => ["message" => "Contact not found"]], 404));
        }

        $address = Address::where('id', $addressId)->where('contact_id', $contact->id)->first();
        if (!$address) {
            throw new HttpResponseException(response(["errors" => ["message" => "Address not found"]], 404));
        }

        $address->delete();
        return response()->json(["data" => true]);
    }
}